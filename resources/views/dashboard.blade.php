@extends('layouts.app')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Selamat Datang, {{ auth()->user()->name }}!</h1>
    <p class="text-gray-600 mb-4">
        Anda login sebagai <span class="font-semibold uppercase text-indigo-600">{{ auth()->user()->role }}</span>.
    </p>

    <div class="flex flex-wrap gap-3">
        <a href="/events" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm font-medium">
            Lihat Daftar Event
        </a>
        @if(auth()->user()->isAdmin())
            <a href="/admin-area" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded text-sm font-medium">
                Tes Halaman Khusus Admin
            </a>
        @endif
    </div>
</div>
@endsection