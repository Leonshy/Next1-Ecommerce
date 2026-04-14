<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acceso no permitido</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 2rem;
        }
        .card {
            background: #fff;
            border-radius: 1.5rem;
            padding: 3rem 2.5rem;
            max-width: 440px; width: 100%;
            text-align: center;
            box-shadow: 0 4px 24px rgba(0,0,0,.07);
        }
        .code {
            font-size: 5rem; font-weight: 800; line-height: 1;
            background: linear-gradient(135deg, #1a4a6b, #2563a8);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            margin-bottom: .5rem;
        }
        h1 { font-size: 1.3rem; font-weight: 700; color: #111827; margin-bottom: .75rem; }
        p  { font-size: .95rem; color: #6b7280; line-height: 1.6; margin-bottom: 1.75rem; }
        .actions { display: flex; flex-direction: column; gap: .75rem; }
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
            padding: .65rem 1.5rem; border-radius: .75rem;
            font-size: .9rem; font-weight: 600; text-decoration: none;
            transition: opacity .15s;
        }
        .btn:hover { opacity: .85; }
        .btn-primary { background: #1a4a6b; color: #fff; }
        .btn-ghost   { background: #f1f5f9; color: #374151; border: 1px solid #e2e8f0; }
        .btn-logout  { background: none; border: none; cursor: pointer; font-family: inherit;
                       font-size: .85rem; color: #9ca3af; margin-top: .5rem; }
        .btn-logout:hover { color: #6b7280; }
    </style>
</head>
<body>
    <div class="card">
        <div class="code">403</div>
        <h1>Sin permiso de acceso</h1>
        <p>No tenés autorización para ver esta página con tu cuenta actual.</p>

        <div class="actions">
            <a href="{{ url('/') }}" class="btn btn-primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Ir al inicio
            </a>
            @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-ghost" style="width:100%">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Cerrar sesión e iniciar con otra cuenta
                </button>
            </form>
            @else
            <a href="{{ route('login') }}" class="btn btn-ghost">Iniciar sesión con otra cuenta</a>
            @endauth
        </div>
    </div>
</body>
</html>
