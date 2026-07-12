<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="shortcut icon" href="{{ asset('images/sofi.png') }}" />
    <title>@yield('title', config('app.name'))</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        @theme {
            --color-teal:    #00c9a7;
            --color-teal-dk: #00a88a;
            --color-surface: #0d1f35;
            --color-surface2:#0a1929;
            --color-border:  #1a3a4a;
            --color-muted:   #64899a;
        }
    </style>
    <style>
        * { -webkit-tap-highlight-color: transparent; }
        @keyframes skeletonShimmer {
            0%   { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        .img-skeleton {
            position: absolute; inset: 0; z-index: 2;
            background: linear-gradient(90deg, #0d1f35 25%, #162f47 50%, #0d1f35 75%);
            background-size: 200% 100%;
            animation: skeletonShimmer 1.4s ease-in-out infinite;
            transition: opacity 0.35s ease;
        }
        .img-skeleton.fade-out { opacity: 0; pointer-events: none; }
        .img-lazy { opacity: 0; transition: opacity 0.35s ease; }
        .img-lazy.loaded { opacity: 1; }
        @keyframes ticker { from { transform: translateX(0); } to { transform: translateX(-50%); } }
        .ticker-track { animation: ticker 28s linear infinite; }
        .ticker-track:hover { animation-play-state: paused; }
    </style>
    @stack('styles')
</head>
<body class="bg-[#050f1a] text-[#e2f0ff] flex flex-col items-center min-h-screen max-w-150 mx-auto">
    @yield('content')
    @stack('scripts')
    <script>
        (function () {
            const skip = ['object-contain','w-4','w-5','w-6','fa-'];
            document.querySelectorAll('img').forEach(function (img) {
                const cls = img.className || '';
                if (skip.some(s => cls.includes(s))) return;
                if (img.getAttribute('data-no-skeleton') !== null) return;
                const parent = img.parentElement;
                const pos = window.getComputedStyle(parent).position;
                if (pos === 'static') parent.style.position = 'relative';
                const skel = document.createElement('div');
                skel.className = 'img-skeleton';
                parent.insertBefore(skel, img);
                img.classList.add('img-lazy');
                function reveal() {
                    img.classList.add('loaded');
                    skel.classList.add('fade-out');
                    setTimeout(function () { skel.remove(); }, 380);
                }
                if (img.complete && img.naturalWidth > 0) { reveal(); }
                else {
                    img.addEventListener('load',  reveal, { once: true });
                    img.addEventListener('error', reveal, { once: true });
                }
            });
        })();
    </script>
</body>
</html>
