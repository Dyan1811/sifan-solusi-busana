<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Usaha - SIFAN Solusi Busana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --brand-green: #0b6643; --bg-sand: #fbf9f5; }
        body { background-color: var(--bg-sand); font-family: 'Segoe UI', system-ui, sans-serif; color: #212529; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card-auth { max-width: 440px; width: 100%; background: #ffffff; padding: 32px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); }
        .btn-brand { background-color: var(--brand-green); color: #ffffff; border: none; padding: 12px; border-radius: 12px; font-weight: 600; }
        .btn-brand:hover { background-color: #084e33; color: #ffffff; }
    </style>
</head>
<body>
    <div class="card-auth">
        <div class="text-center mb-4">
            <h4 class="fw-bold text-dark">Daftar Usaha Jahit Anda</h4>
            <p class="text-muted small">Mulai kelola antrean dan kapasitas jahitan lebih tertata.</p>
        </div>

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">Nama Pemilik / Usaha</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Solusi Busana" value="{{ old('name') }}" required>
                @error('name')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="solusibusana@gmail.com" value="{{ old('email') }}" required>
                @error('email')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 6 karakter" required>
                @error('password')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label small fw-semibold text-secondary">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" required>
            </div>

            <button type="submit" class="btn btn-brand w-100 mb-3">Daftar Sekarang</button>
        </form>

        <div class="text-center small">
            <span class="text-muted">Sudah punya akun?</span>
            <a href="{{ route('login') }}" class="text-success text-decoration-none fw-semibold">Masuk di sini</a>
        </div>
    </div>
</body>
</html>