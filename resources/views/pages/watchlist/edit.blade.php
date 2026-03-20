{{-- resources/views/pages/watchlist/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Editar: ' . $item->title)

@section('content')

    <x-monky.page-header icon="✏️" title="Editar Item" :lastUpdated="now()->format('H:i')" />

    <div class="px-3 lg:px-6 py-6 lg:py-10 bg-background border-2 border-border space-y-8">

        {{-- Back --}}
        <div>
            <a href="{{ route('watchlist.show', $item) }}" class="text-sm text-muted-foreground hover:underline">
                ← Volver al detalle
            </a>
        </div>

        <div class="card">
            <div class="flex items-center gap-2.5 p-4" style="border-bottom: 1px solid var(--border);">
                <span class="bullet"></span>
                <span class="font-medium">EDITAR: {{ strtoupper($item->title) }}</span>
            </div>

            <div class="p-4 lg:p-6">
                <form action="{{ route('watchlist.update', $item) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    @include('pages.watchlist._form', ['item' => $item])

                    <div class="flex items-center gap-3 pt-4" style="border-top: 1px solid var(--border);">
                        <button type="submit" class="px-6 py-2.5 bg-primary text-primary-foreground rounded font-medium text-sm uppercase">
                            ACTUALIZAR
                        </button>
                        <a href="{{ route('watchlist.show', $item) }}" class="px-6 py-2.5 bg-secondary text-secondary-foreground rounded font-medium text-sm uppercase">
                            CANCELAR
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>

@endsection
