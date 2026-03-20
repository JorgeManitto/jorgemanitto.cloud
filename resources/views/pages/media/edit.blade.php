{{-- resources/views/pages/media/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Editar: ' . $file->name)

@section('content')

    <x-monky.page-header icon="✏️" title="Editar Archivo" :lastUpdated="now()->format('H:i')" />

    <div class="px-3 lg:px-6 py-6 lg:py-10 bg-background border-2 border-border space-y-8">

        {{-- Back --}}
        <div>
            <a href="{{ route('media.show', $file) }}" class="text-sm text-muted-foreground hover:underline">
                ← Volver al detalle
            </a>
        </div>

        <div class="card">
            <div class="flex items-center gap-2.5 p-4" style="border-bottom: 1px solid var(--border);">
                <span class="bullet"></span>
                <span class="font-medium">EDITAR: {{ strtoupper($file->name) }}</span>
            </div>

            <div class="p-4 lg:p-6">

                {{-- Current file/link info --}}
                <div class="flex items-center gap-4 p-4 mb-6 rounded border border-border bg-accent">
                    @if($file->type === 'image')
                        <div class="w-16 h-16 rounded overflow-hidden bg-border flex-shrink-0">
                            <img src="{{ $file->stream_url }}" alt="{{ $file->name }}" class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="w-16 h-16 rounded bg-border flex items-center justify-center flex-shrink-0">
                            <span class="text-2xl">{{ $file->type_emoji }}</span>
                        </div>
                    @endif
                    <div>
                        @if($file->is_link)
                            <p class="font-medium text-sm">🔗 {{ $file->link_domain }}</p>
                            <p class="text-xs text-muted-foreground break-all">{{ Str::limit($file->external_url, 80) }}</p>
                        @else
                            <p class="font-medium text-sm">{{ $file->original_name }}</p>
                            <p class="text-xs text-muted-foreground">
                                {{ $file->extension_badge }} — {{ $file->size_formatted }}
                                @if($file->dimensions_formatted) — {{ $file->dimensions_formatted }} @endif
                            </p>
                        @endif
                    </div>
                </div>

                <form action="{{ route('media.update', $file) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    @include('pages.media._form', ['file' => $file])

                    <div class="flex items-center gap-3 pt-4" style="border-top: 1px solid var(--border);">
                        <button type="submit" class="px-6 py-2.5 bg-primary text-primary-foreground rounded font-medium text-sm uppercase">
                            ACTUALIZAR
                        </button>
                        <a href="{{ route('media.show', $file) }}" class="px-6 py-2.5 bg-secondary text-secondary-foreground rounded font-medium text-sm uppercase">
                            CANCELAR
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>

@endsection