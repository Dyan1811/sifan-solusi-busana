<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\RiwayatStatusPesanan;
use Carbon\Carbon;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        $search = $request->input('search');
        $month = $request->input('month', Carbon::now()->format('Y-m'));

        $query = Pesanan::where('id_user', $userId)
            ->where('status_pesanan', 'sudah_diambil')
            ->with(['pelanggan', 'details']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nomor_pesanan', 'like', "%{$search}%")
                  ->orWhereHas('pelanggan', fn($p) => $p->where('nama_pelanggan', 'like', "%{$search}%"));
            });
        }

        if ($month) {
            $query->whereRaw("DATE_FORMAT(tenggat_pengambilan, '%Y-%m') = ?", [$month]);
        }

        $historyOrders = $query->latest('updated_at')->paginate(10);

        // Statistik Bulanan
        $monthlyOrders = Pesanan::where('id_user', $userId)
            ->where('status_pesanan', 'sudah_diambil')
            ->whereRaw("DATE_FORMAT(tenggat_pengambilan, '%Y-%m') = ?", [$month])
            ->with('details')->get();

        $totalCompletedOrders = $monthlyOrders->count();
        $totalGarmentsFinished = $monthlyOrders->sum('jumlah_pakaian');
        $totalSewingMinutes = $monthlyOrders->sum(fn($o) => $o->details->sum('estimasi_waktu'));

        return view('history.index', compact(
            'historyOrders', 'totalCompletedOrders', 
            'totalGarmentsFinished', 'totalSewingMinutes', 'month'
        ));
    }

    public function archive($id)
    {
        $pesanan = Pesanan::where('id_user', auth()->id())->findOrFail($id);
        $oldStatus = $pesanan->status_pesanan;
        $pesanan->status_pesanan = 'sudah_diambil';
        $pesanan->save();

        RiwayatStatusPesanan::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'status_sebelumnya' => $oldStatus,
            'status_baru' => 'sudah_diambil',
            'diubah_oleh' => auth()->id()
        ]);

        return redirect()->route('kanban.index')->with('success', 'Pesanan telah diselesaikan dan masuk ke Riwayat Orderan.');
    }
}