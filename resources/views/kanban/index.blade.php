@extends('layouts.app')

@section('content')
<!-- Header Judul (Sesuai Proposal Mobile) -->
<div class="mb-3">
    <span class="text-success small fw-bold tracking-wide">SOLUSI BUSANA</span>
    <h3 class="fw-bold mb-0">Papan Antrean</h3>
</div>

<!-- Card Informasi Tanggal (Mobile & Desktop) -->
<div class="bg-success-subtle p-3 rounded-3 mb-3 border border-success-subtle">
    <div class="d-flex align-items-center text-success fw-semibold mb-1">
        <i class="bi bi-calendar-event me-2"></i> {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}
    </div>
    <small class="text-muted d-block" style="font-size: 0.8rem;">
        {{ $antrean->count() + $diproses->count() + $selesai->count() }} pesanan aktif &bull; {{ $antrean->count() }} tenggat hari ini
    </small>
</div>

<!-- Widget Kapasitas Kerja Harian (Maksimal 8 Jam / 480 Menit) -->
<div class="bg-white p-3 rounded-3 border shadow-sm mb-3">
    <div class="d-flex justify-content-between align-items-center mb-1">
        <span class="small fw-bold">Kapasitas Hari Ini</span>
        <span class="badge bg-success fs-6">{{ floor($todayAllocatedMinutes / 60) }}j {{ $todayAllocatedMinutes % 60 }}m / 8 jam</span>
    </div>
    <div class="progress" style="height: 8px;">
        <div class="progress-bar bg-success" role="progressbar" style="width: {{ min(100, ($todayAllocatedMinutes / 480) * 100) }}%"></div>
    </div>
    <div class="d-flex justify-content-between mt-1 text-muted" style="font-size:0.75rem;">
        <span>{{ $todayAllocatedMinutes }} dari 480 menit</span>
        <span>&bull; tersisa {{ max(0, 480 - $todayAllocatedMinutes) }} menit</span>
    </div>
</div>

<!-- Tombol Tambah Pesanan Baru (Khusus Tampilan Mobile) -->
<div class="d-md-none mb-3">
    <button type="button" class="btn btn-brand w-100 py-2.5 rounded-3 fw-semibold d-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#modalAddOrder">
        <i class="bi bi-plus-lg me-2 fs-5"></i> Tambah pesanan baru
    </button>
</div>

<!-- Tab Status Bersampingan (Hanya Muncul di Tampilan Mobile) -->
<div class="d-md-none mb-3">
    <div class="mobile-status-tabs">
        <button type="button" class="mobile-status-tab active" data-target="antrean">
            Antrean<br><span class="badge rounded-pill bg-white text-dark mt-1" id="m-badge-antrean">{{ $antrean->count() }}</span>
        </button>
        <button type="button" class="mobile-status-tab" data-target="diproses">
            Diproses<br><span class="badge rounded-pill bg-white text-dark mt-1" id="m-badge-diproses">{{ $diproses->count() }}</span>
        </button>
        <button type="button" class="mobile-status-tab" data-target="selesai">
            Selesai<br><span class="badge rounded-pill bg-white text-dark mt-1" id="m-badge-selesai">{{ $selesai->count() }}</span>
        </button>
        <button type="button" class="mobile-status-tab" data-target="siap-diambil">
            Siap Diambil<br><span class="badge rounded-pill bg-white text-dark mt-1" id="m-badge-siap-diambil">{{ $siapDiambil->count() }}</span>
        </button>
    </div>
</div>

<!-- Input Pencarian Klien / Nota -->
<div class="mb-3">
    <form action="{{ route('kanban.index') }}" method="GET">
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama klien atau nomor pesanan" value="{{ request('search') }}">
        </div>
    </form>
</div>

<!-- Board Kanban Grid System (Responsive: Desktop 4 Kolom, Mobile Filter Tab) -->
<div class="row g-3">
    <!-- Kolom 1: Antrean -->
    <div class="col-12 col-md-3 mobile-col-pane" id="col-antrean">
        <div class="kanban-col">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-bold text-dark"><i class="bi bi-clock me-1"></i> Antrean</span>
                <span class="badge bg-secondary rounded-pill" id="badge-antrean">{{ $antrean->count() }}</span>
            </div>
            <div id="container-antrean">
                @foreach($antrean as $item)
                    <div class="card card-kanban p-3 mb-2" id="order-card-{{ $item->id_pesanan }}">
                        <div class="d-flex justify-content-between">
                            <small class="text-muted fw-bold">{{ $item->nomor_pesanan }}</small>
                            <span class="badge bg-light text-dark border">{{ $item->total_estimasi }}m</span>
                        </div>
                        <h6 class="fw-bold mb-1 mt-1">{{ $item->pelanggan->nama_pelanggan }}</h6>
                        <small class="text-muted d-block">{{ $item->details->pluck('jenis_pakaian')->implode(', ') }}</small>
                        <small class="text-muted d-block mt-1"><i class="bi bi-alarm me-1"></i> {{ $item->tenggat_pengambilan->format('H.i') }} WIB</small>
                        <form action="{{ route('orders.updateStatus', $item->id_pesanan) }}" method="POST" class="mt-3">
                            @csrf
                            <input type="hidden" name="status" value="diproses">
                            <button type="submit" class="btn btn-outline-success btn-sm w-100">Mulai jahit &rarr;</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Kolom 2: Diproses -->
    <div class="col-12 col-md-3 mobile-col-pane d-none d-md-block" id="col-diproses">
        <div class="kanban-col">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-bold text-dark"><i class="bi bi-scissors me-1"></i> Diproses</span>
                <span class="badge bg-warning text-dark rounded-pill" id="badge-diproses">{{ $diproses->count() }}</span>
            </div>
            <div id="container-diproses">
                @foreach($diproses as $item)
                    @php
                        $doneCount = $item->details->where('status_pakaian', 'selesai')->count();
                        $totalCount = $item->details->count();
                    @endphp
                    <div class="card card-kanban p-3 mb-2" id="order-card-{{ $item->id_pesanan }}">
                        <div class="d-flex justify-content-between">
                            <small class="text-muted fw-bold">{{ $item->nomor_pesanan }}</small>
                            <span class="badge bg-light text-dark border">{{ $item->total_estimasi }}m</span>
                        </div>
                        <h6 class="fw-bold mb-1 mt-1">{{ $item->pelanggan->nama_pelanggan }}</h6>
                        <small class="text-muted d-block" id="progress-text-{{ $item->id_pesanan }}">{{ $doneCount }} dari {{ $totalCount }} selesai</small>
                        <button class="btn btn-link btn-sm text-success p-0 mt-2 text-decoration-none text-start" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $item->id_pesanan }}">
                            Lihat detail &rarr;
                        </button>
                    </div>

                    <!-- Modal Detail Pengerjaan & Checklist -->
                    <div class="modal fade" id="modalDetail{{ $item->id_pesanan }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">Detail Pesanan ({{ $item->nomor_pesanan }})</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <h5>{{ $item->pelanggan->nama_pelanggan }}</h5>
                                    <p class="text-muted small mb-3"><i class="bi bi-whatsapp me-1"></i> {{ $item->pelanggan->no_hp }}</p>
                                    <hr>
                                    <h6 class="fw-bold">Daftar Jahitan:</h6>
                                    @foreach($item->details as $detail)
                                        <div class="p-2 border rounded mb-2 d-flex align-items-center justify-content-between">
                                            <div>
                                                <strong class="d-block">{{ $detail->jenis_pakaian }}</strong>
                                                <small class="text-muted">{{ $detail->catatan }} ({{ $detail->estimasi_waktu }}m)</small>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input item-checkbox" type="checkbox" data-id="{{ $detail->id_detail }}" {{ $detail->status_pakaian === 'selesai' ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Kolom 3: Selesai -->
    <div class="col-12 col-md-3 mobile-col-pane d-none d-md-block" id="col-selesai">
        <div class="kanban-col">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-bold text-dark"><i class="bi bi-check-circle me-1"></i> Selesai</span>
                <span class="badge bg-success rounded-pill" id="badge-selesai">{{ $selesai->count() }}</span>
            </div>
            <div id="container-selesai">
                @foreach($selesai as $item)
                    <div class="card card-kanban p-3 mb-2" id="order-card-{{ $item->id_pesanan }}">
                        <div class="d-flex justify-content-between">
                            <small class="text-muted fw-bold">{{ $item->nomor_pesanan }}</small>
                            <span class="badge bg-light text-dark border">{{ $item->total_estimasi }}m</span>
                        </div>
                        <h6 class="fw-bold mb-1 mt-1">{{ $item->pelanggan->nama_pelanggan }}</h6>
                        <button class="btn btn-success btn-sm w-100 mt-2 btn-send-wa" data-id="{{ $item->id_pesanan }}">
                            <i class="bi bi-whatsapp me-1"></i> Kirim WA & Siap Diambil
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Kolom 4: Siap Diambil -->
    <div class="col-12 col-md-3 mobile-col-pane d-none d-md-block" id="col-siap-diambil">
        <div class="kanban-col">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-bold text-dark"><i class="bi bi-bag-check me-1"></i> Siap Diambil</span>
                <span class="badge bg-info text-dark rounded-pill" id="badge-siap-diambil">{{ $siapDiambil->count() }}</span>
            </div>
            <div id="container-siap-diambil">
                @foreach($siapDiambil as $item)
                    <div class="card card-kanban p-3 mb-2" id="order-card-{{ $item->id_pesanan }}">
                        <div class="d-flex justify-content-between">
                            <small class="text-muted fw-bold">{{ $item->nomor_pesanan }}</small>
                        </div>
                        <h6 class="fw-bold mb-1 mt-1">{{ $item->pelanggan->nama_pelanggan }}</h6>
                        <form action="{{ route('orders.archive', $item->id_pesanan) }}" method="POST" class="mt-2">
                            @csrf
                            <button class="btn btn-outline-dark btn-sm w-100"><i class="bi bi-box-seam me-1"></i> Sudah diambil</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Pesanan Multi-Step Wizard (3 Tahap Sesuai Proposal) -->
<div class="modal fade" id="modalAddOrder" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data" id="formMultiStepOrder">
                @csrf
                <div class="modal-header border-bottom-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold">Tambah Pesanan Baru</h5>
                        <p class="text-muted small mb-0">Catat dengan rapi, jahit dengan tenang.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body pt-3">
                    <div class="step-indicator px-2">
                        <div class="step-item active" id="stepIndicator1">
                            <div class="step-badge">1</div> Data Klien
                        </div>
                        <div class="step-item" id="stepIndicator2">
                            <div class="step-badge">2</div> Detail Pakaian
                        </div>
                        <div class="step-item" id="stepIndicator3">
                            <div class="step-badge">3</div> Jadwal & Kuota
                        </div>
                    </div>

                    <!-- STEP 1: DATA KLIEN -->
                    <div id="stepSection1" class="step-section">
                        <h6 class="fw-bold mb-3">Kenali pesanan pelanggan Anda</h6>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Nama Klien <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pelanggan" id="inputNamaPelanggan" class="form-control" placeholder="Contoh: Fitri Handayani">
                            <div class="invalid-feedback">Nama Klien wajib diisi.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Nomor WhatsApp <span class="text-danger">*</span></label>
                            <input type="text" name="no_hp" id="inputNoHp" class="form-control" placeholder="Contoh: 081234567890">
                            <div class="invalid-feedback" id="msgNoHp">Nomor WhatsApp wajib diisi (minimal 10 angka).</div>
                            <small class="text-muted" style="font-size:0.75rem;">Nomor ini digunakan untuk mengabarkan pesanan siap diambil.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Jumlah Pakaian</label>
                            <div class="d-flex align-items-center gap-3">
                                <button type="button" class="btn btn-outline-secondary btn-sm px-3" id="btnDecrementItem">-</button>
                                <span class="fw-bold fs-5" id="displayItemCount">1 pakaian</span>
                                <button type="button" class="btn btn-outline-secondary btn-sm px-3" id="btnIncrementItem">+</button>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: DETAIL PAKAIAN & FOTO -->
                    <div id="stepSection2" class="step-section d-none">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0">Detail setiap jahitan</h6>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" id="badgeTotalDuration">Total estimasi: 30m</span>
                        </div>

                        <ul class="nav nav-tabs mb-3" id="garmentTabs" role="tablist"></ul>
                        <div class="tab-content" id="garmentTabContent"></div>

                        <div class="alert alert-success d-flex align-items-center mt-3 py-2 small mb-0" role="alert">
                            <i class="bi bi-check-circle me-2 fs-5"></i>
                            <span id="garmentSummaryText">Detail pakaian telah lengkap.</span>
                        </div>
                    </div>

                    <!-- STEP 3: JADWAL & VALIDASI KUOTA 8 JAM -->
                    <div id="stepSection3" class="step-section d-none">
                        <h6 class="fw-bold mb-3">Kapan pesanan akan diambil?</h6>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Tenggat Ambil (Tanggal & Jam) <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="tenggat_pengambilan" id="inputTenggatPengambilan" class="form-control" required>
                            <div class="invalid-feedback">Tanggal dan jam tenggat wajib diisi.</div>
                        </div>

                        <div class="card p-3 bg-light border-0 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-bold mb-0" id="summaryClientName">Fitri Handayani</h6>
                                    <small class="text-muted" id="summaryClientPhone">081234567890 &bull; <span id="summaryItemCount">1</span> pakaian</small>
                                </div>
                                <span class="badge bg-success fs-6" id="summaryNewDuration">30 menit</span>
                            </div>
                            <small class="text-muted mt-2 d-block" id="summaryGarmentsList"></small>
                        </div>

                        <div class="card p-3 border mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold small"><i class="bi bi-clock-history me-1"></i> Kapasitas Pengerjaan</span>
                                <span class="small text-muted">Maks. 480 menit / 8 jam per hari</span>
                            </div>
                            <div class="d-flex justify-content-between small text-muted mt-1">
                                <span>Alokasi saat ini</span>
                                <span id="calcExistingMinutes">0 menit</span>
                            </div>
                            <div class="d-flex justify-content-between small text-muted">
                                <span>Pesanan baru</span>
                                <span id="calcNewMinutes">+ 30 menit</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between fw-bold text-dark">
                                <span>Total setelah disimpan</span>
                                <span id="calcTotalMinutes">30 menit</span>
                            </div>
                            <div class="progress mt-2" style="height: 10px;">
                                <div id="calcProgressBar" class="progress-bar bg-success" role="progressbar" style="width: 6%;"></div>
                            </div>
                            <small class="mt-2 text-muted" id="calcRemainingText">480 - 30 = 450 menit kapasitas tersisa</small>
                        </div>

                        <div id="capacityValidationAlert" class="alert alert-success d-flex align-items-center small mb-0" role="alert">
                            <i class="bi bi-check-circle me-2 fs-5"></i>
                            <div>
                                <strong>Slot tersedia</strong>
                                <div>Pesanan ini masih dalam batas 480 menit. Anda dapat menyimpan pesanan ke Antrean.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-secondary" id="btnModalBack" style="display: none;">&larr; Kembali</button>
                    <button type="button" class="btn btn-outline-secondary" id="btnModalCancel" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" id="btnModalNext">Lanjutkan &rarr;</button>
                    <button type="submit" class="btn btn-success" id="btnModalSubmit" style="display: none;">Simpan pesanan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // LOGIKA FILTER TAB STATUS PADA MOBILE (BERSAMPINGAN)
    const mobileTabs = document.querySelectorAll('.mobile-status-tab');
    const mobilePanes = document.querySelectorAll('.mobile-col-pane');

    mobileTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const target = this.dataset.target;
            
            mobileTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            if (window.innerWidth < 768) {
                mobilePanes.forEach(pane => {
                    if (pane.id === `col-${target}`) {
                        pane.classList.remove('d-none');
                    } else {
                        pane.classList.add('d-none');
                    }
                });
            }
        });
    });

    // Reset Tampilan saat Resize Screen Desktop vs Mobile
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768) {
            mobilePanes.forEach(pane => pane.classList.remove('d-none'));
        } else {
            const activeTab = document.querySelector('.mobile-status-tab.active');
            if (activeTab) {
                activeTab.click();
            }
        }
    });

    let currentStep = 1;
    let itemCount = 1;

    const stepSection1 = document.getElementById('stepSection1');
    const stepSection2 = document.getElementById('stepSection2');
    const stepSection3 = document.getElementById('stepSection3');

    const stepIndicator1 = document.getElementById('stepIndicator1');
    const stepIndicator2 = document.getElementById('stepIndicator2');
    const stepIndicator3 = document.getElementById('stepIndicator3');

    const btnModalBack = document.getElementById('btnModalBack');
    const btnModalCancel = document.getElementById('btnModalCancel');
    const btnModalNext = document.getElementById('btnModalNext');
    const btnModalSubmit = document.getElementById('btnModalSubmit');

    const btnDecrementItem = document.getElementById('btnDecrementItem');
    const btnIncrementItem = document.getElementById('btnIncrementItem');
    const displayItemCount = document.getElementById('displayItemCount');

    const inputNamaPelanggan = document.getElementById('inputNamaPelanggan');
    const inputNoHp = document.getElementById('inputNoHp');
    const inputTenggatPengambilan = document.getElementById('inputTenggatPengambilan');

    inputNamaPelanggan.addEventListener('input', function() {
        if (this.value.trim() !== '') {
            this.classList.remove('is-invalid');
        }
    });

    inputNoHp.addEventListener('input', function() {
        if (this.value.trim() !== '') {
            this.classList.remove('is-invalid');
        }
    });

    btnDecrementItem.addEventListener('click', function () {
        if (itemCount > 1) {
            itemCount--;
            displayItemCount.innerText = itemCount + ' pakaian';
        }
    });

    btnIncrementItem.addEventListener('click', function () {
        if (itemCount < 10) {
            itemCount++;
            displayItemCount.innerText = itemCount + ' pakaian';
        }
    });

    btnModalNext.addEventListener('click', function () {
        if (currentStep === 1) {
            let isValid = true;
            const namaVal = inputNamaPelanggan.value.trim();
            const phoneVal = inputNoHp.value.trim();
            const phoneRegex = /^[0-9+\s-]{10,15}$/;

            if (!namaVal) {
                inputNamaPelanggan.classList.add('is-invalid');
                isValid = false;
            } else {
                inputNamaPelanggan.classList.remove('is-invalid');
            }

            if (!phoneVal) {
                document.getElementById('msgNoHp').innerText = 'Nomor WhatsApp wajib diisi.';
                inputNoHp.classList.add('is-invalid');
                isValid = false;
            } else if (!phoneRegex.test(phoneVal)) {
                document.getElementById('msgNoHp').innerText = 'Format nomor WhatsApp tidak valid (minimal 10 angka).';
                inputNoHp.classList.add('is-invalid');
                isValid = false;
            } else {
                inputNoHp.classList.remove('is-invalid');
            }

            if (!isValid) return;

            renderGarmentTabs();
            goToStep(2);
        } else if (currentStep === 2) {
            let step2Valid = true;
            let firstInvalidTab = -1;

            const garmentInputs = document.querySelectorAll('.garment-type-input');
            garmentInputs.forEach((input, idx) => {
                if (!input.value.trim()) {
                    input.classList.add('is-invalid');
                    step2Valid = false;
                    if (firstInvalidTab === -1) {
                        firstInvalidTab = idx;
                    }
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (!step2Valid) {
                if (firstInvalidTab !== -1) {
                    const tabTrigger = new bootstrap.Tab(document.getElementById(`item-tab-${firstInvalidTab}`));
                    tabTrigger.show();
                }
                return;
            }

            goToStep(3);
            calculateCapacityValidation();
        }
    });

    btnModalBack.addEventListener('click', function () {
        if (currentStep === 2) {
            goToStep(1);
        } else if (currentStep === 3) {
            goToStep(2);
        }
    });

    function goToStep(step) {
        currentStep = step;
        stepSection1.classList.add('d-none');
        stepSection2.classList.add('d-none');
        stepSection3.classList.add('d-none');

        stepIndicator1.className = 'step-item';
        stepIndicator2.className = 'step-item';
        stepIndicator3.className = 'step-item';

        if (step === 1) {
            stepSection1.classList.remove('d-none');
            stepIndicator1.className = 'step-item active';
            btnModalBack.style.display = 'none';
            btnModalCancel.style.display = 'inline-block';
            btnModalNext.style.display = 'inline-block';
            btnModalSubmit.style.display = 'none';
        } else if (step === 2) {
            stepSection2.classList.remove('d-none');
            stepIndicator1.className = 'step-item completed';
            stepIndicator2.className = 'step-item active';
            btnModalBack.style.display = 'inline-block';
            btnModalCancel.style.display = 'none';
            btnModalNext.style.display = 'inline-block';
            btnModalSubmit.style.display = 'none';
        } else if (step === 3) {
            stepSection3.classList.remove('d-none');
            stepIndicator1.className = 'step-item completed';
            stepIndicator2.className = 'step-item completed';
            stepIndicator3.className = 'step-item active';
            btnModalBack.style.display = 'inline-block';
            btnModalCancel.style.display = 'none';
            btnModalNext.style.display = 'none';
            btnModalSubmit.style.display = 'inline-block';
        }
    }

    function renderGarmentTabs() {
        const garmentTabs = document.getElementById('garmentTabs');
        const garmentTabContent = document.getElementById('garmentTabContent');
        garmentTabs.innerHTML = '';
        garmentTabContent.innerHTML = '';

        for (let i = 0; i < itemCount; i++) {
            const isActive = i === 0 ? 'active' : '';

            garmentTabs.innerHTML += `
                <li class="nav-item" role="presentation">
                    <button class="nav-link ${isActive} small text-dark fw-bold" id="item-tab-${i}" data-bs-toggle="tab" data-bs-target="#item-pane-${i}" type="button">
                        Pakaian ${i + 1}
                    </button>
                </li>
            `;

            garmentTabContent.innerHTML += `
                <div class="tab-pane fade show ${isActive} p-3 border border-top-0 rounded-bottom" id="item-pane-${i}">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Jenis Pakaian <span class="text-danger">*</span></label>
                                <input type="text" name="items[${i}][jenis_pakaian]" class="form-control garment-type-input" placeholder="Contoh: Celana bahan, Kemeja katun, Kebaya" required>
                                <div class="invalid-feedback">Jenis pakaian wajib diisi.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Catatan Permak / Detail Jahit</label>
                                <textarea name="items[${i}][catatan]" class="form-control" rows="3" placeholder="Contoh: Pendekkan bagian bawah 4 cm. Pertahankan lebar dan jahitan asli."></textarea>
                            </div>
                            <div>
                                <label class="form-label fw-semibold small">Tingkat Kesulitan / Estimasi Durasi</label>
                                <div class="row g-2">
                                    <div class="col-4">
                                        <input type="radio" class="btn-check difficulty-input" name="items[${i}][tingkat_kesulitan]" id="diff_ringan_${i}" value="ringan" data-duration="15">
                                        <label class="btn btn-outline-success w-100 p-2 text-center" for="diff_ringan_${i}">
                                            <div class="extra-small text-muted">Ringan</div>
                                            <strong class="d-block">15m</strong>
                                        </label>
                                    </div>
                                    <div class="col-4">
                                        <input type="radio" class="btn-check difficulty-input" name="items[${i}][tingkat_kesulitan]" id="diff_sedang_${i}" value="sedang" data-duration="30" checked>
                                        <label class="btn btn-outline-success w-100 p-2 text-center" for="diff_sedang_${i}">
                                            <div class="extra-small text-muted">Sedang</div>
                                            <strong class="d-block">30m</strong>
                                        </label>
                                    </div>
                                    <div class="col-4">
                                        <input type="radio" class="btn-check difficulty-input" name="items[${i}][tingkat_kesulitan]" id="diff_rumit_${i}" value="rumit" data-duration="60">
                                        <label class="btn btn-outline-success w-100 p-2 text-center" for="diff_rumit_${i}">
                                            <div class="extra-small text-muted">Rumit</div>
                                            <strong class="d-block">60m</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold small">Foto Penanda Pakaian</label>
                            <div class="border rounded p-3 text-center bg-light">
                                <i class="bi bi-camera fs-2 text-muted d-block mb-2"></i>
                                
                                <!-- TAMBAHKAN capture="environment" DI BARIS INPUT INI -->
                                <input type="file" name="items[${i}][foto]" class="form-control form-control-sm" accept="image/*" capture="environment">
                                
                                <small class="text-muted extra-small mt-1 d-block">Potret langsung atau pilih dari galeri.</small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        document.querySelectorAll('.garment-type-input').forEach(input => {
            input.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    this.classList.remove('is-invalid');
                }
            });
        });

        updateDurationCalculation();
        document.querySelectorAll('.difficulty-input').forEach(radio => {
            radio.addEventListener('change', updateDurationCalculation);
        });
    }

    function calculateNewOrderTotalMinutes() {
        let total = 0;
        document.querySelectorAll('.difficulty-input:checked').forEach(radio => {
            total += parseInt(radio.dataset.duration || 30);
        });
        return total;
    }

    function updateDurationCalculation() {
        const total = calculateNewOrderTotalMinutes();
        document.getElementById('badgeTotalDuration').innerText = `Total estimasi: ${total}m`;
        document.getElementById('garmentSummaryText').innerText = `✓ Total durasi pengerjaan: ${total} menit untuk ${itemCount} pakaian.`;
    }

    inputTenggatPengambilan.addEventListener('change', calculateCapacityValidation);

    function calculateCapacityValidation() {
        const totalNew = calculateNewOrderTotalMinutes();
        const clientName = inputNamaPelanggan.value || 'Klien Baru';
        const clientPhone = inputNoHp.value || '-';

        document.getElementById('summaryClientName').innerText = clientName;
        document.getElementById('summaryClientPhone').innerText = `${clientPhone} • ${itemCount} pakaian`;
        document.getElementById('summaryNewDuration').innerText = `${totalNew} menit`;

        let garmentSummaryList = [];
        document.querySelectorAll('.tab-pane').forEach((pane) => {
            const type = pane.querySelector('.garment-type-input')?.value || 'Pakaian';
            const diff = pane.querySelector('.difficulty-input:checked')?.dataset.duration || '30';
            garmentSummaryList.push(`${type} ${diff}m`);
        });
        document.getElementById('summaryGarmentsList').innerText = garmentSummaryList.join(' + ');

        if (!inputTenggatPengambilan.value) {
            const now = new Date();
            now.setHours(17, 0, 0, 0);
            inputTenggatPengambilan.value = now.toISOString().slice(0, 16);
        }

        const targetDate = inputTenggatPengambilan.value.split('T')[0];

        fetch(`/orders/check-capacity?date=${targetDate}&new_duration=${totalNew}`)
            .then(res => res.json())
            .then(data => {
                const existing = data.existing_minutes || 0;
                const totalAfter = data.total_after || totalNew;
                const maxCap = 480;
                const isAvailable = data.is_available;

                document.getElementById('calcExistingMinutes').innerText = `${existing} menit`;
                document.getElementById('calcNewMinutes').innerText = `+ ${totalNew} menit`;
                document.getElementById('calcTotalMinutes').innerText = `${totalAfter} menit (${Math.floor(totalAfter/60)}j ${totalAfter%60}m)`;

                const percentage = Math.min(100, Math.round((totalAfter / maxCap) * 100));
                const progressBar = document.getElementById('calcProgressBar');
                progressBar.style.width = percentage + '%';

                const alertBox = document.getElementById('capacityValidationAlert');

                if (isAvailable) {
                    progressBar.className = 'progress-bar bg-success';
                    const remaining = maxCap - totalAfter;
                    document.getElementById('calcRemainingText').innerText = `${maxCap} - ${totalAfter} = ${remaining} menit kapasitas tersisa`;
                    
                    alertBox.className = 'alert alert-success d-flex align-items-center small mb-0';
                    alertBox.innerHTML = `
                        <i class="bi bi-check-circle me-2 fs-5"></i>
                        <div>
                            <strong>Slot tersedia</strong>
                            <div>Pesanan ini masih dalam batas 480 menit. Anda dapat menyimpan pesanan ke Antrean.</div>
                        </div>
                    `;
                    btnModalSubmit.disabled = false;
                } else {
                    progressBar.className = 'progress-bar bg-danger';
                    const overflow = totalAfter - maxCap;
                    document.getElementById('calcRemainingText').innerText = `Kelebihan ${overflow} menit dari kapasitas maksimal harian!`;
                    
                    alertBox.className = 'alert alert-danger d-flex align-items-center small mb-0';
                    alertBox.innerHTML = `
                        <i class="bi bi-exclamation-triangle me-2 fs-5"></i>
                        <div>
                            <strong>Slot Penuh</strong>
                            <div>Kelebihan ${overflow} menit. Pilih tanggal lain atau kembali untuk menyesuaikan durasi pakaian sebelum menyimpan.</div>
                        </div>
                    `;
                    btnModalSubmit.disabled = true;
                }
            });
    }

    // EVENT DELEGATION CHECKBOX REAL-TIME
    document.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('item-checkbox')) {
            const cb = e.target;
            const detailId = cb.dataset.id;
            const status = cb.checked ? 'selesai' : 'belum';

            fetch(`/items/${detailId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: status })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const progressText = document.getElementById(`progress-text-${data.order_id}`);
                    if (progressText) {
                        progressText.innerText = `${data.completed_items} dari ${data.total_items} selesai`;
                    }

                    if (data.order_status === 'selesai') {
                        const modalElem = document.getElementById(`modalDetail${data.order_id}`);
                        if (modalElem) {
                            const modalInstance = bootstrap.Modal.getInstance(modalElem) || new bootstrap.Modal(modalElem);
                            modalInstance.hide();
                        }

                        const cardElem = document.getElementById(`order-card-${data.order_id}`);
                        const containerSelesai = document.getElementById('container-selesai');

                        if (cardElem && containerSelesai) {
                            cardElem.innerHTML = `
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted fw-bold">${data.nomor_pesanan}</small>
                                    <span class="badge bg-light text-dark border">${data.total_estimasi}m</span>
                                </div>
                                <h6 class="fw-bold mb-1 mt-1">${data.nama_pelanggan}</h6>
                                <button class="btn btn-success btn-sm w-100 mt-2 btn-send-wa" data-id="${data.order_id}">
                                    <i class="bi bi-whatsapp me-1"></i> Kirim WA & Siap Diambil
                                </button>
                            `;

                            containerSelesai.appendChild(cardElem);

                            updateBadgeCount('badge-diproses', -1);
                            updateBadgeCount('badge-selesai', 1);
                            updateBadgeCount('m-badge-diproses', -1);
                            updateBadgeCount('m-badge-selesai', 1);
                        }
                    }
                }
            });
        }
    });

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-send-wa');
        if (btn) {
            e.preventDefault();
            const orderId = btn.dataset.id;
            fetch(`/orders/${orderId}/send-wa`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.wa_url) {
                    window.open(data.wa_url, '_blank');
                    location.reload();
                }
            });
        }
    });

    function updateBadgeCount(badgeId, delta) {
        const badge = document.getElementById(badgeId);
        if (badge) {
            let current = parseInt(badge.innerText || '0');
            badge.innerText = Math.max(0, current + delta);
        }
    }
});
</script>
@endpush