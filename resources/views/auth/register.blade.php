<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Usaha - SIFAN Solusi Busana</title>
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
        <!-- Banner Kiri -->
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

        <!-- Form Kanan -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center p-4 p-sm-5">
            <div class="w-100" style="max-width: 440px;">
                <small class="text-success fw-bold text-uppercase">Langkah baru untuk usaha Anda</small>
                <h2 class="fw-bold text-dark mt-1 mb-2">Jahit lebih tenang. Mulai di sini.</h2>
                <p class="text-muted mb-4">Buat akun untuk mengelola pesanan dan kapasitas jahit dalam satu tempat.</p>

                <form action="{{ route('register') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Rian Firmansyah" value="{{ old('name') }}" required autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Usaha</label>
                        <input type="text" name="nama_usaha" class="form-control @error('nama_usaha') is-invalid @enderror" placeholder="SOLUSI BUSANA" value="{{ old('nama_usaha') }}" required>
                        @error('nama_usaha')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Email</label>
                        <div class="input-group has-validation">
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="solusibusana@gmail.com" value="{{ old('email') }}" required>
                            <span class="input-group-text bg-white text-muted"><i class="bi bi-envelope"></i></span>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="text-muted extra-small">Gunakan format email resmi (contoh: nama@gmail.com).</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Password</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="terms" required checked>
                        <label class="form-check-label small text-muted" for="terms">
                            Saya menyetujui <a href="#" class="text-success text-decoration-none">Syarat Layanan</a> dan <a href="#" class="text-success text-decoration-none">Kebijakan Privasi</a> Solusi Busana.
                        </label>
                    </div>

                    <button type="submit" class="btn btn-brand w-100 rounded-3 mb-4">
                        Daftarkan Usaha <i class="bi bi-arrow-right ms-1"></i>
                    </button>

                    <div class="text-center small text-muted">
                        Sudah punya akun? <a href="{{ route('login') }}" class="text-success fw-bold text-decoration-none">Masuk ke Akun</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>