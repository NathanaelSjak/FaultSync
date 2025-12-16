@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto mt-10">

    <h1 class="text-2xl font-bold mb-6">Edit Kategori</h1>

    <form action="{{ url('/categories/edit/'.$category->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="font-semibold">Nama Kategori</label>
            <input 
                type="text" 
                name="name" 
                class="border w-full px-3 py-2 rounded"
                value="{{ $category->name }}"
                required
            >
        </div>

        <button class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
        <a href="{{ url('/categories') }}" class="ml-2 text-gray-600">Batal</a>
    </form>

</div>
@endsection