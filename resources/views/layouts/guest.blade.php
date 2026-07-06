<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'LaporanKerja') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col justify-center items-center px-4 py-10 bg-slate-950">
            <div class="mb-2">
                <a href="/">
                    <span class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-teal-500 text-xl font-bold text-white">LK</span>
                </a>
            </div>
            <p class="mb-6 text-sm text-slate-400">Sistem Laporan Kerja Bulanan</p>
            <div class="w-full sm:max-w-md px-7 py-7 bg-white shadow-2xl overflow-hidden rounded-2xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
