<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard · OAuth 2.0</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:       #0b0d12;
            --surface:  #13161f;
            --surface2: #1a1d28;
            --border:   rgba(255,255,255,0.07);
            --text:     #e8eaf0;
            --muted:    #6b7280;
            --accent:   #7c6af7;
            --green:    #22c55e;
            --discord:  #5865F2;
            --twitch:   #9146FF;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        nav {
            position: sticky; top: 0; z-index: 10;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 32px;
            background: rgba(11,13,18,0.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
        }
        .nav-brand {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 16px;
        }
        .nav-brand span { color: var(--accent); }

        .btn-logout {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.06);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 8px 16px;
            border-radius: 10px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-logout:hover { background: rgba(255,255,255,0.1); }

        main { max-width: 880px; margin: 0 auto; padding: 48px 24px; }

        .welcome {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 40px;
            animation: fadeUp 0.5s cubic-bezier(0.16,1,0.3,1) both;
        }
        .avatar {
            width: 72px; height: 72px;
            border-radius: 50%;
            border: 2px solid var(--border);
            object-fit: cover;
            flex-shrink: 0;
        }
        .avatar-placeholder {
            width: 72px; height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), #a78bfa);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Syne', sans-serif;
            font-size: 28px;
            font-weight: 800;
            flex-shrink: 0;
        }
        .welcome-text h1 {
            font-family: 'Syne', sans-serif;
            font-size: 26px;
            font-weight: 800;
        }
        .welcome-text p { color: var(--muted); font-size: 14px; margin-top: 4px; }

        .provider-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 100px;
            font-size: 12px;
            font-weight: 500;
            margin-top: 8px;
        }
        .provider-badge.discord { background: rgba(88,101,242,0.15); border: 1px solid rgba(88,101,242,0.3); color: #818cf8; }
        .provider-badge.twitch  { background: rgba(145,70,255,0.15); border: 1px solid rgba(145,70,255,0.3); color: #c084fc; }
        .provider-badge svg { width: 12px; height: 12px; fill: currentColor; }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            animation: fadeUp 0.5s 0.1s cubic-bezier(0.16,1,0.3,1) both;
        }
        @media (max-width: 600px) { .grid { grid-template-columns: 1fr; } }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
        }
        .card.full { grid-column: 1 / -1; }

        .card-label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 16px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid var(--border);
            font-size: 13px;
        }
        .info-row:last-child { border-bottom: none; }
        .info-key { color: var(--muted); }
        .info-val { font-weight: 500; text-align: right; max-width: 220px; word-break: break-all; }
        .info-val.mono { font-family: 'Courier New', monospace; font-size: 11px; color: #a89df9; }

        .token-status {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-top: 12px;
        }
        .token-status.valid  { background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.2); color: #86efac; }
        .token-status.expired { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2); color: #fca5a5; }
        .dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .dot.green { background: var(--green); box-shadow: 0 0 8px var(--green); }
        .dot.red   { background: #ef4444; }

        .flow-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            margin-top: 16px;
            animation: fadeUp 0.5s 0.2s cubic-bezier(0.16,1,0.3,1) both;
        }
        .flow-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-top: 20px;
        }
        @media (max-width: 700px) { .flow-grid { grid-template-columns: repeat(2,1fr); } }

        .flow-step {
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px;
            text-align: center;
        }
        .flow-step .num { font-family: 'Syne', sans-serif; font-size: 22px; font-weight: 800; color: var(--accent); margin-bottom: 8px; }
        .flow-step .label { font-size: 12px; color: var(--muted); line-height: 1.5; }
        .flow-step .label strong { color: var(--text); display: block; margin-bottom: 2px; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<nav>
    <div class="nav-brand">OAuth <span>2.0</span> Demo</div>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn-logout">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>
            </svg>
            Cerrar sesión
        </button>
    </form>
</nav>

<main>

    <div class="welcome">
        @if($user->avatar)
            <img src="{{ $user->avatar }}" alt="Avatar" class="avatar">
        @else
            <div class="avatar-placeholder">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</div>
        @endif
        <div class="welcome-text">
            <h1>Hola, {{ $user->name }} 👋</h1>
            <p>Autenticado correctamente con OAuth 2.0</p>
            @if($user->provider)
                <div class="provider-badge {{ $user->provider }}">
                    @if($user->provider === 'discord')
                        <svg viewBox="0 0 24 24"><path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028 14.09 14.09 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03z"/></svg>
                    @else
                        <svg viewBox="0 0 24 24"><path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714z"/></svg>
                    @endif
                    Autenticado con {{ ucfirst($user->provider) }}
                </div>
            @endif
        </div>
    </div>

    <div class="grid">

        <div class="card">
            <div class="card-label">Perfil del usuario</div>
            <div class="info-row">
                <span class="info-key">Nombre</span>
                <span class="info-val">{{ $user->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-key">Email</span>
                <span class="info-val">{{ $user->email ?? '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-key">ID local</span>
                <span class="info-val mono">{{ $user->id }}</span>
            </div>
            <div class="info-row">
                <span class="info-key">Registro</span>
                <span class="info-val">{{ $user->created_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>

        <div class="card">
            <div class="card-label">Identidad OAuth 2.0</div>
            <div class="info-row">
                <span class="info-key">Proveedor</span>
                <span class="info-val">{{ ucfirst($user->provider ?? '—') }}</span>
            </div>
            <div class="info-row">
                <span class="info-key">Provider ID</span>
                <span class="info-val mono">{{ $user->provider_id ?? '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-key">Expira</span>
                <span class="info-val">
                    {{ $user->token_expires ? $user->token_expires->format('d/m/Y H:i') : 'Sin expiración' }}
                </span>
            </div>
            @if($user->access_token)
                <div class="token-status {{ $user->hasValidToken() ? 'valid' : 'expired' }}">
                    <div class="dot {{ $user->hasValidToken() ? 'green' : 'red' }}"></div>
                    Access Token {{ $user->hasValidToken() ? 'válido y activo' : 'expirado' }}
                </div>
            @endif
        </div>

        @if($user->access_token)
        <div class="card full">
            <div class="card-label">Access Token (OAuth 2.0)</div>
            <div class="info-row">
                <span class="info-key">Bearer Token</span>
                <span class="info-val mono">{{ substr($user->access_token, 0, 32) }}...</span>
            </div>
            <p style="font-size:12px; color: var(--muted); margin-top:12px; line-height:1.6;">
                Este token se usa como credencial para llamar a la API de {{ ucfirst($user->provider) }}
                en nombre del usuario. Nunca se expone al frontend.
            </p>
        </div>
        @endif

    </div>

    <div class="flow-card">
        <div class="card-label">Flujo OAuth 2.0 completado</div>
        <div class="flow-grid">
            <div class="flow-step">
                <div class="num">1</div>
                <div class="label"><strong>Authorization Request</strong>Tu clic generó la URL con client_id, scope y state</div>
            </div>
            <div class="flow-step">
                <div class="num">2</div>
                <div class="label"><strong>User Consent</strong>{{ ucfirst($user->provider ?? 'El proveedor') }} solicitó tu permiso explícito</div>
            </div>
            <div class="flow-step">
                <div class="num">3</div>
                <div class="label"><strong>Authorization Code</strong>El proveedor envió un código temporal de un solo uso</div>
            </div>
            <div class="flow-step">
                <div class="num">4</div>
                <div class="label"><strong>Access Token</strong>El código se intercambió por un token de acceso seguro ✓</div>
            </div>
        </div>
    </div>

</main>
</body>
</html>