<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title.' · ' : '' }}{{ config('app.name', 'LaporanKerja') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div
            x-data="{
                sidebarOpen: false,
                sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
                toggleSidebar() {
                    if (window.innerWidth < 1024) {
                        this.sidebarOpen = !this.sidebarOpen;
                    } else {
                        this.sidebarCollapsed = !this.sidebarCollapsed;
                        localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
                    }
                }
            }"
            @keydown.escape.window="sidebarOpen = false"
            class="min-h-screen bg-slate-50"
        >
            @include('layouts.navigation')

            @if(session('success'))
                <div
                    x-data="{ show: true }"
                    x-init="setTimeout(() => show = false, 4500)"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="translate-y-3 opacity-0 sm:translate-x-6 sm:translate-y-0"
                    x-transition:enter-end="translate-x-0 translate-y-0 opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="translate-y-2 opacity-0 sm:translate-x-6 sm:translate-y-0"
                    class="fixed inset-x-4 top-20 z-[70] sm:left-auto sm:right-6 sm:w-full sm:max-w-sm lg:top-6"
                    role="status"
                    aria-live="polite"
                >
                    <div class="overflow-hidden rounded-2xl border border-emerald-200 bg-white shadow-xl shadow-slate-900/10">
                        <div class="flex items-start gap-3 p-4">
                            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12 4 4L19 6"/></svg>
                            </span>
                            <div class="min-w-0 flex-1 pt-0.5">
                                <p class="text-sm font-bold text-slate-900">Berhasil</p>
                                <p class="mt-1 text-sm leading-5 text-slate-600">{{ session('success') }}</p>
                            </div>
                            <button type="button" @click="show = false" class="rounded-lg p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600" aria-label="Tutup notifikasi">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="h-1 bg-emerald-500"></div>
                    </div>
                </div>
            @endif

            <div
                class="min-h-screen pt-16 transition-[padding] duration-300 lg:pt-0"
                :class="sidebarCollapsed ? 'lg:pl-20' : 'lg:pl-72'"
            >
                @isset($header)
                    <header class="border-b border-slate-200 bg-white">
                        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
        @stack('scripts')
    </body>
</html>
