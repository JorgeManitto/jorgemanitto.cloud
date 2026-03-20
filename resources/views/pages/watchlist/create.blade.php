{{-- resources/views/pages/watchlist/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Agregar a Watchlist')

@section('content')

    <x-monky.page-header icon="🎬" title="Agregar a Watchlist" :lastUpdated="now()->format('H:i')" />

    <div class="px-3 lg:px-6 py-6 lg:py-10 bg-background border-2 border-border space-y-8">

        {{-- Back --}}
        <div>
            <a href="{{ route('watchlist.index') }}" class="text-sm text-muted-foreground hover:underline">
                ← Volver al listado
            </a>
        </div>

        <div class="card">
            <div class="flex items-center gap-2.5 p-4" style="border-bottom: 1px solid var(--border);">
                <span class="bullet"></span>
                <span class="font-medium">NUEVO ITEM</span>
            </div>

            <div class="p-4 lg:p-6">
                <form action="{{ route('watchlist.store') }}" method="POST" class="space-y-6">
                    @csrf

                    @include('pages.watchlist._form')

                    <div class="flex items-center gap-3 pt-4" style="border-top: 1px solid var(--border);">
                        <button type="submit" class="px-6 py-2.5 bg-primary text-primary-foreground rounded font-medium text-sm uppercase">
                            GUARDAR
                        </button>
                        <a href="{{ route('watchlist.index') }}" class="px-6 py-2.5 bg-secondary text-secondary-foreground rounded font-medium text-sm uppercase">
                            CANCELAR
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>

@endsection
