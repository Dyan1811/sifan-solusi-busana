<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\RiwayatStatusPesanan;
use Carbon\Carbon;

class KanbanController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        // Gunakan zona waktu WIB (Asia/Jakarta) dan Bahasa Indonesia
        $nowWib = Carbon::now('Asia/Jakarta')->locale('id');
        $today = $nowWib->toDateString();
        $tanggalHariIni = $nowWib->translatedFormat('l, d F Y');
        $search = $request->input('search');

        $query = Pesanan::where('id_user', $userId)->with(['pelanggan', 'details']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_pesanan', 'like', "%{$search}%")
                  ->orWhereHas('pelanggan', fn($p) => $p->where('nama_pelanggan', 'like', "%{$search}%"));
            });
        }

        $allOrders = $query->get();

        $antrean = $allOrders->where('status_pesanan', 'antrean');
        $diproses = $allOrders->where('status_pesanan', 'diproses');
        $selesai = $allOrders->where('status_pesanan', 'selesai');
        $siapDiambil = $allOrders->where('status_pesanan', 'siap_diambil');

        // Hitung kapasitas harian: pesanan yang tenggatnya hari ini ATAU baru masuk hari ini
        $todayAllocatedMinutes = Pesanan::where('id_user', $userId)
            ->where(function ($q) use ($today) {
                $q->whereDate('tenggat_pengambilan', $today)
                  ->orWhereDate('tanggal_pesanan', $today);
            })
            ->whereIn('status_pesanan', ['antrean', 'diproses', 'selesai'])
            ->with('details')
            ->get()
            ->sum(fn($o) => $o->details->sum('estimasi_waktu'));

        return view('kanban.index', compact(
            'antrean',
            'diproses',
            'selesai',
            'siapDiambil',
            'todayAllocatedMinutes',
            'tanggalHariIni'
        ));
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $pesanan = Pesanan::where('id_user', auth()->id())->findOrFail($id);
        $oldStatus = $pesanan->status_pesanan;
        $pesanan->status_pesanan = $request->status;
        $pesanan->save();

        RiwayatStatusPesanan::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'status_sebelumnya' => $oldStatus,
            'status_baru' => $request->status,
            'diubah_oleh' => auth()->id()
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function updateItemStatus(Request $request, $idDetail)
    {
        $detail = DetailPesanan::findOrFail($idDetail);
        $detail->status_pakaian = $request->status;
        $detail->save();

        // Ambil data pesanan utama beserta detailnya
        $pesanan = Pesanan::with(['details', 'pelanggan'])->findOrFail($detail->id_pesanan);
        $totalItems = $pesanan->details->count();
        $completedItems = $pesanan->details->where('status_pakaian', 'selesai')->count();

        // Jika semua item pakaian selesai, ubah status pesanan menjadi 'selesai'
        if ($totalItems === $completedItems && $pesanan->status_pesanan === 'diproses') {
            $pesanan->status_pesanan = 'selesai';
            $pesanan->save();

            RiwayatStatusPesanan::create([
                'id_pesanan' => $pesanan->id_pesanan,
                'status_sebelumnya' => 'diproses',
                'status_baru' => 'selesai',
                'diubah_oleh' => auth()->id()
            ]);
        } elseif ($completedItems < $totalItems && $pesanan->status_pesanan === 'selesai') {
            $pesanan->status_pesanan = 'diproses';
            $pesanan->save();

            RiwayatStatusPesanan::create([
                'id_pesanan' => $pesanan->id_pesanan,
                'status_sebelumnya' => 'selesai',
                'status_baru' => 'diproses',
                'diubah_oleh' => auth()->id()
            ]);
        }

        return response()->json([
            'success' => true,
            'completed_items' => $completedItems,
            'total_items' => $totalItems,
            'order_status' => $pesanan->status_pesanan,
            'order_id' => $pesanan->id_pesanan,
            'nomor_pesanan' => $pesanan->nomor_pesanan,
            'nama_pelanggan' => $pesanan->pelanggan->nama_pelanggan,
            'total_estimasi' => $pesanan->total_estimasi
        ]);
    }

    public function sendWaAndSetReady($id)
    {
        $pesanan = Pesanan::where('id_user', auth()->id())->with(['pelanggan', 'details'])->findOrFail($id);
        $oldStatus = $pesanan->status_pesanan;
        $pesanan->status_pesanan = 'siap_diambil';
        $pesanan->save();

        RiwayatStatusPesanan::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'status_sebelumnya' => $oldStatus,
            'status_baru' => 'siap_diambil',
            'diubah_oleh' => auth()->id()
        ]);

        $phone = preg_replace('/[^0-9]/', '', $pesanan->pelanggan->no_hp);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $msg = "Halo Kak {$pesanan->pelanggan->nama_pelanggan}, pesanan jahit/permak Anda pada Solusi Busana (No. {$pesanan->nomor_pesanan}) telah SELESAI dan SIAP DIGUNAKAN / DIAMBIL. Terima kasih!";
        $waUrl = "https://wa.me/{$phone}?text=" . rawurlencode($msg);

        return response()->json(['wa_url' => $waUrl]);
    }
}