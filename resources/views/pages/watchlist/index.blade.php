{{-- resources/views/pages/watchlist/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Watchlist')

@section('content')

    <x-monky.page-header icon="🎬" title="Watchlist" :lastUpdated="now()->format('H:i')" />

    <div class="px-3 lg:px-6 py-6 lg:py-10 bg-background border-2 border-border space-y-8">

        {{-- Success flash --}}
        @if(session('success'))
            <div class="p-3 rounded border" style="border-color: var(--success); background-color: color-mix(in srgb, var(--success) 8%, transparent);">
                <p class="text-sm text-success">{{ session('success') }}</p>
            </div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <div class="card p-4 text-center">
                <p class="text-2xl font-bold">{{ $stats['total'] }}</p>
                <p class="text-xs text-muted-foreground uppercase">Total</p>
            </div>
            <div class="card p-4 text-center">
                <p class="text-2xl font-bold">🎬 {{ $stats['movies'] }}</p>
                <p class="text-xs text-muted-foreground uppercase">Películas</p>
            </div>
            <div class="card p-4 text-center">
                <p class="text-2xl font-bold">📺 {{ $stats['series'] }}</p>
                <p class="text-xs text-muted-foreground uppercase">Series</p>
            </div>
            <div class="card p-4 text-center">
                <p class="text-2xl font-bold text-warning">{{ $stats['pending'] }}</p>
                <p class="text-xs text-muted-foreground uppercase">Pendientes</p>
            </div>
            <div class="card p-4 text-center">
                <p class="text-2xl font-bold text-primary">{{ $stats['watching'] }}</p>
                <p class="text-xs text-muted-foreground uppercase">Viendo</p>
            </div>
            <div class="card p-4 text-center">
                <p class="text-2xl font-bold text-success">{{ $stats['completed'] }}</p>
                <p class="text-xs text-muted-foreground uppercase">Completados</p>
            </div>
        </div>

        {{-- Actions & Filters --}}
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('watchlist.create') }}" class="px-5 py-2.5 bg-primary text-primary-foreground rounded font-medium text-sm uppercase">
                + AGREGAR
            </a>

            {{-- Filters --}}
            <form method="GET" action="{{ route('watchlist.index') }}" class="flex flex-wrap items-center gap-2 ml-auto">
                <input
                    type="text"
                    name="search"
                    value="{{ $filters['search'] ?? '' }}"
                    placeholder="Buscar título..."
                    class="px-3 py-2 bg-accent border border-border rounded text-sm placeholder:text-muted-foreground focus:outline-none focus:border-primary"
                />
                <select name="type" class="px-3 py-2 bg-accent border border-border rounded text-sm focus:outline-none focus:border-primary">
                    <option value="">Todos los tipos</option>
                    @foreach(\App\Models\WatchlistItem::TYPES as $key => $label)
                        <option value="{{ $key }}" @selected(($filters['type'] ?? '') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="status" class="px-3 py-2 bg-accent border border-border rounded text-sm focus:outline-none focus:border-primary">
                    <option value="">Todos los estados</option>
                    @foreach(\App\Models\WatchlistItem::STATUSES as $key => $label)
                        <option value="{{ $key }}" @selected(($filters['status'] ?? '') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-secondary text-secondary-foreground rounded text-sm uppercase font-medium">
                    Filtrar
                </button>
                @if(array_filter($filters))
                    <a href="{{ route('watchlist.index') }}" class="px-4 py-2 text-muted-foreground text-sm uppercase hover:underline">
                        Limpiar
                    </a>
                @endif
            </form>
        </div>

        {{-- Table --}}
        <div class="card">
            <div class="flex items-center justify-between p-4">
                <div class="flex items-center gap-2.5">
                    <span class="bullet"></span>
                    <span class="font-medium">MI WATCHLIST</span>
                </div>
                <span class="badge badge-secondary">{{ $items->total() }} TOTAL</span>
            </div>

            <div class="bg-accent overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <th class="text-left p-3 text-xs font-medium text-muted-foreground uppercase">#</th>
                            <th class="text-left p-3 text-xs font-medium text-muted-foreground uppercase">Título</th>
                            <th class="text-center p-3 text-xs font-medium text-muted-foreground uppercase hidden sm:table-cell">Tipo</th>
                            <th class="text-center p-3 text-xs font-medium text-muted-foreground uppercase">Estado</th>
                            <th class="text-left p-3 text-xs font-medium text-muted-foreground uppercase hidden md:table-cell">Plataforma</th>
                            <th class="text-left p-3 text-xs font-medium text-muted-foreground uppercase hidden md:table-cell">Género</th>
                            <th class="text-center p-3 text-xs font-medium text-muted-foreground uppercase hidden lg:table-cell">Año</th>
                            <th class="text-center p-3 text-xs font-medium text-muted-foreground uppercase hidden sm:table-cell">Rating</th>
                            <th class="text-center p-3 text-xs font-medium text-muted-foreground uppercase hidden lg:table-cell">Progreso</th>
                            <th class="text-right p-3 text-xs font-medium text-muted-foreground uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr class="hover:bg-accent transition-colors" style="border-bottom: 1px solid var(--border);">
                                <td class="p-3 whitespace-nowrap">
                                    <a href="{{ $item->poster_url }}" target="blank" class="font-medium hover:underline">
                                        <img src="{{ $item->poster_url }}" alt="{{ $item->title }}" height="82" width="82">
                                    </a>
                                </td>
                                {{-- Título --}}
                                <td class="p-3 whitespace-nowrap">
                                    <a href="{{ route('watchlist.show', $item) }}" class="font-medium hover:underline">
                                        {{ $item->type_emoji }} {{ $item->title }}
                                    </a>
                                </td>

                                {{-- Tipo --}}
                                <td class="p-3 text-center hidden sm:table-cell">
                                    <span class="badge badge-secondary">{{ $item->type_label }}</span>
                                </td>

                                {{-- Estado --}}
                                <td class="p-3 text-center">
                                    <span class="inline-block px-2 py-0.5 rounded text-xs font-medium"
                                          style="background-color: color-mix(in srgb, var(--{{ $item->status_color }}) 15%, transparent); color: var(--{{ $item->status_color }});">
                                        {{ $item->status_label }}
                                    </span>
                                </td>

                                {{-- Plataforma --}}
                                <td class="p-3 text-muted-foreground hidden md:table-cell">
                                    {{ $item->platform ?? '—' }}
                                </td>

                                {{-- Género --}}
                                <td class="p-3 text-muted-foreground hidden md:table-cell">
                                    {{ $item->genre ?? '—' }}
                                </td>

                                {{-- Año --}}
                                <td class="p-3 text-center text-muted-foreground hidden lg:table-cell">
                                    {{ $item->year ?? '—' }}
                                </td>

                                {{-- Rating --}}
                                <td class="p-3 text-center hidden sm:table-cell">
                                    @if($item->rating)
                                        <span class="text-warning text-xs">{{ $item->rating_stars }}</span>
                                        <span class="text-xs text-muted-foreground ml-1">{{ $item->rating }}/10</span>
                                    @else
                                        <span class="text-muted-foreground text-xs">—</span>
                                    @endif
                                </td>

                                {{-- Progreso (solo series) --}}
                                <td class="p-3 text-center hidden lg:table-cell">
                                    @if($item->type === 'series' && $item->total_episodes)
                                        <div class="flex items-center gap-2">
                                            <div class="w-16 h-1.5 bg-border rounded-full overflow-hidden">
                                                <div class="h-full bg-primary rounded-full" style="width: {{ $item->progress }}%"></div>
                                            </div>
                                            <span class="text-xs text-muted-foreground">{{ $item->current_episode ?? 0 }}/{{ $item->total_episodes }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted-foreground text-xs">—</span>
                                    @endif
                                </td>

                                {{-- Acciones --}}
                                <td class="p-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('watchlist.show', $item) }}" class="px-2 py-1 bg-secondary text-secondary-foreground rounded text-xs">VER</a>
                                        <a href="{{ route('watchlist.edit', $item) }}" class="px-2 py-1 bg-secondary text-secondary-foreground rounded text-xs">EDIT</a>
                                        <form action="{{ route('watchlist.destroy', $item) }}" method="POST" onsubmit="return confirm('¿Eliminar de tu watchlist?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="px-2 py-1 rounded text-xs" style="background-color: color-mix(in srgb, var(--destructive) 15%, transparent); color: var(--destructive);">DEL</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="p-8 text-center text-muted-foreground">
                                    <p class="font-display text-xl mb-2">TU WATCHLIST ESTÁ VACÍA</p>
                                    <p class="text-xs">Agregá una película o serie para empezar.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($items->hasPages())
            <div class="flex justify-center">
                {{ $items->links() }}
            </div>
        @endif

    </div>

@endsection
