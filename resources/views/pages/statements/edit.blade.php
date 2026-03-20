{{-- resources/views/pages/statements/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Statement')

@section('content')

    <x-monky.page-header icon="📄" title="Edit Statement" />

    <div class="px-3 lg:px-6 py-6 lg:py-10 bg-background border-2 border-border">
        @include('pages.statements._form', ['statement' => $statement])
    </div>

@endsection
