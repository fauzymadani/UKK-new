<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Guru - Sistem Absensi</title>

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Livewire Styles --}}
    @livewireStyles
</head>
<body class="bg-gray-100">
<div class="flex h-screen">
    {{-- Sidebar --}}
    <aside class="w-64 bg-indigo-800 text-white flex-shrink-0">
        <div class="p-6">
            <h1 class="text-2xl font-bold">Panel Guru</h1>
            <p class="text-indigo-200 text-sm mt-1">{{ Auth::user()->name }}</p>
        </div>

        <nav class="mt-6">
            <a href="{{ route('guru.dashboard') }}"
               class="flex items-center px-6 py-3 hover:bg-indigo-700 {{ request()->routeIs('guru.dashboard') ? 'bg-indigo-700 border-l-4 border-white' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('guru.input-absensi') }}"
               class="flex items-center px-6 py-3 hover:bg-indigo-700 {{ request()->routeIs('guru.input-absensi') ? 'bg-indigo-700 border-l-4 border-white' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                Input Absensi
            </a>

            <a href="{{ route('guru.rekap-absensi') }}"
               class="flex items-center px-6 py-3 hover:bg-indigo-700 {{ request()->routeIs('guru.rekap-absensi') ? 'bg-indigo-700 border-l-4 border-white' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Rekap Absensi
            </a>

            <a href="{{ route('guru.jadwal-mengajar') }}"
               class="flex items-center px-6 py-3 hover:bg-indigo-700 {{ request()->routeIs('guru.jadwal-mengajar') ? 'bg-indigo-700 border-l-4 border-white' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Jadwal Mengajar
            </a>
        </nav>

        <div class="absolute bottom-0 w-64 p-6">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center text-indigo-200 hover:text-white">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content --}}
    <main class="flex-1 overflow-y-auto">
        <div class="p-8">
            {{-- Flash Messages --}}
            @if (session()->has('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Page Content --}}
            {{ $slot }}
        </div>
    </main>
</div>

{{-- Livewire Scripts --}}
@livewireScripts
</body>
</html>
