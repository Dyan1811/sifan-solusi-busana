<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - SIFAN Solusi Busana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --brand-dark-green: #0b4632;
            --brand-green: #0f5132;
            --bg-sand: #fbf9f5;
        }
        body, html {
            height: 100%;
            background-color: var(--bg-sand);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        .hero-section {
            background: linear-gradient(rgba(11, 70, 50, 0.85), rgba(11, 70, 50, 0.9)), 
                        url('https://images.unsplash.com/photo-1556905055-8f358a7a47b2?q=80&w=1000&auto=format&fit=crop') center/cover no-repeat;
            color: #ffffff;
        }
        .form-control:focus {
            border-color: var(--brand-green);
            box-shadow: 0 0 0 0.25rem rgba(15, 81, 50, 0.15);
        }
        .btn-brand {
            background-color: var(--brand-green);
            color: #ffffff;
            border: none;
            padding: 12px;
            font-weight: 600;
        }
        .btn-brand:hover {
            background-color: var(--brand-dark-green);
            color: #ffffff;
        }
    </style>
</head>
<body>
<div class="container-fluid h-100 p-0">
    <div class="row g-0 h-100">
        <!-- Banner Kiri (Desktop Split-Screen Layout) -->
        <div class="col-lg-6 hero-section d-none d-lg-flex flex-column justify-content-between p-5">
            <div>
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-scissors fs-3"></i>
                    <div>
                        <h5 class="fw-bold mb-0">SIFAN</h5>
                        <small class="text-white-50">TEMAN USAHA JAHIT</small>
                    </div>
                </div>
            </div>

            <div class="my-auto py-5 pe-5">
                <small class="text-uppercase tracking-wider text-white-50 fw-semibold">Dibuat untuk usaha jahit Anda</small>
                <h1 class="display-4 fw-bold mt-2 mb-3">Setiap jahitan, lebih tertata.</h1>
                <p class="fs-5 text-white-50">Dari pesanan pertama hingga siap diambil. Kelola antrean dengan tenang, fokus pada hasil jahitan yang istimewa.</p>
            </div>

            <div class="small text-white-50">
                &copy; 2026 SIFAN &bull; Dibuat dengan sepenuh hati.
            </div>
        </div>

        <!-- Form Kanan (Login) -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center p-4 p-sm-5">
            <div class="w-100" style="max-width: 440px;">
                <!-- Header Mobile Logo -->
                <div class="d-flex align-items-center gap-2 mb-4 d-lg-none">
                    <i class="bi bi-scissors fs-3 text-success"></i>
                    <div>
                        <h5 class="fw-bold mb-0 text-success">SIFAN</h5>
                        <small class="text-muted">TEMAN USAHA JAHIT</small>
                    </div>
                </div>

                <small class="text-success fw-bold text-uppercase">Selamat datang kembali</small>
                <h2 class="fw-bold text-dark mt-1 mb-2">Siap merapikan hari Anda?</h2>
                <p class="text-muted mb-4">Masuk dan lanjutkan cerita baik di balik setiap jahitan.</p>

                @if($errors->any())
                    <div class="alert alert-danger py-2 small" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    
                    <!-- Input Email -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Email</label>
                        <div class="input-group">
                            <input type="email" name="email" class="form-control border-end-0 @error('email') is-invalid @enderror" placeholder="solusibusana@gmail.com" value="{{ old('email') }}" required>
                            <span class="input-group-text bg-white border-start-0 text-muted rounded-end-3"><i class="bi bi-envelope"></i></span>
                        </div>
                        @error('email')
                            <div class="text-danger small mt-1" style="font-size: 0.8rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Input Password -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="inputPassword" class="form-control border-end-0 @error('password') is-invalid @enderror" placeholder="••••••••••••" required>
                            <button type="button" class="input-group-text bg-white border-start-0 text-muted rounded-end-3" id="togglePassword" style="cursor: pointer;">
                                <i class="bi bi-eye-slash" id="iconToggle"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="text-danger small mt-1" style="font-size: 0.8rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4 small">
                        <div class="form-check">
                            <input type="checkbox" name="remember" class="form-check-input" id="remember">
                            <label class="form-check-label text-muted" for="remember">Ingat saya</label>
                        </div>
                        <a href="{{ route('password.request') }}" class="text-success text-decoration-none fw-semibold">Lupa password?</a>
                    </div>

                    <button type="submit" class="btn btn-brand w-100 d-flex align-items-center justify-content-center gap-2">
                        <span>Masuk ke Sistem</span> <i class="bi bi-arrow-right"></i>
                    </button>
                </form>

                    <div class="text-center small text-muted mb-4">
                        Belum punya akun? <a href="{{ route('register') }}" class="text-success fw-bold text-decoration-none">Daftar usaha Anda</a>
                    </div>

                    <div class="text-center extra-small text-muted" style="font-size: 0.78rem;">
                        <i class="bi bi-shield-check me-1"></i> Data pelanggan Anda aman dan tetap milik Anda.
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const togglePassword = document.getElementById('togglePassword');
        const inputPassword = document.getElementById('inputPassword');
        const iconToggle = document.getElementById('iconToggle');

        if (togglePassword && inputPassword && iconToggle) {
            togglePassword.addEventListener('click', function () {
                // Ubah tipe input antara password dan text
                const type = inputPassword.getAttribute('type') === 'password' ? 'text' : 'password';
                inputPassword.setAttribute('type', type);

                // Ganti ikon mata terbuka / tertutup
                if (type === 'text') {
                    iconToggle.classList.remove('bi-eye-slash');
                    iconToggle.classList.add('bi-eye');
                } else {
                    iconToggle.classList.remove('bi-eye');
                    iconToggle.classList.add('bi-eye-slash');
                }
            });
        }
    });
</script>
</body>
</html>