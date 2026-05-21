<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'HRIS — Sistem Manajemen SDM Indonesia')</title>
    <meta name="description" content="@yield('description', 'HRIS Indonesia all-in-one: payroll PPh 21 TER, BPJS otomatis, attendance via WhatsApp, BYOK provider AI. SaaS atau license-once self-host.')">
    <meta name="keywords" content="@yield('keywords', 'HRIS Indonesia, software payroll, aplikasi absensi, BPJS, PPh 21 TER, HR Indonesia')">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph --}}
    <meta property="og:title" content="@yield('title', 'HRIS Indonesia')">
    <meta property="og:description" content="@yield('description', 'Payroll PPh 21 TER & BPJS otomatis, ESS WhatsApp, BYOK provider')">
    <meta property="og:image" content="{{ asset('og-image.png') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'HRIS Indonesia')">
    <meta name="twitter:description" content="@yield('description', 'HRIS Indonesia')">

    {{-- JSON-LD --}}
    @stack('jsonld')

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Inter', 'system-ui', 'sans-serif'],
                        display: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                    },
                    colors: {
                        // Candy palette — soft pastel multi-color
                        brand: { // primary (peach/orange)
                            50: '#fff7ed', 100: '#ffedd5', 200: '#fed7aa',
                            300: '#fdba74', 400: '#fb923c', 500: '#f97316',
                            600: '#ea580c', 700: '#c2410c', 800: '#9a3412', 900: '#7c2d12',
                        },
                        candy: {
                            pink:   { 50:'#fdf2f8', 100:'#fce7f3', 200:'#fbcfe8', 300:'#f9a8d4', 400:'#f472b6', 500:'#ec4899', 600:'#db2777' },
                            purple: { 50:'#faf5ff', 100:'#f3e8ff', 200:'#e9d5ff', 300:'#d8b4fe', 400:'#c084fc', 500:'#a855f7', 600:'#9333ea' },
                            blue:   { 50:'#eff6ff', 100:'#dbeafe', 200:'#bfdbfe', 300:'#93c5fd', 400:'#60a5fa', 500:'#3b82f6', 600:'#2563eb' },
                            mint:   { 50:'#ecfdf5', 100:'#d1fae5', 200:'#a7f3d0', 300:'#6ee7b7', 400:'#34d399', 500:'#10b981', 600:'#059669' },
                            yellow: { 50:'#fefce8', 100:'#fef9c3', 200:'#fef08a', 300:'#fde047', 400:'#facc15', 500:'#eab308', 600:'#ca8a04' },
                            cream:  { 50:'#fffbeb', 100:'#fef3c7', 200:'#fde68a', 300:'#fcd34d', 400:'#fbbf24', 500:'#f59e0b' },
                        },
                    },
                    backgroundImage: {
                        'candy-soft':    'linear-gradient(135deg, #fdf2f8 0%, #faf5ff 25%, #eff6ff 50%, #ecfdf5 75%, #fefce8 100%)',
                        'candy-hero':    'linear-gradient(135deg, #fce7f3 0%, #f3e8ff 30%, #ffedd5 70%, #fef3c7 100%)',
                        'candy-pink':    'linear-gradient(135deg, #fdf2f8 0%, #fce7f3 100%)',
                        'candy-mint':    'linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%)',
                        'candy-peach':   'linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%)',
                        'candy-lavender':'linear-gradient(135deg, #faf5ff 0%, #e9d5ff 100%)',
                        'candy-sunshine':'linear-gradient(135deg, #fefce8 0%, #fef3c7 100%)',
                        'candy-sky':     'linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%)',
                        'candy-pop':     'linear-gradient(135deg, #f472b6 0%, #c084fc 50%, #fb923c 100%)',
                    },
                    boxShadow: {
                        'candy':       '0 8px 32px -4px rgba(236, 72, 153, 0.15), 0 2px 8px -2px rgba(168, 85, 247, 0.1)',
                        'candy-lg':    '0 20px 60px -10px rgba(236, 72, 153, 0.25), 0 4px 16px -2px rgba(168, 85, 247, 0.15)',
                        'candy-glow':  '0 0 40px rgba(236, 72, 153, 0.3)',
                    },
                },
            },
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
        /* Decorative blobs */
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.4;
            pointer-events: none;
            z-index: 0;
        }
        .blob-pink { background: #f9a8d4; }
        .blob-purple { background: #d8b4fe; }
        .blob-yellow { background: #fde047; }
        .blob-mint { background: #6ee7b7; }
        .blob-peach { background: #fdba74; }
        @keyframes float-slow { 0%,100%{transform:translate(0,0)} 50%{transform:translate(15px,-20px)} }
        .animate-float { animation: float-slow 6s ease-in-out infinite; }
    </style>
</head>
<body class="bg-candy-soft text-gray-900 antialiased min-h-screen">

    {{-- Navbar --}}
    <nav class="fixed top-0 inset-x-0 z-50 backdrop-blur-xl bg-white/70 border-b border-candy-pink-100/50">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 font-extrabold text-xl">
                <span class="w-9 h-9 bg-candy-pop rounded-xl flex items-center justify-center text-white shadow-candy">H</span>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-candy-pink-500 via-candy-purple-500 to-brand-500">HRIS</span>
            </a>
            <div class="hidden md:flex items-center gap-8 text-sm font-semibold text-gray-700">
                <a href="/#features" class="hover:text-candy-pink-600 transition">Fitur</a>
                <a href="/#pricing" class="hover:text-candy-pink-600 transition">Harga</a>
                <a href="/#demo" class="hover:text-candy-pink-600 transition">Demo</a>
                <a href="/karir" class="hover:text-candy-pink-600 transition flex items-center gap-1">
                    Karir
                    <span class="px-1.5 py-0.5 text-[10px] bg-candy-mint-200 text-candy-mint-600 rounded-full font-bold">HIRING</span>
                </a>
                <a href="/best-aplikasi-hris-2026" class="hover:text-candy-pink-600 transition">Bandingkan</a>
                <a href="/admin/login" class="hover:text-candy-pink-600 transition">Masuk</a>
            </div>
            <a href="/admin/login" class="bg-gradient-to-r from-candy-pink-500 via-candy-purple-500 to-brand-500 hover:shadow-candy-lg text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-candy transition transform hover:-translate-y-0.5">
                Coba Demo Gratis ✨
            </a>
        </div>
    </nav>

    <main class="pt-16 relative">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="relative mt-24 bg-gradient-to-br from-candy-purple-50 via-candy-pink-50 to-brand-50 border-t border-candy-pink-100">
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="blob blob-pink w-96 h-96 -top-20 -left-20"></div>
            <div class="blob blob-purple w-96 h-96 -bottom-20 -right-20"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-6 py-16 grid grid-cols-2 md:grid-cols-5 gap-8">
            <div class="col-span-2">
                <div class="flex items-center gap-2 font-extrabold text-2xl mb-3">
                    <span class="w-10 h-10 bg-candy-pop rounded-xl flex items-center justify-center text-white shadow-candy">H</span>
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-candy-pink-500 via-candy-purple-500 to-brand-500">HRIS Indonesia</span>
                </div>
                <p class="text-sm text-gray-600 max-w-xs leading-relaxed">HRIS Indonesia all-in-one. Payroll PPh 21 TER, BPJS otomatis. BYOK provider, license-once tersedia.</p>
                <div class="mt-4 flex flex-wrap gap-2">
                    <span class="px-3 py-1 text-xs bg-candy-mint-100 text-candy-mint-600 rounded-full font-semibold">🔒 UU PDP</span>
                    <span class="px-3 py-1 text-xs bg-candy-blue-100 text-candy-blue-600 rounded-full font-semibold">🛡 ISO 27001-ready</span>
                    <span class="px-3 py-1 text-xs bg-candy-yellow-100 text-candy-yellow-600 rounded-full font-semibold">🇮🇩 Made in Indonesia</span>
                </div>
            </div>
            <div>
                <h4 class="font-bold text-gray-900 mb-3">Produk</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="/#features" class="text-gray-600 hover:text-candy-pink-600 transition">Fitur</a></li>
                    <li><a href="/#pricing" class="text-gray-600 hover:text-candy-pink-600 transition">Harga</a></li>
                    <li><a href="/admin/login" class="text-gray-600 hover:text-candy-pink-600 transition">Login Demo</a></li>
                    <li><a href="/__pair" class="text-gray-600 hover:text-candy-pink-600 transition">License Pairing</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-gray-900 mb-3">Solusi</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="/best-aplikasi-hris-untuk-manufaktur" class="text-gray-600 hover:text-candy-pink-600 transition">Manufaktur</a></li>
                    <li><a href="/best-aplikasi-hris-untuk-f-b" class="text-gray-600 hover:text-candy-pink-600 transition">F&B & Retail</a></li>
                    <li><a href="/best-aplikasi-hris-untuk-startup" class="text-gray-600 hover:text-candy-pink-600 transition">Startup & Tech</a></li>
                    <li><a href="/karir" class="text-gray-600 hover:text-candy-pink-600 transition font-semibold">🚀 Karir / Lowongan</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-gray-900 mb-3">Bandingkan</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="/compare/hris-vs-talenta" class="text-gray-600 hover:text-candy-pink-600 transition">vs Mekari Talenta</a></li>
                    <li><a href="/compare/hris-vs-gadjian" class="text-gray-600 hover:text-candy-pink-600 transition">vs Gadjian</a></li>
                    <li><a href="/compare/hris-vs-kantorku" class="text-gray-600 hover:text-candy-pink-600 transition">vs KantorKu</a></li>
                    <li><a href="/alternatives-to-kantorku" class="text-gray-600 hover:text-candy-pink-600 transition">Alternatif</a></li>
                </ul>
            </div>
        </div>
        <div class="relative border-t border-candy-pink-100/50 py-6 text-center text-xs text-gray-500">
            © {{ date('Y') }} HRIS Indonesia · Made with 💕 di Indonesia 🇮🇩 · License v3 dilindungi whitelabel.co.id
        </div>
    </footer>

</body>
</html>
