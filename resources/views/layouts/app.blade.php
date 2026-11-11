<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SIFAN - Solusi Busana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --brand-green: #0b6643;
            --brand-light-green: #e8f5e9;
            --bg-sand: #fbf9f5;
        }
        body { background-color: var(--bg-sand); font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; color: #212529; }
        
        .sidebar {
            width: 260px;
            height: 100vh;
            position: sticky;
            top: 0;
            background: #ffffff;
            border-right: 1px solid #e0e0e0;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            z-index: 1020;
            overflow-y: auto;
        }
        
        .kanban-col { background: #f0f2f5; border-radius: 12px; padding: 16px; min-height: 75vh; }
        .card-kanban { border: none; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.04); transition: transform 0.2s; background: #ffffff; }
        .card-kanban:hover { transform: translateY(-2px); }
        .bottom-nav { position: fixed; bottom: 0; left: 0; right: 0; background: #fff; border-top: 1px solid #e5e5e5; z-index: 1030; }

        /* Step Wizard Indicator */
        .step-indicator { display: flex; justify-content: space-between; position: relative; margin-bottom: 1.5rem; }
        .step-item { display: flex; align-items: center; gap: 8px; font-size: 0.875rem; color: #6c757d; font-weight: 600; }
        .step-item.active { color: var(--brand-green); }
        .step-item.completed { color: #198754; }
        .step-badge { width: 28px; height: 28px; border-radius: 50%; background: #e9ecef; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; }
        .step-item.active .step-badge { background: var(--brand-green); color: white; }
        .step-item.completed .step-badge { background: #198754; color: white; }

        /* Mobile Tab Status Switcher (Bersampingan) */
        .mobile-status-tabs {
            display: flex;
            background: #eae8e1;
            padding: 4px;
            border-radius: 12px;
            gap: 4px;
        }
        .mobile-status-tab {
            flex: 1;
            text-align: center;
            padding: 8px 4px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            color: #555;
            cursor: pointer;
            text-decoration: none;
            border: none;
            background: transparent;
            transition: all 0.2s;
        }
        .mobile-status-tab.active {
            background: var(--brand-green);
            color: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn-brand {
            background-color: var(--brand-green);
            color: #ffffff;
            border: none;
        }
        .btn-brand:hover {
            background-color: #084e33;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Desktop Sidebar -->
        <div class="sidebar p-3 d-none d-md-flex">
            <div class="d-flex align-items-center mb-4 ps-2">
                <i class="bi bi-scissors text-success fs-3 me-2"></i>
                <div>
                    <h5 class="fw-bold mb-0 text-success">SIFAN</h5>
                    <small class="text-muted">TEMAN USAHA JAHIT</small>
                </div>
            </div>

            <button type="button" class="btn btn-brand w-100 py-2 mb-4 rounded-3 text-start fw-semibold" data-bs-toggle="modal" data-bs-target="#modalAddOrder">
                <i class="bi bi-plus-lg me-2"></i> Tambah pesanan baru
            </button>

            <div class="small text-muted fw-bold mb-2 ps-2">RUANG KERJA</div>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item mb-1">
                    <a href="{{ route('kanban.index') }}" class="nav-link {{ request()->routeIs('kanban.index') ? 'active bg-success' : 'text-dark' }}">
                        <i class="bi bi-kanban me-2"></i> Papan Antrean
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('history.index') }}" class="nav-link {{ request()->routeIs('history.index') ? 'active bg-success' : 'text-dark' }}">
                        <i class="bi bi-clock-history me-2"></i> Riwayat Orderan
                    </a>
                </li>
            </ul>

            <div class="mt-auto pt-3 border-top position-relative" style="z-index: 1025;">
                @php
                    $fullName = trim(Auth::user()->name ?? 'Rian Firmansyah');
                    $nameParts = explode(' ', $fullName);
                    $initials = strtoupper(
                        substr($nameParts[0], 0, 1) . 
                        (count($nameParts) > 1 ? substr(end($nameParts), 0, 1) : '')
                    );
                @endphp
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2 fw-bold" style="width:38px; height:38px; min-width:38px;">
                        {{ $initials }}
                    </div>
                    <div class="overflow-hidden">
                        <div class="fw-bold small text-truncate">{{ $fullName }}</div>
                        <div class="text-muted small text-truncate" style="font-size: 0.75rem;">{{ Auth::user()->nama_usaha ?? 'SOLUSI BUSANA' }}</div>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="w-100">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-1" style="cursor: pointer;">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-fill p-3 p-md-4 mb-5 mb-md-0 overflow-auto" style="min-width: 0;">
            <!-- Mobile Header Top Bar (Sesuai Proposal) -->
            <div class="d-flex d-md-none justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center">
                    <div class="bg-success text-white rounded-3 d-flex align-items-center justify-content-center me-2" style="width:36px; height:36px;">
                        <i class="bi bi-scissors fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">SIFAN</h6>
                        <small class="text-muted extra-small" style="font-size: 0.65rem;">TEMAN USAHA JAHIT</small>
                    </div>
                </div>
                @php
                    $fullName = trim(Auth::user()->name ?? 'Rian Firmansyah');
                    $nameParts = explode(' ', $fullName);
                    $initials = strtoupper(
                        substr($nameParts[0], 0, 1) . 
                        (count($nameParts) > 1 ? substr(end($nameParts), 0, 1) : '')
                    );
                @endphp
                <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:36px; height:36px;">
                    {{ $initials }}
                </div>
            </div>

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- Mobile Bottom Navigation Bar (Sesuai Proposal) -->
    <div class="bottom-nav d-md-none d-flex justify-content-around py-2">
        <a href="{{ route('kanban.index') }}" class="text-center text-decoration-none {{ request()->routeIs('kanban.index') ? 'text-success fw-bold' : 'text-muted' }}">
            <i class="bi bi-kanban fs-5 d-block"></i>
            <small style="font-size: 0.7rem;">Papan Antrean</small>
        </a>
        <a href="{{ route('history.index') }}" class="text-center text-decoration-none {{ request()->routeIs('history.index') ? 'text-success fw-bold' : 'text-muted' }}">
            <i class="bi bi-clock-history fs-5 d-block"></i>
            <small style="font-size: 0.7rem;">Riwayat Orderan</small>
        </a>
        <form action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-link text-muted text-decoration-none p-0 text-center">
                <i class="bi bi-box-arrow-right fs-5 d-block"></i>
                <small style="font-size: 0.7rem;">Logout</small>
            </button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>