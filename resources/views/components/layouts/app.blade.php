@extends('components.layouts.base', ['title' => $title ?? 'Page Title'])

@section('main')
    {{ $slot }}
@endsection
