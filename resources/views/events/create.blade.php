@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Buat Event Baru</h1>

    <form action="{{ route('events.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700">Judul Event</label>
            <input type="text" name="title" value="{{ old('title') }}" class="mt-1 w-full border border-gray-300 rounded px-3 py-2">
            @error('title') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
            <textarea name="description" rows="4" class="mt-1 w-full border border-gray-300 rounded px-3 py-2">{{ old('description') }}</textarea>
            @error('description') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Tanggal & Waktu</label>
                <input type="datetime-local" name="event_date" value="{{ old('event_date') }}" class="mt-1 w-full border border-gray-300 rounded px-3 py-2">
                @error('event_date') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Lokasi</label>
                <input type="text" name="location" value="{{ old('location') }}" class="mt-1 w-full border border-gray-300 rounded px-3 py-2">
                @error('location') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori Event (Bisa pilih lebih dari satu)</label>
            <div class="grid grid-cols-2 gap-2 bg-gray-50 p-3 rounded border">
                @foreach($categories as $category)
                    <label class="inline-flex items-center space-x-2 text-sm">
                        <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                            {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                        <span>{{ $category->name }}</span>
                    </label>
                @endforeach
            </div>
            @error('categories') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end space-x-2 pt-2">
            <a href="{{ route('events.index') }}" class="px-4 py-2 border rounded text-sm text-gray-600">Batal</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm font-semibold">
                Simpan Event
            </button>
        </div>
    </form>
</div>
@endsection