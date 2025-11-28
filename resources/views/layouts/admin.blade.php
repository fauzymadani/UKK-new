<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - Sistem Absensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>
<body class="bg-gray-100">
<div class="flex h-screen">
    {{-- Sidebar --}}
    <aside class="w-64 bg-gray-900 text-white flex-shrink-0 overflow-y-auto">
        <div class="p-6">
            <h1 class="text-2xl font-bold">Panel Admin</h1>
            <p class="text-gray-400 text-sm mt-1">{{ Auth::user()->name }}</p>
        </div>

        <nav class="mt-6">
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center px-6 py-3 hover:bg-gray-800 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 border-l-4 border-indigo-500' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('admin.manage-users') }}"
               class="flex items-center px-6 py-3 hover:bg-gray-800 {{ request()->routeIs('admin.manage-users') ? 'bg-gray-800 border-l-4 border-indigo-500' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Kelola Users
            </a>

            <a href="{{ route('admin.manage-kelas') }}"
               class="flex items-center px-6 py-3 hover:bg-gray-800 {{ request()->routeIs('admin.manage-kelas') ? 'bg-gray-800 border-l-4 border-indigo-500' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Kelola Kelas
            </a>

            <a href="{{ route('admin.manage-mata-pelajaran') }}"
               class="flex items-center px-6 py-3 hover:bg-gray-800 {{ request()->routeIs('admin.manage-mata-pelajaran') ? 'bg-gray-800 border-l-4 border-indigo-500' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                Mata Pelajaran
            </a>

            <a href="{{ route('admin.manage-jadwal') }}"
               class="flex items-center px-6 py-3 hover:bg-gray-800 {{ request()->routeIs('admin.manage-jadwal') ? 'bg-gray-800 border-l-4 border-indigo-500' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Jadwal Pelajaran
            </a>

            <a href="{{ route('admin.laporan-absensi') }}"
               class="flex items-center px-6 py-3 hover:bg-gray-800 {{ request()->routeIs('admin.laporan-absensi') ? 'bg-gray-800 border-l-4 border-indigo-500' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Laporan Absensi
            </a>
        </nav>

        <div class="absolute bottom-0 w-64 p-6">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center text-gray-400 hover:text-white">
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

            @if (session()->has('info'))
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4" role="alert">
                    <span class="block sm:inline">{{ session('info') }}</span>
                </div>
            @endif

            {{-- Page Content --}}
            {{ $slot }}
        </div>
    </main>
</div>

@livewireScripts
</body>
</html>
