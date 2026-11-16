<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Ulang Kata Sandi - SIFAN Solusi Busana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --brand-green: #0b6643; --bg-sand: #fbf9f5; }
        body { background-color: var(--bg-sand); font-family: 'Segoe UI', system-ui, sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card-auth { max-width: 420px; width: 100%; background: #ffffff; padding: 32px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); }
        .btn-brand { background-color: var(--brand-green); color: #ffffff; border: none; padding: 12px; border-radius: 12px; font-weight: 600; }
        .btn-brand:hover { background-color: #084e33; color: #ffffff; }
    </style>
</head>
<body>
    <div class="card-auth">
        <div class="text-center mb-4">
            <h4 class="fw-bold text-dark">Buat Kata Sandi Baru</h4>
            <p class="text-muted small">Silakan masukkan kata sandi baru untuk akun Anda.</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2 small rounded-3 mb-3">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $email) }}" required readonly>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">Kata Sandi Baru</label>
                <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-semibold text-secondary">Konfirmasi Kata Sandi Baru</label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi kata sandi baru" required>
            </div>

            <button type="submit" class="btn btn-brand w-100">Simpan Kata Sandi Baru</button>
        </form>
    </div>
</body>
</html>