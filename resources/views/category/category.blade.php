@extends('layouts.app')

@section('content')
<div class="category-box">
    <div class="category-header">
        <h2>Category Library</h2>

        <div class="actions">
            <button class="btn add">Add Category</button>
            <button class="btn edit">Edit Category</button>
            <button class="btn remove">Remove Category</button>
            <input type="text" id="search" placeholder="Search...">
        </div>
    </div>

    <div class="category-grid">
        @foreach ($categories as $category)
            <div class="category-card {{ $category->type }}">
                <span>{{ $category->name }}</span>
            </div>
        @endforeach
    </div>
</div>
@endsection