@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto mt-10">
    <h1 class="text-2xl font-bold mb-6">Daftar Kategori</h1>

    {{-- Search --}}
    <form action="{{ route('categories.search') }}" method="GET" class="flex mb-5">
        <input 
            type="text" 
            name="search" 
            placeholder="Cari kategori..." 
            class="border rounded-l px-3 py-2 w-full"
        >
        <button class="bg-blue-600 text-white px-4 rounded-r">Cari</button>
    </form>

    {{-- Button Tambah --}}
    <a href="{{ url('/categories/create-view') }}" 
       class="bg-green-600 text-white px-4 py-2 rounded inline-block mb-4">
        + Tambah Kategori
    </a>

    {{-- Tabel --}}
    <table class="w-full border">
        <thead>
            <tr class="bg-gray-200 text-left">
                <th class="p-2">Nama</th>
                <th class="p-2">Tipe</th>
                <th class="p-2">Aksi</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($categories as $category)
            <tr class="border-t">
                <td class="p-2">{{ $category->name }}</td>
                <td class="p-2 capitalize">{{ $category->type }}</td>
                <td class="p-2">
                    <a href="{{ url('/categories/edit-view/'.$category->id) }}" 
                       class="text-blue-600 mr-3">
                       Edit
                    </a>

                    <form action="{{ url('/categories/remove/'.$category->id) }}" 
                          method="POST" 
                          class="inline-block"
                          onsubmit="return confirm('Hapus kategori ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-600">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection