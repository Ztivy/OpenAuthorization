<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión · OAuth 2.0</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #0b0d12;
            --surface:   #13161f;
            --border:    rgba(255,255,255,0.07);
            --text:      #e8eaf0;
            --muted:     #6b7280;
            --accent:    #7c6af7;
            --discord:   #5865F2;
            --discord-h: #4752c4;
            --spotify:   #1DB954;
            --spotify-h: #17a349;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* ── Fondo animado ── */
        .bg-grid {
            position: fixed; inset: 0; z-index: 0;
            background-image:
                linear-gradient(rgba(124,106,247,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(124,106,247,0.04) 1px, transparent 1px);
            background-size: 60px 60px;
        }
        .bg-glow {
            position: fixed;
            width: 600px; height: 600px;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.15;
            animation: drift 12s ease-in-out infinite alternate;
        }
        .bg-glow.g1 { background: #5865F2; top: -200px; left: -200px; }
        .bg-glow.g2 { background: #1DB954; bottom: -200px; right: -200px; animation-delay: -6s; }

        @keyframes drift {
            from { transform: translate(0, 0) scale(1); }
            to   { transform: translate(40px, 30px) scale(1.08); }
        }

        /* ── Card ── */
        .card {
            position: relative; z-index: 1;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 48px 44px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 32px 80px rgba(0,0,0,0.5);
            animation: fadeUp 0.6s cubic-bezier(0.16,1,0.3,1) both;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(124,106,247,0.12);
            border: 1px solid rgba(124,106,247,0.25);
            color: #a89df9;
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 5px 12px;
            border-radius: 100px;
            margin-bottom: 24px;
        }
        .badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: #7c6af7; }

        h1 {
            font-family: 'Syne', sans-serif;
            font-size: 28px;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        h1 span { color: var(--accent); }

        .subtitle {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 36px;
        }

        /* ── Botones OAuth ── */
        .btn {
            display: flex;
            align-items: center;
            gap: 14px;
            width: 100%;
            padding: 15px 20px;
            border-radius: 14px;
            border: none;
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            font-weight: 500;
            color: #fff;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.15s, box-shadow 0.15s, filter 0.15s;
            margin-bottom: 12px;
        }
        .btn:hover {
            transform: translateY(-2px);
            filter: brightness(1.1);
        }
        .btn:active { transform: translateY(0); }

        .btn-discord {
            background: var(--discord);
            box-shadow: 0 8px 24px rgba(88,101,242,0.35);
        }
        .btn-discord:hover { box-shadow: 0 12px 32px rgba(88,101,242,0.5); }

        .btn-spotify {
            background: var(--spotify);
            box-shadow: 0 8px 24px rgba(29,185,84,0.35);
        }
        .btn-spotify:hover { box-shadow: 0 12px 32px rgba(29,185,84,0.5); }

        .btn-icon {
            width: 24px; height: 24px;
            flex-shrink: 0;
            fill: white;
        }

        .btn-text { flex: 1; }
        .btn-arrow { opacity: 0.6; font-size: 18px; }

        /* ── Divider ── */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0;
            color: var(--muted);
            font-size: 12px;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        /* ── Info footer ── */
        .info {
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid var(--border);
        }
        .info-title {
            font-family: 'Syne', sans-serif;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 12px;
        }
        .flow-steps {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .flow-step {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 12px;
            color: var(--muted);
            line-height: 1.5;
        }
        .step-num {
            flex-shrink: 0;
            width: 18px; height: 18px;
            border-radius: 50%;
            background: rgba(124,106,247,0.15);
            border: 1px solid rgba(124,106,247,0.3);
            color: #a89df9;
            font-size: 10px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ── Error alert ── */
        .alert-error {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.25);
            color: #fca5a5;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="bg-grid"></div>
<div class="bg-glow g1"></div>
<div class="bg-glow g2"></div>

<div class="card">

    <div class="badge">OAuth 2.0 · OpenID Connect</div>

    <h1>Bienvenido de<br><span>vuelta</span></h1>
    <p class="subtitle">
        Inicia sesión de forma segura usando tu cuenta de Discord o Spotify.
        No almacenamos tu contraseña.
    </p>

    {{-- Error de autenticación --}}
    @if(session('error'))
        <div class="alert-error">⚠ {{ session('error') }}</div>
    @endif

    {{-- ── Botón Discord ── --}}
    <a href="{{ route('oauth.redirect', 'discord') }}" class="btn btn-discord">
        <svg class="btn-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028 14.09 14.09 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/>
        </svg>
        <span class="btn-text">Continuar con Discord</span>
        <span class="btn-arrow">→</span>
    </a>

    {{-- ── Botón Spotify ── --}}
    <a href="{{ route('oauth.redirect', 'spotify') }}" class="btn btn-spotify">
        <svg class="btn-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/>
        </svg>
        <span class="btn-text">Continuar con Spotify</span>
        <span class="btn-arrow">→</span>
    </a>

    <div class="divider">Flujo de autorización</div>

    <div class="info">
        <div class="info-title">¿Cómo funciona OAuth 2.0?</div>
        <div class="flow-steps">
            <div class="flow-step">
                <div class="step-num">1</div>
                <span>Tu clic genera una Authorization URL con <code>client_id</code>, <code>scope</code> y <code>state</code></span>
            </div>
            <div class="flow-step">
                <div class="step-num">2</div>
                <span>El proveedor autentica y pide tu consentimiento explícito</span>
            </div>
            <div class="flow-step">
                <div class="step-num">3</div>
                <span>Regresa un <code>authorization_code</code> que se intercambia por un <code>access_token</code></span>
            </div>
            <div class="flow-step">
                <div class="step-num">4</div>
                <span>Con el token obtenemos tu perfil del Resource Server de forma segura</span>
            </div>
        </div>
    </div>

</div>

</body>
</html>