{{-- Mobile top bar --}}
<header class="fixed inset-x-0 top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 bg-white px-4 lg:hidden">
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-teal-700 text-sm font-bold text-white">LK</span>
        <span class="font-bold tracking-tight text-slate-900">LaporanKerja</span>
    </a>
    <button
        type="button"
        @click="sidebarOpen = true"
        class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-slate-600 transition hover:bg-slate-100"
        aria-label="Buka sidebar"
    >
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>
</header>

{{-- Mobile backdrop --}}
<div
    x-cloak
    x-show="sidebarOpen"
    x-transition.opacity
    @click="sidebarOpen = false"
    class="fixed inset-0 z-40 bg-slate-950/45 backdrop-blur-sm lg:hidden"
></div>

{{-- Sidebar --}}
<aside
    class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col border-r border-slate-200 bg-white shadow-xl transition-all duration-300 lg:translate-x-0 lg:shadow-none"
    :class="[
        sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
        sidebarCollapsed ? 'lg:w-20' : 'lg:w-72'
    ]"
>
    <div class="flex h-20 shrink-0 items-center border-b border-slate-100 px-5" :class="sidebarCollapsed ? 'lg:justify-center lg:px-3' : 'justify-between'">
        <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-3">
            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-teal-700 text-sm font-bold text-white shadow-sm shadow-teal-700/20">LK</span>
            <span :class="sidebarCollapsed ? 'lg:hidden' : ''" class="truncate font-bold tracking-tight text-slate-900">LaporanKerja</span>
        </a>
        <button
            type="button"
            @click="sidebarOpen = false"
            class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 lg:hidden"
            aria-label="Tutup sidebar"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <nav class="flex-1 space-y-1.5 overflow-y-auto px-3 py-6">
        <p :class="sidebarCollapsed ? 'lg:hidden' : ''" class="mb-3 px-3 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Menu Utama</p>

        <a
            href="{{ route('dashboard') }}"
            @click="sidebarOpen = false"
            title="Dashboard"
            class="flex h-12 items-center gap-3 rounded-xl px-3 text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-teal-50 text-teal-800' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
            :class="sidebarCollapsed ? 'lg:justify-center' : ''"
        >
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10.5 12 3l9 7.5V21a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1V10.5Z"/></svg>
            <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Dashboard</span>
        </a>

        <a
            href="{{ route('work-reports.index') }}"
            @click="sidebarOpen = false"
            title="Laporan"
            class="flex h-12 items-center gap-3 rounded-xl px-3 text-sm font-medium transition {{ request()->routeIs('work-reports.*') ? 'bg-teal-50 text-teal-800' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
            :class="sidebarCollapsed ? 'lg:justify-center' : ''"
        >
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 3h7l5 5v13H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/><path stroke-linecap="round" stroke-width="1.8" d="M14 3v6h5M9 13h6M9 17h6"/></svg>
            <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Laporan</span>
        </a>

        @if(auth()->user()->isAdmin())
            <a
                href="{{ route('admin.users.index') }}"
                @click="sidebarOpen = false"
                title="Pengguna"
                class="flex h-12 items-center gap-3 rounded-xl px-3 text-sm font-medium transition {{ request()->routeIs('admin.*') ? 'bg-teal-50 text-teal-800' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                :class="sidebarCollapsed ? 'lg:justify-center' : ''"
            >
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Pengguna</span>
            </a>
        @endif
    </nav>

    <div class="shrink-0 border-t border-slate-100 p-3">
        <a
            href="{{ route('profile.edit') }}"
            title="Profil"
            class="mb-2 flex items-center gap-3 rounded-xl p-3 transition hover:bg-slate-50"
            :class="sidebarCollapsed ? 'lg:justify-center' : ''"
        >
            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-sm font-bold text-slate-600">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
            <span :class="sidebarCollapsed ? 'lg:hidden' : ''" class="min-w-0 flex-1">
                <span class="block truncate text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</span>
                <span class="block text-xs capitalize text-slate-500">{{ auth()->user()->role }}</span>
            </span>
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                title="Keluar"
                class="flex h-11 w-full items-center gap-3 rounded-xl px-3 text-sm font-medium text-rose-600 transition hover:bg-rose-50"
                :class="sidebarCollapsed ? 'lg:justify-center' : ''"
            >
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 17l5-5-5-5M15 12H3M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/></svg>
                <span :class="sidebarCollapsed ? 'lg:hidden' : ''">Keluar</span>
            </button>
        </form>
    </div>

    {{-- Desktop collapse control --}}
    <button
        type="button"
        @click="toggleSidebar()"
        class="absolute -right-4 top-24 hidden h-8 w-8 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 shadow-sm transition hover:border-teal-200 hover:text-teal-700 lg:flex"
        :aria-label="sidebarCollapsed ? 'Buka sidebar' : 'Tutup sidebar'"
    >
        <svg class="h-4 w-4 transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 18-6-6 6-6"/></svg>
    </button>
</aside>
