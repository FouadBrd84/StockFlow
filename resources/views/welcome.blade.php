<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockFlow — La logistique en mode fluide</title>
    <meta name="description" content="StockFlow connecte fournisseurs, dépôt central et magasins : stock en temps réel, transferts, alertes automatiques.">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/stockflow-logo.svg') }}">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">
    <meta name="theme-color" content="#0b0a1f">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #1e1b4b; --text: #475569; --muted: #64748b; --night: #0b0a1f;
            --indigo: #6366f1; --violet: #8b5cf6; --pink: #ec4899; --orange: #f97316; --cyan: #06b6d4; --lime: #84cc16; --yellow: #fde047;
            --grad: linear-gradient(120deg, #6366f1 0%, #8b5cf6 35%, #ec4899 70%, #f97316 100%);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; overflow-x: clip; }
        body { font-family: 'Outfit', 'Segoe UI', system-ui, sans-serif; color: var(--text); background: #f6f3ff; overflow-x: clip; }
        a { text-decoration: none; }
        .wrap { width: min(1200px, 92vw); margin: 0 auto; }
        .grad-text { background: var(--grad); background-size: 200% 100%; -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; animation: slide 6s ease-in-out infinite alternate; }

        /* ── Barre de navigation ── */
        .top { position: fixed; inset: 0 0 auto 0; z-index: 50; padding: 18px 0; transition: background .3s, padding .3s, box-shadow .3s; }
        .top.scrolled { background: rgba(11, 10, 31, .72); backdrop-filter: blur(16px); padding: 10px 0; box-shadow: 0 10px 30px -15px rgba(0, 0, 0, .6); }
        .top .wrap { display: flex; align-items: center; gap: 28px; }
        .brand { display: flex; align-items: center; gap: 10px; color: #fff; font-weight: 800; font-size: 1.35rem; }
        .brand img { height: 40px; border-radius: 12px; transition: transform .5s cubic-bezier(.3, 1.6, .5, 1); }
        .brand:hover img { transform: rotate(-14deg) scale(1.1); }
        .links { display: flex; gap: 6px; margin-left: auto; }
        .links a { color: rgba(255, 255, 255, .78); font-weight: 500; padding: 8px 14px; border-radius: 999px; transition: background .2s, color .2s; }
        .links a:hover { background: rgba(255, 255, 255, .1); color: #fff; }
        .btn { display: inline-flex; align-items: center; gap: 8px; font-weight: 700; border-radius: 999px; padding: 12px 24px; transition: transform .2s, box-shadow .2s, background-position .5s; position: relative; overflow: hidden; }
        .btn:hover { transform: translateY(-3px); }
        .btn-main { color: #fff; background: linear-gradient(120deg, #6366f1, #8b5cf6, #ec4899, #f97316); background-size: 250% 100%; box-shadow: 0 16px 30px -14px rgba(236, 72, 153, .9); }
        .btn-main:hover { background-position: 100% 0; color: #fff; }
        .btn-main::after { content: ""; position: absolute; top: 0; left: -60%; width: 40%; height: 100%; background: linear-gradient(100deg, transparent, rgba(255, 255, 255, .5), transparent); transform: skewX(-20deg); transition: left .7s; }
        .btn-main:hover::after { left: 130%; }
        .btn-ghost { color: #fff; border: 1.5px solid rgba(255, 255, 255, .35); background: rgba(255, 255, 255, .06); }
        .btn-ghost:hover { color: #fff; background: rgba(255, 255, 255, .14); }
        .btn-sm { padding: 9px 18px; font-size: .92rem; }

        /* ── Hero (fond nuit + bulles + grille) ── */
        .hero { position: relative; min-height: 100vh; background: var(--night); color: #fff; overflow: hidden; display: flex; align-items: center; padding: 130px 0 90px; }
        .blob { position: absolute; border-radius: 50%; filter: blur(70px); opacity: .55; animation: drift 16s ease-in-out infinite alternate; }
        .b1 { width: 520px; height: 520px; background: #6366f1; left: -140px; top: -120px; }
        .b2 { width: 460px; height: 460px; background: #ec4899; right: -120px; top: 60px; animation-delay: -5s; }
        .b3 { width: 420px; height: 420px; background: #f97316; left: 35%; bottom: -220px; opacity: .35; animation-delay: -9s; }
        .grid-bg { position: absolute; inset: 0; background-image: linear-gradient(rgba(255, 255, 255, .06) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, .06) 1px, transparent 1px); background-size: 56px 56px; mask-image: radial-gradient(ellipse at 50% 40%, #000 30%, transparent 75%); -webkit-mask-image: radial-gradient(ellipse at 50% 40%, #000 30%, transparent 75%); }
        .shape { position: absolute; color: rgba(255, 255, 255, .5); font-size: 1.6rem; animation: bob 6s ease-in-out infinite; }
        .hero .wrap { position: relative; z-index: 2; display: grid; grid-template-columns: 1.05fr 1fr; gap: 60px; align-items: center; }
        .pill { display: inline-flex; align-items: center; gap: 8px; padding: 7px 16px; border-radius: 999px; font-size: .88rem; font-weight: 600; background: rgba(255, 255, 255, .08); border: 1px solid rgba(255, 255, 255, .16); }
        .pill .dot { width: 8px; height: 8px; border-radius: 50%; background: var(--lime); box-shadow: 0 0 0 0 rgba(132, 204, 22, .7); animation: ping 1.8s infinite; }
        .hero h1 { font-size: clamp(2.6rem, 5.4vw, 4.6rem); line-height: 1.02; font-weight: 900; letter-spacing: -2px; margin: 22px 0; }
        .rot { display: inline-block; min-width: 1ch; }
        .caret { display: inline-block; width: 5px; height: .9em; margin-left: 4px; vertical-align: -6px; border-radius: 3px; background: var(--pink); animation: blink 1s steps(1) infinite; }
        .hero p.lead { font-size: 1.2rem; line-height: 1.6; color: rgba(255, 255, 255, .78); max-width: 540px; }
        .cta { display: flex; gap: 14px; flex-wrap: wrap; margin: 34px 0 26px; }
        .trust { display: flex; gap: 10px; flex-wrap: wrap; }
        .trust span { font-size: .85rem; padding: 6px 12px; border-radius: 10px; background: rgba(255, 255, 255, .06); color: rgba(255, 255, 255, .8); }

        /* Faux tableau de bord flottant */
        .mock-zone { position: relative; height: 470px; }
        .mock { position: absolute; inset: 20px 10px 30px 20px; border-radius: 26px; background: rgba(255, 255, 255, .08); border: 1px solid rgba(255, 255, 255, .18); backdrop-filter: blur(18px); box-shadow: 0 40px 90px -30px rgba(0, 0, 0, .8); padding: 18px; transition: transform .2s ease-out; }
        .mock-head { display: flex; align-items: center; gap: 7px; margin-bottom: 16px; }
        .mock-head i { width: 11px; height: 11px; border-radius: 50%; display: block; }
        .mock-head span { margin-left: 10px; font-size: .8rem; padding: 4px 12px; border-radius: 999px; background: rgba(255, 255, 255, .08); color: rgba(255, 255, 255, .7); }
        .tiles { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
        .tile { border-radius: 16px; padding: 12px 14px; background: rgba(255, 255, 255, .07); }
        .tile small { font-size: .7rem; letter-spacing: 1px; text-transform: uppercase; color: rgba(255, 255, 255, .6); }
        .tile b { display: block; font-size: 1.6rem; font-weight: 800; }
        .bars { display: flex; align-items: flex-end; gap: 10px; height: 150px; margin: 16px 0; padding: 14px; border-radius: 16px; background: rgba(255, 255, 255, .05); }
        .bars div { flex: 1; height: var(--h); border-radius: 8px 8px 4px 4px; background: linear-gradient(180deg, var(--c1), var(--c2)); transform-origin: bottom; animation: grow 1.2s cubic-bezier(.2, .8, .2, 1) backwards; }
        .feed { display: grid; gap: 8px; }
        .feed div { display: flex; align-items: center; gap: 10px; font-size: .85rem; padding: 9px 12px; border-radius: 12px; background: rgba(255, 255, 255, .06); color: rgba(255, 255, 255, .85); }
        .toast { position: absolute; display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 16px; font-size: .9rem; font-weight: 600; color: var(--ink); background: #fff; box-shadow: 0 20px 40px -18px rgba(0, 0, 0, .7); animation: bob 5s ease-in-out infinite; z-index: 3; transition: transform .2s ease-out; }
        .toast em { font-style: normal; font-size: 1.2rem; }
        .t1 { top: -6px; right: -18px; }
        .t2 { bottom: 70px; left: -40px; animation-delay: -1.5s; }
        .t3 { bottom: -6px; right: 40px; animation-delay: -3s; }

        /* ── Bandeau qui défile ── */
        .marquee { background: var(--grad); background-size: 200% 100%; animation: slide 8s linear infinite alternate; transform: rotate(-2deg) scale(1.04); margin: -28px 0 0; position: relative; z-index: 5; padding: 18px 0; overflow: hidden; box-shadow: 0 20px 40px -20px rgba(236, 72, 153, .8); }
        .marquee-track { display: flex; gap: 48px; width: max-content; animation: scroll 28s linear infinite; }
        .marquee span { color: #fff; font-weight: 800; font-size: 1.25rem; white-space: nowrap; }

        /* ── Sections claires ── */
        .light { position: relative; padding: 110px 0; background-color: #f6f3ff; background-image: radial-gradient(circle at 1px 1px, rgba(124, 58, 237, .14) 1.2px, transparent 0); background-size: 24px 24px; }
        .light.alt { background-color: #fdf2f8; }
        .kicker { display: inline-block; font-weight: 800; font-size: .85rem; letter-spacing: 2px; text-transform: uppercase; color: var(--violet); margin-bottom: 12px; }
        .h2 { font-size: clamp(2rem, 3.6vw, 3rem); font-weight: 900; color: var(--ink); letter-spacing: -1px; line-height: 1.1; }
        .sub { font-size: 1.1rem; color: var(--muted); margin-top: 14px; max-width: 620px; }
        .center { text-align: center; }
        .center .sub { margin-left: auto; margin-right: auto; }

        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
        .stat { background: rgba(255, 255, 255, .85); border-radius: 26px; padding: 28px; border: 1px solid #fff; box-shadow: 0 20px 40px -24px rgba(76, 29, 149, .4); position: relative; overflow: hidden; }
        .stat::before { content: ""; position: absolute; inset: auto -40px -40px auto; width: 130px; height: 130px; border-radius: 50%; background: var(--c); opacity: .18; }
        .stat b { display: block; font-size: 3.2rem; font-weight: 900; line-height: 1; color: var(--c); }
        .stat span { color: var(--muted); font-weight: 600; }

        /* Bento */
        .bento { display: grid; grid-template-columns: repeat(4, 1fr); grid-auto-rows: 210px; gap: 18px; margin-top: 54px; }
        .card { position: relative; overflow: hidden; border-radius: 28px; padding: 26px; background: rgba(255, 255, 255, .9); border: 1px solid #fff; box-shadow: 0 24px 44px -26px rgba(76, 29, 149, .45); transition: transform .3s cubic-bezier(.2, .8, .2, 1), box-shadow .3s; }
        .card:hover { transform: translateY(-6px) rotate(-.4deg); box-shadow: 0 34px 60px -26px rgba(76, 29, 149, .55); }
        .card .ico { width: 52px; height: 52px; border-radius: 16px; display: grid; place-items: center; font-size: 1.5rem; color: #fff; background: var(--c, var(--grad)); margin-bottom: 16px; box-shadow: 0 12px 22px -10px var(--s, rgba(139, 92, 246, .9)); }
        .card h3 { font-size: 1.3rem; font-weight: 800; color: var(--ink); margin-bottom: 8px; }
        .card p { font-size: .98rem; line-height: 1.55; }
        .big { grid-column: span 2; grid-row: span 2; background: var(--night); color: rgba(255, 255, 255, .8); }
        .big h3 { color: #fff; font-size: 1.8rem; }
        .big .mini-bars { position: absolute; left: 26px; right: 26px; bottom: 26px; height: 170px; display: flex; align-items: flex-end; gap: 12px; }
        .big .mini-bars div { flex: 1; border-radius: 10px 10px 4px 4px; background: linear-gradient(180deg, #ec4899, #8b5cf6); animation: pulse-h 3.5s ease-in-out infinite; }
        .wide { grid-column: span 2; }
        .card .deco { position: absolute; right: -10px; bottom: -18px; font-size: 6rem; opacity: .12; transform: rotate(-12deg); transition: transform .4s cubic-bezier(.3, 1.6, .5, 1), opacity .3s; }
        .card:hover .deco { transform: rotate(4deg) scale(1.15); opacity: .25; }

        /* Rôles */
        .roles { display: grid; grid-template-columns: repeat(3, 1fr); gap: 22px; margin-top: 54px; }
        .role { border-radius: 30px; padding: 32px 28px; color: #fff; position: relative; overflow: hidden; background: var(--c); box-shadow: 0 30px 50px -28px var(--s); transition: transform .35s cubic-bezier(.2, .8, .2, 1); }
        .role:hover { transform: translateY(-8px) rotate(1deg); }
        .role::after { content: ""; position: absolute; inset: 0; background-image: radial-gradient(circle at 1px 1px, rgba(255, 255, 255, .22) 1.2px, transparent 0); background-size: 18px 18px; pointer-events: none; }
        .role .emoji { font-size: 3rem; display: inline-block; animation: bob 4s ease-in-out infinite; }
        .role h3 { font-size: 1.6rem; font-weight: 900; margin: 10px 0 14px; }
        .role li { list-style: none; padding: 7px 0; border-bottom: 1px dashed rgba(255, 255, 255, .3); font-weight: 500; }
        .role li::before { content: "✦ "; opacity: .8; }

        /* Flux */
        .flow { display: grid; grid-template-columns: repeat(6, 1fr); gap: 16px; margin-top: 60px; position: relative; }
        .flow::before { content: ""; position: absolute; left: 6%; right: 6%; top: 34px; height: 4px; background: repeating-linear-gradient(90deg, #8b5cf6 0 14px, transparent 14px 26px); animation: dash 1.2s linear infinite; opacity: .5; }
        .step { position: relative; text-align: center; }
        .step .num { width: 68px; height: 68px; margin: 0 auto 14px; border-radius: 22px; display: grid; place-items: center; font-size: 1.9rem; background: #fff; box-shadow: 0 16px 30px -16px rgba(76, 29, 149, .6); border: 3px solid transparent; background-image: linear-gradient(#fff, #fff), var(--grad); background-origin: border-box; background-clip: padding-box, border-box; transition: transform .3s cubic-bezier(.3, 1.6, .5, 1); }
        .step:hover .num { transform: scale(1.12) rotate(-6deg); }
        .step b { display: block; color: var(--ink); font-weight: 800; }
        .step small { color: var(--muted); }
        .phase { display: flex; justify-content: space-around; margin-top: 28px; gap: 16px; flex-wrap: wrap; }
        .phase span { padding: 8px 16px; border-radius: 999px; font-weight: 700; font-size: .9rem; background: #fff; color: var(--violet); box-shadow: 0 10px 20px -14px rgba(76, 29, 149, .6); }

        /* Stack */
        .stack { display: flex; gap: 12px; flex-wrap: wrap; justify-content: center; margin-top: 34px; }
        .stack span { padding: 12px 20px; border-radius: 16px; background: #fff; font-weight: 700; color: var(--ink); box-shadow: 0 12px 24px -18px rgba(76, 29, 149, .7); transition: transform .2s; }
        .stack span:hover { transform: translateY(-4px) rotate(-2deg); }

        /* Appel à l'action */
        .final { position: relative; overflow: hidden; border-radius: 40px; padding: 70px 40px; text-align: center; color: #fff; background: var(--grad); background-size: 200% 100%; animation: slide 8s ease-in-out infinite alternate; box-shadow: 0 40px 80px -36px rgba(236, 72, 153, .9); }
        .final::before { content: ""; position: absolute; inset: 0; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='200' viewBox='0 0 200 200'%3E%3Cg fill='none' stroke='%23ffffff' stroke-width='3' stroke-linecap='round' opacity='.3'%3E%3Ccircle cx='30' cy='40' r='12'/%3E%3Cpath d='M150 30 l14 24 h-28 z'/%3E%3Cpath d='M30 160 q14 -16 28 0 t28 0'/%3E%3Cpath d='M170 140 v22 M159 151 h22'/%3E%3C/g%3E%3C/svg%3E"); animation: patterns 40s linear infinite; }
        .final > * { position: relative; }
        .final h2 { font-size: clamp(2rem, 4vw, 3.4rem); font-weight: 900; letter-spacing: -1px; }
        .final p { font-size: 1.15rem; opacity: .9; margin: 14px 0 30px; }
        .final .btn-white { background: #fff; color: var(--ink); box-shadow: 0 16px 30px -14px rgba(0, 0, 0, .5); }

        /* Pied de page */
        footer { background: var(--night); color: rgba(255, 255, 255, .6); padding: 46px 0; }
        footer .wrap { display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap; }
        footer .school { display: flex; align-items: center; gap: 10px; font-size: .9rem; }
        footer .school img { height: 34px; border-radius: 8px; background: #fff; padding: 3px; }

        /* Apparition au défilement */
        .reveal { opacity: 0; transform: translateY(40px) scale(.98); transition: opacity .8s cubic-bezier(.2, .8, .2, 1), transform .8s cubic-bezier(.2, .8, .2, 1); transition-delay: var(--d, 0s); }
        .reveal.in { opacity: 1; transform: none; }

        #progress { position: fixed; top: 0; left: 0; height: 4px; width: 0; z-index: 60; background: var(--grad); }

        @keyframes slide { from { background-position: 0% 50%; } to { background-position: 100% 50%; } }
        @keyframes drift { to { transform: translate(80px, 60px) scale(1.15); } }
        @keyframes bob { 50% { transform: translateY(-14px); } }
        @keyframes ping { 0% { box-shadow: 0 0 0 0 rgba(132, 204, 22, .7); } 80%, 100% { box-shadow: 0 0 0 10px rgba(132, 204, 22, 0); } }
        @keyframes blink { 50% { opacity: 0; } }
        @keyframes grow { from { transform: scaleY(0); } }
        @keyframes scroll { to { transform: translateX(-50%); } }
        @keyframes pulse-h { 50% { transform: scaleY(.7); } }
        @keyframes dash { to { background-position: 26px 0; } }
        @keyframes patterns { to { background-position: 200px 200px; } }

        @media (max-width: 992px) {
            .hero .wrap { grid-template-columns: 1fr; }
            .mock-zone { height: 430px; }
            .links { display: none; }
            .stats { grid-template-columns: repeat(2, 1fr); }
            .bento { grid-template-columns: repeat(2, 1fr); }
            .roles { grid-template-columns: 1fr; }
            .flow { grid-template-columns: repeat(3, 1fr); row-gap: 30px; }
            .flow::before { display: none; }
        }
        @media (max-width: 600px) {
            .top .btn-ghost { display: none; }
            .bento { grid-template-columns: 1fr; grid-auto-rows: auto; }
            .big, .wide { grid-column: auto; grid-row: auto; min-height: 340px; }
            .card { min-height: 190px; }
            .flow { grid-template-columns: repeat(2, 1fr); }
            .t2 { left: -6px; }
            .t1 { right: -6px; }
            .final { padding: 50px 22px; border-radius: 28px; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
            .reveal { opacity: 1; transform: none; }
        }
    </style>
</head>
<body>
    <div id="progress"></div>

    {{-- Barre de navigation --}}
    <header class="top" id="top">
        <div class="wrap">
            <a href="{{ route('home') }}" class="brand"><img src="{{ asset('images/stockflow-logo.svg') }}" alt="">StockFlow</a>
            <nav class="links" aria-label="Sections">
                <a href="#fonctionnalites">Fonctionnalités</a>
                <a href="#roles">Rôles</a>
                <a href="#flux">Le flux</a>
            </nav>
            <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Connexion</a>
            <a href="{{ route('register') }}" class="btn btn-main btn-sm">Commencer <i class="bi bi-arrow-right"></i></a>
        </div>
    </header>

    {{-- Hero --}}
    <section class="hero">
        <div class="blob b1"></div><div class="blob b2"></div><div class="blob b3"></div>
        <div class="grid-bg"></div>
        <span class="shape" style="left:8%;top:22%">✦</span>
        <span class="shape" style="right:46%;top:16%;animation-delay:-2s">◯</span>
        <span class="shape" style="left:44%;bottom:14%;animation-delay:-4s">△</span>
        <span class="shape" style="right:6%;bottom:22%;animation-delay:-1s">✳</span>

        <div class="wrap">
            <div>
                <span class="pill"><span class="dot"></span> Projet DWWM 2026 · version Neo ✨</span>
                <h1>Ton stock,<br><span class="grad-text rot" id="rot">en temps réel</span><span class="caret"></span></h1>
                <p class="lead">StockFlow connecte fournisseurs, dépôt central et magasins. Commandes, transferts, alertes : tout bouge tout seul, et toi tu gardes le contrôle.</p>
                <div class="cta">
                    <a href="{{ route('login') }}" class="btn btn-main"><i class="bi bi-rocket-takeoff"></i> Accéder à la plateforme</a>
                    <a href="{{ route('register') }}" class="btn btn-ghost"><i class="bi bi-person-plus"></i> Créer un compte</a>
                </div>
                <div class="trust">
                    <span>⚡ Laravel 12</span><span>🧪 46 tests auto</span><span>🔐 3 rôles sécurisés</span>
                </div>
            </div>

            <div class="mock-zone" id="mock-zone">
                <div class="mock" data-depth="10">
                    <div class="mock-head"><i style="background:#f87171"></i><i style="background:#fbbf24"></i><i style="background:#34d399"></i><span>stockflow · dashboard</span></div>
                    <div class="tiles">
                        <div class="tile"><small>Produits</small><b>248</b></div>
                        <div class="tile"><small>Transferts</small><b>12</b></div>
                        <div class="tile"><small>Alertes</small><b style="color:#fda4af">3</b></div>
                    </div>
                    <div class="bars">
                        <div style="--h:55%;--c1:#818cf8;--c2:#6366f1"></div>
                        <div style="--h:80%;--c1:#c084fc;--c2:#8b5cf6;animation-delay:.1s"></div>
                        <div style="--h:40%;--c1:#f472b6;--c2:#ec4899;animation-delay:.2s"></div>
                        <div style="--h:95%;--c1:#fb923c;--c2:#f97316;animation-delay:.3s"></div>
                        <div style="--h:65%;--c1:#22d3ee;--c2:#06b6d4;animation-delay:.4s"></div>
                        <div style="--h:30%;--c1:#a3e635;--c2:#84cc16;animation-delay:.5s"></div>
                        <div style="--h:75%;--c1:#c084fc;--c2:#8b5cf6;animation-delay:.6s"></div>
                    </div>
                    <div class="feed">
                        <div>📦 Réception : stock du dépôt +500</div>
                        <div>🚚 Transfert vers le magasin City</div>
                    </div>
                </div>
                <div class="toast t1" data-depth="26"><em>✅</em> Commande #24 validée</div>
                <div class="toast t2" data-depth="34"><em>⚠️</em> Stock bas · yaourt vanille</div>
                <div class="toast t3" data-depth="20"><em>🎉</em> Stock du magasin +40</div>
            </div>
        </div>
    </section>

    {{-- Bandeau --}}
    <div class="marquee" aria-hidden="true">
        <div class="marquee-track">
            @for ($i = 0; $i < 2; $i++)
                <span>📦 Stock en temps réel</span><span>✦</span><span>🔔 Alertes auto</span><span>✦</span><span>🚚 Transferts</span><span>✦</span><span>📊 Dashboards</span><span>✦</span><span>🔐 Accès par rôle</span><span>✦</span><span>📄 Export PDF</span><span>✦</span><span>🧪 Testé avec Pest</span><span>✦</span>
            @endfor
        </div>
    </div>

    {{-- Chiffres --}}
    <section class="light" style="padding-top:130px">
        <div class="wrap stats">
            <div class="stat reveal" style="--c:#6366f1"><b data-count="3">0</b><span>rôles avec leur propre espace</span></div>
            <div class="stat reveal" style="--c:#ec4899;--d:.1s"><b data-count="10">0</b><span>règles métier codées</span></div>
            <div class="stat reveal" style="--c:#f97316;--d:.2s"><b data-count="46">0</b><span>tests automatisés</span></div>
            <div class="stat reveal" style="--c:#06b6d4;--d:.3s"><b data-count="100" data-suffix="%">0</b><span>des mouvements tracés</span></div>
        </div>
    </section>

    {{-- Fonctionnalités --}}
    <section class="light alt" id="fonctionnalites">
        <div class="wrap">
            <div class="center reveal">
                <span class="kicker">Fonctionnalités</span>
                <h2 class="h2">Tout ce qu'il faut. <span class="grad-text">Rien de trop.</span></h2>
                <p class="sub">Un seul outil pour suivre la marchandise, du fournisseur jusqu'au rayon.</p>
            </div>
            <div class="bento">
                <div class="card big reveal">
                    <div class="ico"><i class="bi bi-lightning-charge-fill"></i></div>
                    <h3>Stock en temps réel</h3>
                    <p>Chaque réception, transfert ou vente met à jour le stock instantanément, magasin par magasin.</p>
                    <div class="mini-bars" aria-hidden="true">
                        <div style="height:60%"></div><div style="height:85%;animation-delay:-.6s"></div><div style="height:45%;animation-delay:-1.2s"></div><div style="height:95%;animation-delay:-1.8s"></div><div style="height:70%;animation-delay:-2.4s"></div><div style="height:50%;animation-delay:-3s"></div>
                    </div>
                </div>
                <div class="card wide reveal" style="--d:.1s">
                    <div class="ico" style="--c:linear-gradient(135deg,#f43f5e,#f97316);--s:rgba(244,63,94,.8)"><i class="bi bi-bell-fill"></i></div>
                    <h3>Alertes automatiques</h3>
                    <p>Stock bas ou rupture ? Une alerte, sans doublon, plus un email au bon moment.</p>
                    <span class="deco">🔔</span>
                </div>
                <div class="card reveal" style="--d:.15s">
                    <div class="ico" style="--c:linear-gradient(135deg,#06b6d4,#6366f1);--s:rgba(6,182,212,.8)"><i class="bi bi-arrow-left-right"></i></div>
                    <h3>Transferts</h3>
                    <p>Tout ou rien : jamais de commande à moitié.</p>
                </div>
                <div class="card reveal" style="--d:.2s">
                    <div class="ico" style="--c:linear-gradient(135deg,#84cc16,#10b981);--s:rgba(16,185,129,.8)"><i class="bi bi-file-earmark-pdf-fill"></i></div>
                    <h3>Export PDF</h3>
                    <p>Chaque mouvement est tracé, filtrable, exportable.</p>
                </div>
                <div class="card wide reveal" style="--d:.1s">
                    <div class="ico" style="--c:linear-gradient(135deg,#8b5cf6,#ec4899)"><i class="bi bi-shield-lock-fill"></i></div>
                    <h3>Chacun son espace</h3>
                    <p>Admin, gestionnaire, fournisseur : chacun voit ce qui le concerne. Les curieux tombent sur une erreur 403 😏</p>
                    <span class="deco">🔐</span>
                </div>
                <div class="card wide reveal" style="--d:.2s">
                    <div class="ico" style="--c:linear-gradient(135deg,#f59e0b,#ec4899);--s:rgba(245,158,11,.8)"><i class="bi bi-graph-up-arrow"></i></div>
                    <h3>Dashboards vivants</h3>
                    <p>Graphiques, compteurs animés, palette Ctrl + K… et des confettis quand ça marche 🎉</p>
                    <span class="deco">📊</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Rôles --}}
    <section class="light" id="roles">
        <div class="wrap">
            <div class="center reveal">
                <span class="kicker">Les rôles</span>
                <h2 class="h2">Trois profils, <span class="grad-text">une seule équipe.</span></h2>
            </div>
            <div class="roles">
                <div class="role reveal" style="--c:linear-gradient(150deg,#6366f1,#8b5cf6);--s:rgba(99,102,241,.9)">
                    <span class="emoji">🏭</span><h3>Administrateur</h3>
                    <ul><li>Pilote le dépôt central</li><li>Commande aux fournisseurs</li><li>Valide les commandes des magasins</li><li>Gère les comptes</li></ul>
                </div>
                <div class="role reveal" style="--c:linear-gradient(150deg,#ec4899,#f43f5e);--s:rgba(236,72,153,.9);--d:.1s">
                    <span class="emoji" style="animation-delay:-1s">🚚</span><h3>Fournisseur</h3>
                    <ul><li>Gère son catalogue</li><li>Reçoit les commandes</li><li>Confirme puis expédie</li><li>Suit chaque livraison</li></ul>
                </div>
                <div class="role reveal" style="--c:linear-gradient(150deg,#06b6d4,#10b981);--s:rgba(6,182,212,.9);--d:.2s">
                    <span class="emoji" style="animation-delay:-2s">🏪</span><h3>Gestionnaire</h3>
                    <ul><li>Gère son magasin</li><li>Commande au dépôt</li><li>Enregistre ventes et casses</li><li>Reçoit ses alertes</li></ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Flux --}}
    <section class="light alt" id="flux">
        <div class="wrap">
            <div class="center reveal">
                <span class="kicker">Le flux</span>
                <h2 class="h2">Du fournisseur au rayon, <span class="grad-text">en 6 étapes.</span></h2>
            </div>
            <div class="flow">
                <div class="step reveal"><div class="num">🧾</div><b>1. Catalogue</b><small>le fournisseur publie</small></div>
                <div class="step reveal" style="--d:.08s"><div class="num">🛒</div><b>2. Commande</b><small>l'admin commande</small></div>
                <div class="step reveal" style="--d:.16s"><div class="num">🚚</div><b>3. Expédition</b><small>le fournisseur envoie</small></div>
                <div class="step reveal" style="--d:.24s"><div class="num">📦</div><b>4. Réception</b><small>stock du dépôt ↑</small></div>
                <div class="step reveal" style="--d:.32s"><div class="num">🏪</div><b>5. Magasin</b><small>commande validée</small></div>
                <div class="step reveal" style="--d:.4s"><div class="num">✅</div><b>6. Rayon</b><small>stock du magasin ↑</small></div>
            </div>
            <div class="phase reveal"><span>Phase 1 · Approvisionnement</span><span>Phase 2 · Distribution</span></div>
            <div class="stack reveal">
                <span>🐘 PHP 8.2</span><span>🔺 Laravel 12</span><span>🐬 MySQL</span><span>🅱️ Bootstrap 5</span><span>📈 Chart.js</span><span>🧪 Pest</span>
            </div>
        </div>
    </section>

    {{-- Appel à l'action --}}
    <section class="light" style="padding-top:40px">
        <div class="wrap">
            <div class="final reveal">
                <h2>Prêt à faire couler ton stock ? 🌊</h2>
                <p>Connecte-toi et vois ton stock bouger en direct.</p>
                <div class="cta" style="justify-content:center;margin:0">
                    <a href="{{ route('login') }}" class="btn btn-white"><i class="bi bi-box-arrow-in-right"></i> Se connecter</a>
                    <a href="{{ route('register') }}" class="btn btn-ghost"><i class="bi bi-person-plus"></i> Créer un compte</a>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="wrap">
            <a href="{{ route('home') }}" class="brand"><img src="{{ asset('images/stockflow-logo.svg') }}" alt="">StockFlow</a>
            <div class="school"><img src="{{ asset('images/adg.webp') }}" alt="ADG Education"> Projet de fin d'année DWWM 2026 · Fouad Bordjah</div>
            <span>Fait avec 💜 en Laravel 12</span>
        </div>
    </footer>

    <script>
        (function () {
            const calme = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            /** Barre du haut qui devient opaque + barre de progression. */
            const top = document.getElementById('top');
            const progres = document.getElementById('progress');
            const auDefilement = () => {
                top.classList.toggle('scrolled', window.scrollY > 30);
                const max = document.documentElement.scrollHeight - window.innerHeight;
                progres.style.width = (max > 0 ? (window.scrollY / max) * 100 : 0) + '%';
            };
            window.addEventListener('scroll', auDefilement, { passive: true });
            auDefilement();

            /** Mot qui s'écrit et s'efface tout seul. */
            const mots = ['en temps réel', 'sans rupture', 'sans paperasse', 'en mode fluide', 'sous contrôle'];
            const cible = document.getElementById('rot');
            if (!calme) {
                let mot = 0, lettre = mots[0].length, efface = true;
                const tick = () => {
                    const actuel = mots[mot];
                    lettre += efface ? -1 : 1;
                    cible.textContent = actuel.slice(0, lettre);
                    let delai = efface ? 45 : 85;
                    if (!efface && lettre === actuel.length) { efface = true; delai = 1800; }
                    else if (efface && lettre === 0) { efface = false; mot = (mot + 1) % mots.length; delai = 300; }
                    setTimeout(tick, delai);
                };
                setTimeout(tick, 2200);
            }

            /** Apparition des blocs et compteurs quand ils arrivent à l'écran. */
            const compter = (element) => {
                const fin = parseInt(element.dataset.count, 10);
                const suffixe = element.dataset.suffix || '';
                if (calme) { element.textContent = fin + suffixe; return; }
                const debut = performance.now();
                const etape = (maintenant) => {
                    const p = Math.min((maintenant - debut) / 1400, 1);
                    element.textContent = Math.round(fin * (1 - Math.pow(1 - p, 3))) + suffixe;
                    if (p < 1) { requestAnimationFrame(etape); }
                };
                requestAnimationFrame(etape);
            };
            const observateur = new IntersectionObserver((entrees) => {
                entrees.forEach((entree) => {
                    if (!entree.isIntersecting) { return; }
                    entree.target.classList.add('in');
                    entree.target.addEventListener('transitionend', () => entree.target.classList.remove('reveal'), { once: true });
                    entree.target.querySelectorAll('[data-count]').forEach(compter);
                    observateur.unobserve(entree.target);
                });
            }, { threshold: 0.15 });
            document.querySelectorAll('.reveal').forEach((element) => observateur.observe(element));

            /** Effet de profondeur : le faux tableau de bord suit la souris. */
            const zone = document.getElementById('mock-zone');
            if (!calme && window.matchMedia('(pointer: fine)').matches) {
                document.querySelector('.hero').addEventListener('mousemove', (evenement) => {
                    const x = evenement.clientX / window.innerWidth - 0.5;
                    const y = evenement.clientY / window.innerHeight - 0.5;
                    zone.querySelectorAll('[data-depth]').forEach((couche) => {
                        const profondeur = parseFloat(couche.dataset.depth);
                        couche.style.transform = `translate(${x * profondeur}px, ${y * profondeur}px)`;
                    });
                });
            }
        })();
    </script>
</body>
</html>
