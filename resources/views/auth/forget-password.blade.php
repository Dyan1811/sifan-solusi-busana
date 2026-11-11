<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - SIFAN Solusi Busana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --brand-green: #0b6643; --bg-sand: #fbf9f5; }
        body { background-color: var(--bg-sand); font-family: 'Segoe UI', system-ui, sans-serif; }
        .card-auth { max-width: 420px; margin: 60px auto; background: #fff; padding: 32px; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .btn-brand { background-color: var(--brand-green); color: #fff; border: none; padding: 12px; border-radius: 12px; font-weight: 600; }
        .btn-brand:hover { background-color: #084e33; color: #fff; }
    </style>
</head>
<body>
    <div class="card-auth">
        <div class="text-center mb-4">
            <div class="bg-success text-white rounded-3 d-inline-flex align-items-center justify-content-center p-2 mb-2">
                <i class="bi bi-key fs-4"></i>
            </div>
            <h4 class="fw-bold text-dark">Lupa Kata Sandi?</h4>
            <p class="text-muted small">Masukkan email Anda untuk menerima tautan pemulihan kata sandi.</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success py-2 small mb-3">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger py-2 small mb-3">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">Email</label>
                <input type="email" name="email" class="form-control" style="border-radius:10px;" placeholder="solusibusana@gmail.com" value="{{ old('email') }}" required>
            </div>
            <button type="submit" class="btn btn-brand w-100 mb-3">Kirim Tautan Pemulihan</button>
        </form>

        <div class="text-center">
            <a href="{{ route('login') }}" class="text-success text-decoration-none small fw-semibold">&larr; Kembali ke Login</a>
        </div>
    </div>
</body>
</html>