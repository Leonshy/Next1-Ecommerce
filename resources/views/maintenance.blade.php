<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php $storeInfo = \App\Models\SiteContent::getByKey('store_info')?->metadata ?? []; @endphp
    <title>Mantenimiento — {{ $storeInfo['storeName'] ?? config('app.name') }}</title>
    @if(!empty($storeInfo['faviconUrl']))
        <link rel="icon" href="{{ $storeInfo['faviconUrl'] }}">
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #1a4a6b;
            --accent:  #e07b1d;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #0a0f1e;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* ── Fondo animado ── */
        .bg-glow {
            position: fixed; inset: 0; z-index: 0;
            background:
                radial-gradient(ellipse 80% 60% at 20% 10%,  rgba(26,74,107,.45) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 80% 90%,  rgba(224,123,29,.2) 0%, transparent 55%),
                radial-gradient(ellipse 50% 40% at 55% 50%,  rgba(26,74,107,.15) 0%, transparent 60%);
        }

        .grid-lines {
            position: fixed; inset: 0; z-index: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        /* Partículas flotantes */
        .particle {
            position: fixed; border-radius: 50%; z-index: 0;
            animation: float linear infinite;
            opacity: 0;
        }
        @keyframes float {
            0%   { transform: translateY(110vh) scale(0);   opacity: 0; }
            10%  { opacity: .6; }
            90%  { opacity: .3; }
            100% { transform: translateY(-10vh) scale(1.2); opacity: 0; }
        }

        /* ── Contenido ── */
        .wrapper {
            position: relative; z-index: 1;
            display: flex; flex-direction: column; align-items: center;
            text-align: center;
            padding: 2rem;
            max-width: 580px;
            width: 100%;
        }

        /* Logo */
        .logo-wrap { margin-bottom: 2.5rem; }
        .logo-wrap img { height: 52px; width: auto; filter: brightness(0) invert(1); }
        .logo-badge {
            display: inline-flex; align-items: center; gap: .6rem;
        }
        .logo-badge .box {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, var(--primary), #2563a8);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 900; font-size: 1rem; color: #fff;
            box-shadow: 0 0 24px rgba(26,74,107,.6);
        }
        .logo-badge .name {
            font-size: 1.5rem; font-weight: 800; color: #fff; letter-spacing: -.02em;
        }

        /* Ícono principal */
        .icon-ring {
            position: relative;
            width: 100px; height: 100px;
            margin: 0 auto 2rem;
        }
        .icon-ring svg.gear {
            width: 100px; height: 100px;
            color: var(--accent);
            filter: drop-shadow(0 0 20px rgba(224,123,29,.5));
            animation: spin 8s linear infinite;
        }
        .icon-ring svg.gear-inner {
            position: absolute; top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 44px; height: 44px;
            color: rgba(255,255,255,.15);
            animation: spin-reverse 5s linear infinite;
        }
        @keyframes spin         { to { transform: rotate(360deg); } }
        @keyframes spin-reverse { to { transform: translate(-50%, -50%) rotate(-360deg); } }

        /* Textos */
        .tag {
            display: inline-flex; align-items: center; gap: .4rem;
            background: rgba(224,123,29,.15);
            border: 1px solid rgba(224,123,29,.35);
            color: #f59e0b;
            font-size: .72rem; font-weight: 600; letter-spacing: .08em;
            text-transform: uppercase;
            padding: .3rem .85rem;
            border-radius: 100px;
            margin-bottom: 1.25rem;
        }
        .tag span.dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: #f59e0b;
            animation: pulse-dot 1.4s ease-in-out infinite;
        }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: .4; transform: scale(.7); }
        }

        h1 {
            font-size: clamp(2rem, 5vw, 2.8rem);
            font-weight: 800;
            color: #fff;
            line-height: 1.15;
            letter-spacing: -.03em;
            margin-bottom: 1rem;
        }
        h1 span { color: var(--accent); }

        .subtitle {
            font-size: 1rem;
            color: rgba(255,255,255,.55);
            line-height: 1.65;
            max-width: 420px;
            margin: 0 auto;
        }

        /* ETA */
        .eta-box {
            margin-top: 2rem;
            display: flex; align-items: center; gap: .6rem;
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 12px;
            padding: .75rem 1.25rem;
            color: rgba(255,255,255,.7);
            font-size: .875rem;
            font-weight: 500;
            backdrop-filter: blur(8px);
        }
        .eta-box svg { flex-shrink: 0; color: var(--accent); }

        /* Divisor */
        .divider {
            width: 60px; height: 2px;
            background: linear-gradient(90deg, transparent, var(--accent), transparent);
            margin: 2.5rem auto;
            border-radius: 2px;
        }

        /* Footer */
        .footer {
            position: fixed; bottom: 1.5rem; left: 0; right: 0;
            text-align: center;
            font-size: .78rem;
            color: rgba(255,255,255,.2);
            z-index: 1;
        }
        .footer a { color: rgba(255,255,255,.35); text-decoration: none; }
        .footer a:hover { color: rgba(255,255,255,.6); }
    </style>
</head>
<body>

    <div class="bg-glow"></div>
    <div class="grid-lines"></div>

    <!-- Partículas generadas con JS -->
    <script>
        const colors = ['#1a4a6b','#2563a8','#e07b1d','#f59e0b','#fff'];
        for (let i = 0; i < 18; i++) {
            const p = document.createElement('div');
            p.className = 'particle';
            const size = Math.random() * 4 + 2;
            p.style.cssText = `
                width:${size}px; height:${size}px;
                left:${Math.random()*100}vw;
                background:${colors[Math.floor(Math.random()*colors.length)]};
                animation-duration:${8 + Math.random()*12}s;
                animation-delay:${Math.random()*10}s;
            `;
            document.body.appendChild(p);
        }
    </script>

    <div class="wrapper">

        <!-- Logo -->
        <div class="logo-wrap">
            @if(!empty($storeInfo['logoUrl']))
                <img src="{{ $storeInfo['logoUrl'] }}" alt="{{ $storeInfo['storeName'] ?? '' }}">
            @else
                <div class="logo-badge">
                    <div class="box">N1</div>
                    <span class="name">{{ $storeInfo['storeName'] ?? config('app.name') }}</span>
                </div>
            @endif
        </div>

        <!-- Ícono -->
        <div class="icon-ring">
            <svg class="gear" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <svg class="gear-inner" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            </svg>
        </div>

        <!-- Tag -->
        <div class="tag">
            <span class="dot"></span>
            Mantenimiento en progreso
        </div>

        <!-- Título -->
        <h1>Estamos mejorando<br><span>tu experiencia</span></h1>

        <!-- Subtítulo / mensaje -->
        <p class="subtitle">{{ $message }}</p>

        <!-- ETA -->
        @if(!empty($estimated_time))
        <div class="eta-box">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 6v6l4 2"/>
            </svg>
            {{ $estimated_time }}
        </div>
        @endif

        <div class="divider"></div>

        <!-- Redes sociales si existen -->
        @php
            $nets = $storeInfo['socialNetworks'] ?? [];
            $active = collect($nets)->filter(fn($n) => !empty($n['enabled']) && !empty($n['url']));
        @endphp
        @if($active->isNotEmpty())
        <p style="color:rgba(255,255,255,.3); font-size:.8rem; margin-bottom:1rem;">Seguinos mientras tanto</p>
        <div style="display:flex; gap:.75rem; justify-content:center;">
            @foreach($active as $net => $info)
            <a href="{{ $info['url'] }}" target="_blank" rel="noopener"
               style="width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.5);text-decoration:none;transition:all .2s;font-size:.7rem;font-weight:600;"
               onmouseover="this.style.background='rgba(255,255,255,.13)';this.style.color='#fff'"
               onmouseout="this.style.background='rgba(255,255,255,.07)';this.style.color='rgba(255,255,255,.5)'">
                {{ strtoupper(substr($net, 0, 2)) }}
            </a>
            @endforeach
        </div>
        @endif

    </div>

    <footer class="footer">
        &copy; {{ date('Y') }} {{ $storeInfo['storeName'] ?? config('app.name') }} · Todos los derechos reservados
        @auth
        · <form method="POST" action="{{ route('logout') }}" style="display:inline">
            @csrf
            <button type="submit" style="background:none;border:none;cursor:pointer;color:rgba(255,255,255,.35);font-size:.78rem;font-family:inherit;padding:0;" onmouseover="this.style.color='rgba(255,255,255,.7)'" onmouseout="this.style.color='rgba(255,255,255,.35)'">
                Cerrar sesión
            </button>
          </form>
        @endauth
    </footer>

</body>
</html>
