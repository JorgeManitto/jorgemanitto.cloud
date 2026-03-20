{{-- resources/views/pages/media/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Archivos')

@section('content')

    <x-monky.page-header icon="📁" title="Archivos" :lastUpdated="now()->format('H:i')" />

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
                <p class="text-2xl font-bold">🖼️ {{ $stats['images'] }}</p>
                <p class="text-xs text-muted-foreground uppercase">Imágenes</p>
            </div>
            <div class="card p-4 text-center">
                <p class="text-2xl font-bold">🎥 {{ $stats['videos'] }}</p>
                <p class="text-xs text-muted-foreground uppercase">Videos</p>
            </div>
            <div class="card p-4 text-center">
                <p class="text-2xl font-bold">📄 {{ $stats['documents'] }}</p>
                <p class="text-xs text-muted-foreground uppercase">Documentos</p>
            </div>
            <div class="card p-4 text-center">
                <p class="text-2xl font-bold">🔗 {{ $stats['links'] }}</p>
                <p class="text-xs text-muted-foreground uppercase">Enlaces</p>
            </div>
            <div class="card p-4 text-center">
                <p class="text-2xl font-bold">
                    @php
                        $totalBytes = $stats['total_size'];
                        if ($totalBytes >= 1073741824) echo number_format($totalBytes / 1073741824, 1) . ' GB';
                        elseif ($totalBytes >= 1048576) echo number_format($totalBytes / 1048576, 1) . ' MB';
                        elseif ($totalBytes >= 1024) echo number_format($totalBytes / 1024, 1) . ' KB';
                        else echo $totalBytes . ' B';
                    @endphp
                </p>
                <p class="text-xs text-muted-foreground uppercase">Almacenamiento</p>
            </div>
        </div>

        {{-- Actions & Filters --}}
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('media.create') }}" class="px-5 py-2.5 bg-primary text-primary-foreground rounded font-medium text-sm uppercase">
                + SUBIR ARCHIVO
            </a>
            <a href="{{ route('media.create', ['mode' => 'link']) }}" class="px-5 py-2.5 bg-secondary text-secondary-foreground rounded font-medium text-sm uppercase">
                🔗 AGREGAR ENLACE
            </a>

            {{-- Filters --}}
            <form method="GET" action="{{ route('media.index') }}" class="flex flex-wrap items-center gap-2 ml-auto">
                <input
                    type="text"
                    name="search"
                    value="{{ $filters['search'] ?? '' }}"
                    placeholder="Buscar..."
                    class="px-3 py-2 bg-accent border border-border rounded text-sm placeholder:text-muted-foreground focus:outline-none focus:border-primary"
                />
                <select name="type" class="px-3 py-2 bg-accent border border-border rounded text-sm focus:outline-none focus:border-primary">
                    <option value="">Todos los tipos</option>
                    @foreach(\App\Models\MediaFile::TYPES as $key => $label)
                        <option value="{{ $key }}" @selected(($filters['type'] ?? '') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="folder" class="px-3 py-2 bg-accent border border-border rounded text-sm focus:outline-none focus:border-primary">
                    <option value="">Todas las carpetas</option>
                    @foreach($usedFolders as $folder)
                        <option value="{{ $folder }}" @selected(($filters['folder'] ?? '') === $folder)>{{ $folder }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-secondary text-secondary-foreground rounded text-sm uppercase font-medium">
                    Filtrar
                </button>
                @if(array_filter($filters))
                    <a href="{{ route('media.index') }}" class="px-4 py-2 text-muted-foreground text-sm uppercase hover:underline">
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
                    <span class="font-medium">TODOS LOS ARCHIVOS</span>
                </div>
                <span class="badge badge-secondary">{{ $files->total() }} TOTAL</span>
            </div>

            <div class="bg-accent overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <th class="text-left p-3 text-xs font-medium text-muted-foreground uppercase w-10">Vista</th>
                            <th class="text-left p-3 text-xs font-medium text-muted-foreground uppercase">Nombre</th>
                            <th class="text-center p-3 text-xs font-medium text-muted-foreground uppercase hidden sm:table-cell">Tipo</th>
                            <th class="text-center p-3 text-xs font-medium text-muted-foreground uppercase hidden md:table-cell">Ext.</th>
                            <th class="text-right p-3 text-xs font-medium text-muted-foreground uppercase hidden md:table-cell">Tamaño</th>
                            <th class="text-left p-3 text-xs font-medium text-muted-foreground uppercase hidden lg:table-cell">Carpeta</th>
                            <th class="text-center p-3 text-xs font-medium text-muted-foreground uppercase hidden lg:table-cell">Info</th>
                            <th class="text-center p-3 text-xs font-medium text-muted-foreground uppercase hidden sm:table-cell">Descargas</th>
                            <th class="text-right p-3 text-xs font-medium text-muted-foreground uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($files as $file)
                            <tr class="hover:bg-accent transition-colors" style="border-bottom: 1px solid var(--border);">
                                {{-- Thumbnail --}}
                                <td class="p-3">
                                    @if($file->type === 'image')
                                        <div class="w-10 h-10 rounded overflow-hidden bg-border flex-shrink-0">
                                            <img src="{{ $file->stream_url }}" alt="{{ $file->name }}" class="w-full h-full object-cover">
                                        </div>
                                    @elseif($file->thumbnail_stream_url)
                                        <div class="w-10 h-10 rounded overflow-hidden bg-border flex-shrink-0">
                                            <img src="{{ $file->thumbnail_stream_url }}" alt="{{ $file->name }}" class="w-full h-full object-cover">
                                        </div>
                                    @else
                                        <div class="w-10 h-10 rounded bg-border flex items-center justify-center flex-shrink-0">
                                            <span class="text-lg">{{ $file->type_emoji }}</span>
                                        </div>
                                    @endif
                                </td>

                                {{-- Nombre --}}
                                <td class="p-3">
                                    <a href="{{ route('media.show', $file) }}" class="font-medium hover:underline block">
                                        {{ Str::limit($file->name, 40) }}
                                    </a>
                                    @if($file->is_link)
                                        <span class="text-xs text-muted-foreground">{{ Str::limit($file->link_domain, 40) }}</span>
                                    @else
                                        <span class="text-xs text-muted-foreground">{{ Str::limit($file->original_name, 50) }}</span>
                                    @endif
                                </td>

                                {{-- Tipo --}}
                                <td class="p-3 text-center hidden sm:table-cell">
                                    <span class="inline-block px-2 py-0.5 rounded text-xs font-medium"
                                          style="background-color: color-mix(in srgb, var(--{{ $file->type_color }}) 15%, transparent); color: var(--{{ $file->type_color }});">
                                        {{ $file->type_label }}
                                    </span>
                                </td>

                                {{-- Extension --}}
                                <td class="p-3 text-center hidden md:table-cell">
                                    <span class="badge badge-secondary text-xs">{{ $file->extension_badge }}</span>
                                </td>

                                {{-- Tamaño --}}
                                <td class="p-3 text-right text-muted-foreground hidden md:table-cell">
                                    {{ $file->size_formatted }}
                                </td>

                                {{-- Carpeta --}}
                                <td class="p-3 text-muted-foreground hidden lg:table-cell">
                                    {{ $file->folder ?? '—' }}
                                </td>

                                {{-- Info extra --}}
                                <td class="p-3 text-center text-xs text-muted-foreground hidden lg:table-cell">
                                    @if($file->is_link)
                                        {{ Str::limit($file->link_domain, 25) }}
                                    @elseif($file->dimensions_formatted)
                                        {{ $file->dimensions_formatted }}
                                    @elseif($file->duration_formatted)
                                        {{ $file->duration_formatted }}
                                    @else
                                        —
                                    @endif
                                </td>

                                {{-- Descargas --}}
                                <td class="p-3 text-center hidden sm:table-cell">
                                    <span class="badge badge-secondary">{{ $file->downloads }}</span>
                                </td>

                                {{-- Acciones --}}
                                <td class="p-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($file->is_file)
                                            <a href="{{ route('media.download', $file) }}" class="px-2 py-1 bg-primary text-primary-foreground rounded text-xs">⬇</a>
                                        @else
                                            <a href="{{ $file->external_url }}" target="_blank" class="px-2 py-1 bg-primary text-primary-foreground rounded text-xs">↗</a>
                                        @endif
                                        <a href="{{ route('media.show', $file) }}" class="px-2 py-1 bg-secondary text-secondary-foreground rounded text-xs">VER</a>
                                        <a href="{{ route('media.edit', $file) }}" class="px-2 py-1 bg-secondary text-secondary-foreground rounded text-xs">EDIT</a>
                                        <form action="{{ route('media.destroy', $file) }}" method="POST" onsubmit="return confirm('¿Eliminar este {{ $file->is_link ? 'enlace' : 'archivo' }} permanentemente?')">
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
                                    <p class="font-display text-xl mb-2">NO HAY ARCHIVOS</p>
                                    <p class="text-xs">Subí un archivo o agregá un enlace para empezar.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($files->hasPages())
            <div class="flex justify-center">
                {{ $files->links() }}
            </div>
        @endif

    </div>

@endsection