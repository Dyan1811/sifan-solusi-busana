@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <span class="text-success small fw-bold">SOLUSI BUSANA</span>
        <h3 class="fw-bold mb-0">Riwayat Orderan</h3>
    </div>
</div>

<!-- 3 Kartu Statistik Ringkasan -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="bg-white p-3 rounded-3 border shadow-sm">
            <span class="text-muted small">Pesanan diambil bulan ini</span>
            <h2 class="fw-bold text-success mb-0">{{ $totalCompletedOrders }} <small class="fs-6 text-muted">pesanan</small></h2>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="bg-white p-3 rounded-3 border shadow-sm">
            <span class="text-muted small">Pakaian terselesaikan</span>
            <h2 class="fw-bold text-primary mb-0">{{ $totalGarmentsFinished }} <small class="fs-6 text-muted">pakaian</small></h2>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="bg-white p-3 rounded-3 border shadow-sm">
            <span class="text-muted small">Waktu jahit tercatat</span>
            <h2 class="fw-bold text-dark mb-0">{{ floor($totalSewingMinutes / 60) }}j {{ $totalSewingMinutes % 60 }}m</h2>
        </div>
    </div>
</div>

<!-- Filter & Tabel Data -->
<div class="bg-white p-3 rounded-3 border shadow-sm">
    <form action="{{ route('history.index') }}" method="GET" class="row g-2 mb-3">
        <div class="col-12 col-md-6">
            <input type="text" name="search" class="form-control" placeholder="Cari nama pelanggan atau nomor pesanan..." value="{{ request('search') }}">
        </div>
        <div class="col-12 col-md-4">
            <input type="month" name="month" class="form-control" value="{{ $month }}">
        </div>
        <div class="col-12 col-md-2">
            <button class="btn btn-success w-100">Filter</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>PESANAN</th>
                    <th>TENGGAT AMBIL</th>
                    <th>DIAMBIL PADA</th>
                    <th>DURASI JAHIT</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($historyOrders as $order)
                    <tr>
                        <td>
                            <strong>{{ $order->pelanggan->nama_pelanggan }}</strong>
                            <small class="text-muted d-block">{{ $order->nomor_pesanan }} • {{ $order->jumlah_pakaian }} pakaian</small>
                        </td>
                        <td>{{ $order->tenggat_pengambilan->format('d M Y, H.i') }}</td>
                        <td>{{ $order->updated_at->format('d M Y, H.i') }}</td>
                        <td>{{ $order->total_estimasi }} menit</td>
                        <td><span class="badge bg-success">Sudah diambil</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Belum ada riwayat transaksi yang tersimpan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $historyOrders->links() }}
    </div>
</div>
@endsection