<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">Dashboard Admin</h2>
        <button wire:click="refresh" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Refresh
        </button>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Siswa</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['total_siswa'] }}</p>
                </div>
                <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Total Guru</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['total_guru'] }}</p>
                </div>
                <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Total Kelas</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['total_kelas'] }}</p>
                </div>
                <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Absensi Hari Ini</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['total_absensi_hari_ini'] }}</p>
                </div>
                <div class="bg-orange-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Today's Attendance Breakdown --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Absensi Hari Ini</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                            <span class="text-gray-700">Hadir</span>
                        </div>
                        <span class="font-semibold text-gray-900">{{ $absensiHariIni['hadir'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                            <span class="text-gray-700">Izin</span>
                        </div>
                        <span class="font-semibold text-gray-900">{{ $absensiHariIni['izin'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                            <span class="text-gray-700">Sakit</span>
                        </div>
                        <span class="font-semibold text-gray-900">{{ $absensiHariIni['sakit'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                            <span class="text-gray-700">Alpha</span>
                        </div>
                        <span class="font-semibold text-gray-900">{{ $absensiHariIni['alpha'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Activities --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Aktivitas Terbaru</h3>
            </div>
            <div class="p-6">
                @if(count($recentActivities) > 0)
                    <div class="space-y-4">
                        @foreach($recentActivities as $activity)
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    @php
                                        $colors = [
                                            'hadir' => 'bg-green-100 text-green-600',
                                            'izin' => 'bg-yellow-100 text-yellow-600',
                                            'sakit' => 'bg-blue-100 text-blue-600',
                                            'alpha' => 'bg-red-100 text-red-600',
                                        ];
                                    @endphp
                                    <div
                                        class="w-8 h-8 rounded-full {{ $colors[$activity['status']] }} flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm text-gray-900">
                                        <span class="font-medium">{{ $activity['siswa'] }}</span>
                                        <span class="text-gray-600"> - {{ ucfirst($activity['status']) }}</span>
                                    </p>
                                    <p class="text-xs text-gray-500">oleh {{ $activity['pencatat'] }}
                                        • {{ $activity['waktu'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-600 text-center py-8">Belum ada aktivitas</p>
                @endif
            </div>
        </div>
    </div>
</div>
