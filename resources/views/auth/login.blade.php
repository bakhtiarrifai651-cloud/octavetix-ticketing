@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Login Akun</h2>

    <form action="/login" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" value="{{ old('email') }}"
                class="mt-1 w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-indigo-500">
            @error('email')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" name="password"
                class="mt-1 w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-indigo-500">
            @error('password')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 rounded">
            Masuk
        </button>
    </form>

    <div class="mt-4 p-3 bg-gray-50 rounded text-xs text-gray-600 border">
        <p class="font-semibold mb-1">Akun Uji Coba (Seeder):</p>
        <p>Admin: <code>admin@amikom.ac.id</code> | Pass: <code>password</code></p>
        <p>Peserta: <code>peserta@amikom.ac.id</code> | Pass: <code>password</code></p>
    </div>
</div>
@endsection