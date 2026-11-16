@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Kelola Kategori Event</h1>

    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <form action="{{ route('categories.store') }}" method="POST" class="flex gap-3">
            @csrf
            <input type="text" name="name" placeholder="Nama kategori baru..." class="flex-grow border rounded px-3 py-2 text-sm">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm font-semibold">
                + Tambah
            </button>
        </form>
        @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-left border-collapse text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="p-3 font-semibold text-gray-700">Nama Kategori</th>
                    <th class="p-3 font-semibold text-gray-700">Slug</th>
                    <th class="p-3 font-semibold text-gray-700">Jumlah Event</th>
                    <th class="p-3 font-semibold text-gray-700 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($categories as $category)
                    <tr>
                        <td class="p-3 font-medium text-gray-800">{{ $category->name }}</td>
                        <td class="p-3 text-gray-500">{{ $category->slug }}</td>
                        <td class="p-3 text-gray-600">{{ $category->events_count }} event</td>
                        <td class="p-3 text-right">
                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline text-xs font-semibold">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection