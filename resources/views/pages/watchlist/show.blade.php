{{-- resources/views/pages/watchlist/show.blade.php --}}
@extends('layouts.app')

@section('title', $item->title)

@section('content')

    <x-monky.page-header icon="{{ $item->type_emoji }}" :title="$item->title" :lastUpdated="now()->format('H:i')" />

    <div class="px-3 lg:px-6 py-6 lg:py-10 bg-background border-2 border-border space-y-8">

        {{-- Success flash --}}
        @if(session('success'))
            <div class="p-3 rounded border" style="border-color: var(--success); background-color: color-mix(in srgb, var(--success) 8%, transparent);">
                <p class="text-sm text-success">{{ session('success') }}</p>
            </div>
        @endif

        {{-- Back + Actions --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('watchlist.index') }}" class="text-sm text-muted-foreground hover:underline">
                ← Volver al listado
            </a>
            <div class="flex items-center gap-2">
                <a href="{{ route('watchlist.edit', $item) }}" class="px-5 py-2.5 bg-secondary text-secondary-foreground rounded font-medium text-sm uppercase">
                    ✏️ EDITAR
                </a>
                <form action="{{ route('watchlist.destroy', $item) }}" method="POST" onsubmit="return confirm('¿Eliminar de tu watchlist?')">
                    @csrf
                    @method('DELETE')
                    <button class="px-5 py-2.5 rounded font-medium text-sm uppercase" style="background-color: color-mix(in srgb, var(--destructive) 15%, transparent); color: var(--destructive);">
                        🗑️ ELIMINAR
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Main info --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Info card --}}
                <div class="card">
                    <div class="flex items-center gap-2.5 p-4" style="border-bottom: 1px solid var(--border);">
                        <span class="bullet"></span>
                        <span class="font-medium">INFORMACIÓN</span>
                    </div>

                    <div class="p-4 lg:p-6 space-y-4">
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            <div>
                                <p class="text-xs text-muted-foreground uppercase mb-1">Tipo</p>
                                <span class="badge badge-secondary">{{ $item->type_emoji }} {{ $item->type_label }}</span>
                            </div>
                            <div>
                                <p class="text-xs text-muted-foreground uppercase mb-1">Estado</p>
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-medium"
                                      style="background-color: color-mix(in srgb, var(--{{ $item->status_color }}) 15%, transparent); color: var(--{{ $item->status_color }});">
                                    {{ $item->status_label }}
                                </span>
                            </div>
                            <div>
                                <p class="text-xs text-muted-foreground uppercase mb-1">Año</p>
                                <p class="text-sm font-medium">{{ $item->year ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-muted-foreground uppercase mb-1">Género</p>
                                <p class="text-sm font-medium">{{ $item->genre ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-muted-foreground uppercase mb-1">Plataforma</p>
                                <p class="text-sm font-medium">{{ $item->platform ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-muted-foreground uppercase mb-1">Rating</p>
                                @if($item->rating)
                                    <p class="text-sm">
                                        <span class="text-warning">{{ $item->rating_stars }}</span>
                                        <span class="font-bold ml-1">{{ $item->rating }}/10</span>
                                    </p>
                                @else
                                    <p class="text-sm text-muted-foreground">Sin calificar</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Series progress --}}
                @if($item->type === 'series')
                    <div class="card">
                        <div class="flex items-center gap-2.5 p-4" style="border-bottom: 1px solid var(--border);">
                            <span class="bullet"></span>
                            <span class="font-medium">PROGRESO</span>
                        </div>
                        <div class="p-4 lg:p-6">
                            <div class="grid grid-cols-3 gap-4 mb-4">
                                <div class="text-center">
                                    <p class="text-2xl font-bold">{{ $item->seasons ?? '—' }}</p>
                                    <p class="text-xs text-muted-foreground uppercase">Temporadas</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-2xl font-bold">{{ $item->current_episode ?? 0 }}</p>
                                    <p class="text-xs text-muted-foreground uppercase">Episodio actual</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-2xl font-bold">{{ $item->total_episodes ?? '—' }}</p>
                                    <p class="text-xs text-muted-foreground uppercase">Total episodios</p>
                                </div>
                            </div>
                            @if($item->total_episodes)
                                <div class="w-full h-3 bg-border rounded-full overflow-hidden">
                                    <div class="h-full bg-primary rounded-full transition-all" style="width: {{ $item->progress }}%"></div>
                                </div>
                                <p class="text-xs text-muted-foreground text-center mt-2">{{ $item->progress }}% completado</p>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Notes --}}
                @if($item->notes)
                    <div class="card">
                        <div class="flex items-center gap-2.5 p-4" style="border-bottom: 1px solid var(--border);">
                            <span class="bullet"></span>
                            <span class="font-medium">NOTAS</span>
                        </div>
                        <div class="p-4 lg:p-6">
                            <p class="text-sm text-muted-foreground whitespace-pre-line">{{ $item->notes }}</p>
                        </div>
                    </div>
                @endif

            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">

                {{-- Poster --}}
                @if($item->poster_url)
                    <div class="card overflow-hidden">
                        <img src="{{ $item->poster_url }}" alt="{{ $item->title }}" class="w-full h-auto">
                    </div>
                @endif

                {{-- Dates --}}
                <div class="card">
                    <div class="flex items-center gap-2.5 p-4" style="border-bottom: 1px solid var(--border);">
                        <span class="bullet"></span>
                        <span class="font-medium">FECHAS</span>
                    </div>
                    <div class="p-4 space-y-3">
                        <div>
                            <p class="text-xs text-muted-foreground uppercase">Empezado</p>
                            <p class="text-sm font-medium">{{ $item->started_at?->format('d/m/Y') ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-muted-foreground uppercase">Terminado</p>
                            <p class="text-sm font-medium">{{ $item->finished_at?->format('d/m/Y') ?? '—' }}</p>
                        </div>
                        <div style="border-top: 1px solid var(--border); padding-top: 0.75rem;">
                            <p class="text-xs text-muted-foreground uppercase">Creado</p>
                            <p class="text-sm font-medium">{{ $item->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Quick status change --}}
                <div class="card">
                    <div class="flex items-center gap-2.5 p-4" style="border-bottom: 1px solid var(--border);">
                        <span class="bullet"></span>
                        <span class="font-medium">CAMBIAR ESTADO</span>
                    </div>
                    <div class="p-4 grid grid-cols-2 gap-2">
                        @foreach(\App\Models\WatchlistItem::STATUSES as $key => $label)
                            <form action="{{ route('watchlist.toggle-status', $item) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="{{ $key }}">
                                <button type="submit"
                                        class="w-full px-3 py-2 rounded text-xs font-medium uppercase transition-colors {{ $item->status === $key ? 'ring-2 ring-primary' : '' }}"
                                        style="background-color: color-mix(in srgb, var(--{{ \App\Models\WatchlistItem::STATUSES[$key] === $item->status_label ? 'primary' : 'secondary' }}) 15%, transparent);">
                                    {{ $label }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>

            </div>

        </div>

    </div>

@endsection
