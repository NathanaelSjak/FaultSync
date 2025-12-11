@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto mt-10">

    <h1 class="text-2xl font-bold mb-6">Detail Kategori</h1>

    <div class="border p-4 rounded bg-white shadow">
        <p class="mb-2">
            <strong>Nama:</strong> {{ $category->name }}
        </p>

        <p class="mb-2">
            <strong>Tipe:</strong> 
            <span class="capitalize">{{ $category->type }}</span>
        </p>
    </div>

    <a href="{{ url('/categories') }}" 
       class="mt-4 inline-block text-blue-600">
       ← Kembali
    </a>

</div>
@endsection