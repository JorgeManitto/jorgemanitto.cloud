{{-- resources/views/pages/media/show.blade.php --}}
@extends('layouts.app')

@section('title', $file->name)

@section('content')

    <x-monky.page-header icon="{{ $file->type_emoji }}" :title="$file->name" :lastUpdated="now()->format('H:i')" />

    <div class="px-3 lg:px-6 py-6 lg:py-10 bg-background border-2 border-border space-y-8">

        {{-- Success flash --}}
        @if(session('success'))
            <div class="p-3 rounded border" style="border-color: var(--success); background-color: color-mix(in srgb, var(--success) 8%, transparent);">
                <p class="text-sm text-success">{{ session('success') }}</p>
            </div>
        @endif

        {{-- Back + Actions --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('media.index') }}" class="text-sm text-muted-foreground hover:underline">
                ← Volver al listado
            </a>
            <div class="flex items-center gap-2">
                @if($file->is_link)
                    <a href="{{ $file->external_url }}" target="_blank" class="px-5 py-2.5 bg-primary text-primary-foreground rounded font-medium text-sm uppercase">
                        ↗ ABRIR ENLACE
                    </a>
                @else
                    <a href="{{ route('media.download', $file) }}" class="px-5 py-2.5 bg-primary text-primary-foreground rounded font-medium text-sm uppercase">
                        ⬇ DESCARGAR
                    </a>
                @endif
                <a href="{{ route('media.edit', $file) }}" class="px-5 py-2.5 bg-secondary text-secondary-foreground rounded font-medium text-sm uppercase">
                    ✏️ EDITAR
                </a>
                <form action="{{ route('media.destroy', $file) }}" method="POST" onsubmit="return confirm('¿Eliminar permanentemente?')">
                    @csrf
                    @method('DELETE')
                    <button class="px-5 py-2.5 rounded font-medium text-sm uppercase" style="background-color: color-mix(in srgb, var(--destructive) 15%, transparent); color: var(--destructive);">
                        🗑️ ELIMINAR
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ══════════ Main content ══════════ --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Preview --}}
                <div class="card">
                    <div class="flex items-center gap-2.5 p-4" style="border-bottom: 1px solid var(--border);">
                        <span class="bullet"></span>
                        <span class="font-medium">VISTA PREVIA</span>
                    </div>
                    <div class="p-4 lg:p-6">

                        {{-- ── Link → iframe ── --}}
                        @if($file->is_link)
                            <div class="space-y-3">
                                <div class="flex items-center gap-2 text-xs text-muted-foreground">
                                    <span>🔗</span>
                                    <a href="{{ $file->external_url }}" target="_blank" class="hover:underline break-all">
                                        {{ $file->external_url }}
                                    </a>
                                </div>
                                <div class="w-full rounded border border-border overflow-hidden" style="height: 600px;">
                                    <iframe
                                        src="{{ $file->external_url }}"
                                        class="w-full h-full"
                                        frameborder="0"
                                        allowfullscreen
                                        loading="lazy"
                                        sandbox="allow-scripts allow-same-origin allow-popups allow-forms"
                                        referrerpolicy="no-referrer"
                                        title="{{ $file->name }}">
                                    </iframe>
                                </div>
                                <p class="text-xs text-muted-foreground">
                                    Si el sitio bloquea la carga en iframe,
                                    <a href="{{ $file->external_url }}" target="_blank" class="text-primary hover:underline">abrilo en una pestaña nueva ↗</a>
                                </p>
                            </div>

                        {{-- ── Imagen (streaming privado) ── --}}
                        @elseif($file->type === 'image')
                            <div class="flex justify-center">
                                <img src="{{ $file->stream_url }}"
                                     alt="{{ $file->name }}"
                                     class="max-w-full max-h-[500px] rounded object-contain border border-border">
                            </div>

                        {{-- ── Video (streaming privado) ── --}}
                        @elseif($file->type === 'video')
                            <video controls
                                   class="w-full max-h-[500px] rounded bg-black"
                                   preload="metadata">
                                <source src="{{ $file->stream_url }}" type="{{ $file->mime_type }}">
                                Tu navegador no soporta la reproducción de video.
                            </video>

                        {{-- ── PDF (streaming privado en iframe) ── --}}
                        @elseif($file->extension === 'pdf')
                            <iframe src="{{ $file->stream_url }}"
                                    class="w-full rounded border border-border"
                                    style="height: 600px;"
                                    title="{{ $file->name }}">
                            </iframe>

                        {{-- ── Otros archivos ── --}}
                        @else
                            <div class="p-8 text-center">
                                <div class="text-6xl mb-4">{{ $file->type_emoji }}</div>
                                <p class="text-muted-foreground text-sm mb-4">
                                    No hay vista previa disponible para archivos <strong>.{{ $file->extension }}</strong>
                                </p>
                                <a href="{{ route('media.download', $file) }}" class="px-5 py-2.5 bg-primary text-primary-foreground rounded font-medium text-sm uppercase inline-block">
                                    ⬇ DESCARGAR ARCHIVO
                                </a>
                            </div>
                        @endif

                    </div>
                </div>

                {{-- Description --}}
                @if($file->description)
                    <div class="card">
                        <div class="flex items-center gap-2.5 p-4" style="border-bottom: 1px solid var(--border);">
                            <span class="bullet"></span>
                            <span class="font-medium">DESCRIPCIÓN</span>
                        </div>
                        <div class="p-4 lg:p-6">
                            <p class="text-sm text-muted-foreground whitespace-pre-line">{{ $file->description }}</p>
                        </div>
                    </div>
                @endif

            </div>

            {{-- ══════════ Sidebar ══════════ --}}
            <div class="space-y-6">

                {{-- File info --}}
                <div class="card">
                    <div class="flex items-center gap-2.5 p-4" style="border-bottom: 1px solid var(--border);">
                        <span class="bullet"></span>
                        <span class="font-medium">INFORMACIÓN</span>
                    </div>
                    <div class="p-4 space-y-3">
                        <div>
                            <p class="text-xs text-muted-foreground uppercase">Tipo</p>
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-medium mt-0.5"
                                  style="background-color: color-mix(in srgb, var(--{{ $file->type_color }}) 15%, transparent); color: var(--{{ $file->type_color }});">
                                {{ $file->type_emoji }} {{ $file->type_label }}
                            </span>
                        </div>

                        @if($file->is_link)
                            <div>
                                <p class="text-xs text-muted-foreground uppercase">Dominio</p>
                                <p class="text-sm font-medium">{{ $file->link_domain }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-muted-foreground uppercase">URL completa</p>
                                <p class="text-xs text-muted-foreground font-mono break-all">{{ $file->external_url }}</p>
                            </div>
                        @else
                            <div>
                                <p class="text-xs text-muted-foreground uppercase">Nombre original</p>
                                <p class="text-sm font-medium break-all">{{ $file->original_name }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-muted-foreground uppercase">Extensión</p>
                                <span class="badge badge-secondary">{{ $file->extension_badge }}</span>
                            </div>
                            <div>
                                <p class="text-xs text-muted-foreground uppercase">MIME</p>
                                <p class="text-xs text-muted-foreground font-mono">{{ $file->mime_type }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-muted-foreground uppercase">Tamaño</p>
                                <p class="text-sm font-bold">{{ $file->size_formatted }}</p>
                            </div>
                            @if($file->dimensions_formatted)
                                <div>
                                    <p class="text-xs text-muted-foreground uppercase">Dimensiones</p>
                                    <p class="text-sm font-medium">{{ $file->dimensions_formatted }} px</p>
                                </div>
                            @endif
                            @if($file->duration_formatted)
                                <div>
                                    <p class="text-xs text-muted-foreground uppercase">Duración</p>
                                    <p class="text-sm font-medium">{{ $file->duration_formatted }}</p>
                                </div>
                            @endif
                            <div>
                                <p class="text-xs text-muted-foreground uppercase">Almacenamiento</p>
                                <span class="badge badge-secondary text-xs">🔒 Privado</span>
                            </div>
                        @endif

                        <div>
                            <p class="text-xs text-muted-foreground uppercase">Carpeta</p>
                            <p class="text-sm font-medium">{{ $file->folder ?? 'Sin carpeta' }}</p>
                        </div>

                        @if($file->is_file)
                            <div>
                                <p class="text-xs text-muted-foreground uppercase">Descargas</p>
                                <p class="text-sm font-bold">{{ $file->downloads }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Tags --}}
                @if($file->tags && count($file->tags))
                    <div class="card">
                        <div class="flex items-center gap-2.5 p-4" style="border-bottom: 1px solid var(--border);">
                            <span class="bullet"></span>
                            <span class="font-medium">TAGS</span>
                        </div>
                        <div class="p-4">
                            <div class="flex flex-wrap gap-2">
                                @foreach($file->tags as $tag)
                                    <span class="badge badge-secondary text-xs">{{ $tag }}</span>
                                @endforeach
                            </div>
                        </div>
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
                            <p class="text-xs text-muted-foreground uppercase">Creado</p>
                            <p class="text-sm font-medium">{{ $file->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-muted-foreground uppercase">Última modificación</p>
                            <p class="text-sm font-medium">{{ $file->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Share / Copy URL --}}
                <div class="card">
                    <div class="flex items-center gap-2.5 p-4" style="border-bottom: 1px solid var(--border);">
                        <span class="bullet"></span>
                        <span class="font-medium">{{ $file->is_link ? 'URL ORIGINAL' : 'ENLACE DE STREAM' }}</span>
                    </div>
                    <div class="p-4">
                        <div class="flex items-center gap-2">
                            <input type="text" readonly
                                   value="{{ $file->is_link ? $file->external_url : $file->stream_url }}"
                                   id="share-url"
                                   class="flex-1 px-3 py-2 bg-accent border border-border rounded text-xs font-mono text-muted-foreground focus:outline-none truncate">
                            <button onclick="navigator.clipboard.writeText(document.getElementById('share-url').value); this.textContent='✓'; setTimeout(() => this.textContent='📋', 1500)"
                                    class="px-3 py-2 bg-secondary text-secondary-foreground rounded text-sm flex-shrink-0">
                                📋
                            </button>
                        </div>
                        @if($file->is_file)
                            <p class="text-xs text-muted-foreground mt-2">
                                🔒 Esta URL solo funciona para usuarios autenticados.
                            </p>
                        @endif
                    </div>
                </div>

            </div>

        </div>

    </div>

@endsection