{{-- resources/views/pages/statements/create.blade.php --}}
@extends('layouts.app')

@section('title', 'New Statement')

@section('content')

    <x-monky.page-header icon="📄" title="New Statement" />

    <div class="px-3 lg:px-6 py-6 lg:py-10 bg-background border-2 border-border">
        @include('pages.statements._form')
    </div>

@endsection
