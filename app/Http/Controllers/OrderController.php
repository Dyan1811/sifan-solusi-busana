<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\Pelanggan;
use App\Models\DetailPesanan;
use App\Models\RiwayatStatusPesanan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function checkCapacity(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $newDuration = (int) $request->input('new_duration', 0);
        $userId = auth()->id();

        $existingMinutes = Pesanan::where('id_user', $userId)
            ->whereDate('tenggat_pengambilan', $date)
            ->whereIn('status_pesanan', ['antrean', 'diproses', 'selesai'])
            ->with('details')->get()->sum(fn($o) => $o->details->sum('estimasi_waktu'));

        $maxCapacity = 480; // 8 jam
        $totalAfter = $existingMinutes + $newDuration;
        $isAvailable = $totalAfter <= $maxCapacity;

        return response()->json([
            'existing_minutes' => $existingMinutes,
            'new_duration' => $newDuration,
            'total_after' => $totalAfter,
            'remaining_capacity' => max(0, $maxCapacity - $existingMinutes),
            'max_capacity' => $maxCapacity,
            'is_available' => $isAvailable
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string',
            'no_hp' => 'required|string',
            'tenggat_pengambilan' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.jenis_pakaian' => 'required|string',
            'items.*.tingkat_kesulitan' => 'required|in:ringan,sedang,rumit',
            'items.*.foto' => 'nullable|image|max:5120'
        ]);

        $durationMap = ['ringan' => 15, 'sedang' => 30, 'rumit' => 60];
        $totalDuration = 0;
        foreach ($request->items as $item) {
            $totalDuration += $durationMap[$item['tingkat_kesulitan']];
        }

        // Cek Kuota Pengerjaan Harian (Batas 8 Jam / 480 Menit)
        $targetDate = Carbon::parse($request->tenggat_pengambilan)->toDateString();
        $existingMinutes = Pesanan::where('id_user', auth()->id())
            ->whereDate('tenggat_pengambilan', $targetDate)
            ->whereIn('status_pesanan', ['antrean', 'diproses', 'selesai'])
            ->with('details')->get()->sum(fn($o) => $o->details->sum('estimasi_waktu'));

        if (($existingMinutes + $totalDuration) > 480) {
            return back()->with('error', 'Kapasitas pengerjaan harian melebihi batas 8 jam (480 menit)! Silakan sesuaikan jadwal tenggat.');
        }

        DB::transaction(function () use ($request, $totalDuration, $durationMap) {
            $pelanggan = Pelanggan::firstOrCreate(
                ['no_hp' => $request->no_hp, 'id_user' => auth()->id()],
                ['nama_pelanggan' => $request->nama_pelanggan]
            );

            // Ambil nomor pesanan terakhir yang ada di database untuk menghitung urutan baru
            $lastOrder = Pesanan::where('nomor_pesanan', 'like', 'SB-%')
                ->orderBy('id_pesanan', 'desc')
                ->first();

            if ($lastOrder) {
                // Ambil angka dari nomor pesanan terakhir (misal: "SB-1041" -> 1041)
                $lastNum = (int) str_replace('SB-', '', $lastOrder->nomor_pesanan);
                $nextNum = $lastNum + 1;
            } else {
                $nextNum = 1041; // Angka awal jika database masih kosong
            }

            $nomorPesanan = 'SB-' . $nextNum;

            // Buat pesanan baru dengan nomor yang dijamin unik
            $pesanan = Pesanan::create([
                'nomor_pesanan' => $nomorPesanan,
                'id_user' => auth()->id(),
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'tanggal_pesanan' => Carbon::now(),
                'tenggat_pengambilan' => $request->tenggat_pengambilan,
                'status_pesanan' => 'antrean',
                'jumlah_pakaian' => count($request->items)
            ]);

            foreach ($request->items as $item) {
                $imagePath = null;
                if (isset($item['foto'])) {
                    $imagePath = $item['foto']->store('garments', 'public');
                }

                DetailPesanan::create([
                    'id_pesanan' => $pesanan->id_pesanan,
                    'jenis_pakaian' => $item['jenis_pakaian'],
                    'catatan' => $item['catatan'] ?? null,
                    'tingkat_kesulitan' => $item['tingkat_kesulitan'],
                    'image_path' => $imagePath,
                    'estimasi_waktu' => $durationMap[$item['tingkat_kesulitan']],
                    'status_pakaian' => 'belum'
                ]);
            }

            RiwayatStatusPesanan::create([
                'id_pesanan' => $pesanan->id_pesanan,
                'status_sebelumnya' => null,
                'status_baru' => 'antrean',
                'diubah_oleh' => auth()->id()
            ]);
        });

        return redirect()->route('kanban.index')->with('success', 'Pesanan baru berhasil ditambahkan.');
    }
}