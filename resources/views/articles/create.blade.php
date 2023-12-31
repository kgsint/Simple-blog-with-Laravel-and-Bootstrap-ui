@extends('layouts.app')

@section('content')

<h3>
    Create an article
</h3>

<hr>

<form action="{{ route('articles.store') }}" method="POST">

    @csrf

    <div class="mb-3">
        <label class=" form-label fw-bold" for="">Article's Title</label>
        <input
        type="text"
        value="{{ old('title') }}"
        class=" form-control @error('title') is-invalid @enderror "
        name="title">
        @error('title')
            <div class=" invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class=" form-label fw-bold" for="">Choose Category</label>
        <select class="form-select" name="category">
            <option value="">Choose Category</option>
            @foreach (\App\Models\Category::all() as $category)
                <option
                    value="{{ $category->id }}"
                    {{ (int) old('category') === $category->id ? 'selected' : '' }}
                >
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('content')
            <div class=" invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class=" form-label fw-bold" for="">Article's Content</label>
        <textarea
            class=" form-control @error('content') is-invalid @enderror"
            name="content"
            rows="4"
            cols="10"
        >
        {{ old('content') }}
        </textarea>
        @error('content')
            <div class=" invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button class=" btn btn-primary">Create</button>
</form>
@endsection

