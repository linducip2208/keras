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
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '#fff7ed', 100: '#ffedd5', 200: '#fed7aa',
                            300: '#fdba74', 400: '#fb923c', 500: '#f97316',
                            600: '#ea580c', 700: '#c2410c', 800: '#9a3412', 900: '#7c2d12',
                        },
                    },
                },
            },
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-white text-gray-900 antialiased">

    {{-- Navbar --}}
    <nav class="fixed top-0 inset-x-0 z-50 backdrop-blur-md bg-white/80 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 font-bold text-xl">
                <span class="w-9 h-9 bg-brand-500 rounded-lg flex items-center justify-center text-white">H</span>
                <span>HRIS</span>
            </a>
            <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-700">
                <a href="/#features" class="hover:text-brand-600">Fitur</a>
                <a href="/#pricing" class="hover:text-brand-600">Harga</a>
                <a href="/#demo" class="hover:text-brand-600">Demo</a>
                <a href="/best-aplikasi-hris-2026" class="hover:text-brand-600">Bandingkan</a>
                <a href="/admin/login" class="hover:text-brand-600">Masuk</a>
            </div>
            <a href="/admin/login" class="bg-brand-600 hover:bg-brand-700 text-white px-5 py-2 rounded-lg text-sm font-semibold shadow-sm">Coba Demo Gratis</a>
        </div>
    </nav>

    <main class="pt-16">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-950 text-gray-400 mt-24">
        <div class="max-w-7xl mx-auto px-6 py-16 grid grid-cols-2 md:grid-cols-5 gap-8">
            <div class="col-span-2">
                <div class="flex items-center gap-2 font-bold text-xl text-white mb-3">
                    <span class="w-9 h-9 bg-brand-500 rounded-lg flex items-center justify-center text-white">H</span>
                    HRIS
                </div>
                <p class="text-sm text-gray-400 max-w-xs">HRIS Indonesia all-in-one. Payroll PPh 21 TER, BPJS otomatis. BYOK provider, license-once tersedia.</p>
                <div class="mt-4 flex gap-3">
                    <span class="px-2 py-1 text-xs bg-gray-800 rounded">UU PDP Compliant</span>
                    <span class="px-2 py-1 text-xs bg-gray-800 rounded">ISO 27001-ready</span>
                </div>
            </div>
            <div>
                <h4 class="font-semibold text-white mb-3">Produk</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="/#features" class="hover:text-white">Fitur</a></li>
                    <li><a href="/#pricing" class="hover:text-white">Harga</a></li>
                    <li><a href="/admin/login" class="hover:text-white">Login Demo</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-white mb-3">Solusi</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="/best-aplikasi-hris-untuk-manufaktur" class="hover:text-white">Manufaktur</a></li>
                    <li><a href="/best-aplikasi-hris-untuk-f-b" class="hover:text-white">F&B & Retail</a></li>
                    <li><a href="/best-aplikasi-hris-untuk-startup" class="hover:text-white">Startup & Tech</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-white mb-3">Bandingkan</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="/compare/hris-vs-talenta" class="hover:text-white">vs Mekari Talenta</a></li>
                    <li><a href="/compare/hris-vs-gadjian" class="hover:text-white">vs Gadjian</a></li>
                    <li><a href="/alternatives-to-kantorku" class="hover:text-white">Alternatif KantorKu</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-800 py-6 text-center text-xs text-gray-500">
            © {{ date('Y') }} HRIS Indonesia. Dibuat di Indonesia 🇮🇩. License v3 dilindungi whitelabel.co.id.
        </div>
    </footer>

</body>
</html>
