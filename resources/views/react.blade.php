@extends('layouts.app')

@php($spa = true)

@php($appProps = [
    'page' => $page ?? null,
    'data' => $data ?? null,
    'csrfToken' => csrf_token(),
    'auth' => [
        'check' => auth()->check(),
        'user' => auth()->user(),
    ],
    'flash' => [
        'status' => session('status'),
        'success' => session('success'),
        'error' => session('error'),
    ],
    'errors' => $errors->toArray(),
])

@section('content')
    <div id="app"></div>

    <script>
        window.__APP_PROPS__ = @json($appProps);
    </script>
@endsection
