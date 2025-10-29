{{-- Sidebar tanpa Bootstrap JS --}}
<div class="bg-dark text-white p-3" style="width: 190px; min-height: 100vh;">
    <ul class="nav flex-column">
        <!-- Dashboard -->
        <li class="mb-2">
            <h4>
                <a href="{{ url('/admin/dashboard') }}"
                   class="d-block px-4 py-2 rounded text-white text-decoration-none fs-5 }}">
                    Dashboard
                </a>
            </h4>
        </li>

        {{-- ======================
             COMMON: Layout
        ======================= --}}
        <li class="nav-item mb-2" 
            x-data="{ open: {{ request()->is('admin/banner*') || request()->is('admin/artikel*') ? 'true' : 'false' }} }">
            <button @click="open = !open"
                class="nav-link text-white w-100 d-flex justify-content-between align-items-center border-0 bg-transparent" 
                style="font-size: 15px;">
                Layout
                <span :class="open ? 'rotate-180' : ''" class="transition-transform">&#9662;</span>
            </button>

            <div x-show="open" class="mt-2 ms-3" x-transition>
                <ul class="nav flex-column">
                    <li class="nav-item mb-1">
                        <a href="{{ url('/admin/banner') }}"
                           class="nav-link text-white {{ request()->is('admin/banner') ? 'active text-dark' : '' }}" 
                           style="font-size: 12px;">
                            Banner
                        </a>
                    </li>
                    <li class="nav-item mb-1">
                        <a href="{{ url('/admin/artikel') }}"
                           class="nav-link text-white {{ request()->is('admin/artikel') ? 'active text-dark' : '' }}" 
                           style="font-size: 12px;">
                            Artikel
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        {{-- ======================
             FOR KONSELOR: Data
        ======================= --}}
        @if(Auth::user()->role === 'konselor')
            <li class="nav-item mb-2" 
                x-data="{ open: {{ request()->is('admin/konseling*') || request()->is('admin/laporan*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="nav-link text-white w-100 d-flex justify-content-between align-items-center border-0 bg-transparent" 
                    style="font-size: 15px;">
                    Data
                    <span :class="open ? 'rotate-180' : ''" class="transition-transform">&#9662;</span>
                </button>

                <div x-show="open" class="mt-2 ms-3" x-transition>
                    <ul class="nav flex-column">
                        <li class="nav-item mb-1">
                            <a href="{{ url('/admin/pesan/settings') }}"
                               class="nav-link text-white {{ request()->is('admin/pesan/settings') ? 'active text-dark' : '' }}" 
                               style="font-size: 12px;">
                                Pesan Otomatis
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a href="{{ route('admin.konseling.index') }}"
                               class="nav-link text-white {{ request()->is('admin/konseling*') ? 'active text-dark' : '' }}" 
                               style="font-size: 12px;">
                                Konseling
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a href="{{ url('/admin/laporan') }}"
                               class="nav-link text-white {{ request()->is('admin/laporan*') ? 'active text-dark' : '' }}" 
                               style="font-size: 12px;">
                                Laporan
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a href="{{ url('/admin/siswa') }}"
                               class="nav-link text-white {{ request()->is('admin/siswa*') ? 'active text-dark' : '' }}" 
                               style="font-size: 12px;">
                                Siswa
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        @endif

        {{-- ======================
             FOR ADMIN: Manajemen
        ======================= --}}
        @if(Auth::user()->role === 'admin')
            <li class="nav-item mb-2" 
                x-data="{ open: {{ request()->is('admin/report*') || request()->is('admin/konselor*') || request()->is('admin/user*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="nav-link text-white w-100 d-flex justify-content-between align-items-center border-0 bg-transparent" 
                    style="font-size: 15px;">
                    Manajemen
                    <span :class="open ? 'rotate-180' : ''" class="transition-transform">&#9662;</span>
                </button>

                <div x-show="open" class="mt-2 ms-3" x-transition>
                    <ul class="nav flex-column">
                        <li class="nav-item mb-1">
                            <a href="{{ url('/admin/report') }}"
                               class="nav-link text-white {{ request()->is('admin/report*') ? 'active text-dark' : '' }}" 
                               style="font-size: 12px;">
                                Reports
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a href="{{ url('/admin/konselor') }}"
                               class="nav-link text-white {{ request()->is('admin/konselor*') ? 'active text-dark' : '' }}" 
                               style="font-size: 12px;">
                                Konselor
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a href="{{ url('/admin/user') }}"
                               class="nav-link text-white {{ request()->is('admin/user*') ? 'active text-dark' : '' }}" 
                               style="font-size: 12px;">
                                Akun
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        @endif
    </ul>
</div>

{{-- Alpine.js --}}
<script src="//unpkg.com/alpinejs" defer></script>
