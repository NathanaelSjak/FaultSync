@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto mt-10">

    <h1 class="text-2xl font-bold mb-6">Tambah Kategori</h1>

    <form action="{{ route('categories.create') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="font-semibold">Nama Kategori</label>
            <input type="text" name="name" class="border w-full px-3 py-2 rounded" required>
        </div>

        <div>
            <label class="font-semibold">Tipe</label>
            <select name="type" class="border w-full px-3 py-2 rounded">
                <option value="income">Pemasukan</option>
                <option value="expense">Pengeluaran</option>
            </select>
        </div>

        <button class="bg-green-600 text-white px-4 py-2 rounded">Simpan</button>
        <a href="{{ url('/categories') }}" class="ml-2 text-gray-600">Batal</a>
    </form>

</div>
@endsection