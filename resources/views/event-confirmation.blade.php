@extends('components.layouts.base', ['title' => 'Visszaigazolás'])

@section('main')
    <div class="flex flex-col justify-center items-center">
        <div class="text-2xl font-bold">{{ $message }}</div>
        <div class="mt-4">
            <a href="{{ route('welcome') }}">Vissza a főoldalra</a>
        </div>
    </div>
@endsection