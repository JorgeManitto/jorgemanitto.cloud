{{-- resources/views/pages/media/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Subir Archivo')

@section('content')

    <x-monky.page-header icon="📁" title="Nuevo Archivo o Enlace" :lastUpdated="now()->format('H:i')" />

    <div class="px-3 lg:px-6 py-6 lg:py-10 bg-background border-2 border-border space-y-8">

        {{-- Back --}}
        <div>
            <a href="{{ route('media.index') }}" class="text-sm text-muted-foreground hover:underline">
                ← Volver al listado
            </a>
        </div>

        {{-- Main form --}}
        <div class="card">
            <div class="flex items-center gap-2.5 p-4" style="border-bottom: 1px solid var(--border);">
                <span class="bullet"></span>
                <span class="font-medium">NUEVO ARCHIVO / ENLACE</span>
            </div>

            <div class="p-4 lg:p-6">
                <form action="{{ route('media.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    @include('pages.media._form')

                    <div class="flex items-center gap-3 pt-4" style="border-top: 1px solid var(--border);">
                        <button type="submit" class="px-6 py-2.5 bg-primary text-primary-foreground rounded font-medium text-sm uppercase">
                            GUARDAR
                        </button>
                        <a href="{{ route('media.index') }}" class="px-6 py-2.5 bg-secondary text-secondary-foreground rounded font-medium text-sm uppercase">
                            CANCELAR
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Bulk Upload --}}
        <div class="card">
            <div class="flex items-center gap-2.5 p-4" style="border-bottom: 1px solid var(--border);">
                <span class="bullet"></span>
                <span class="font-medium">SUBIDA MÚLTIPLE</span>
            </div>

            <div class="p-4 lg:p-6">
                <form action="{{ route('media.bulk-upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label for="bulk_files" class="block text-xs font-medium text-muted-foreground uppercase mb-1.5">Seleccionar archivos (máx. 20)</label>
                        <input type="file" name="files[]" id="bulk_files" multiple required
                               accept="{{ implode(',', array_map(fn($e) => '.' . $e, \App\Models\MediaFile::allAllowedExtensions())) }}"
                               class="w-full px-3 py-2.5 bg-accent border border-border rounded text-sm focus:outline-none focus:border-primary file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:uppercase file:bg-secondary file:text-secondary-foreground file:cursor-pointer">
                        <p class="text-xs text-muted-foreground mt-1">Todos se almacenan de forma privada.</p>
                    </div>

                    <div class="max-w-xs">
                        <label for="bulk_folder" class="block text-xs font-medium text-muted-foreground uppercase mb-1.5">Carpeta (todos)</label>
                        <select name="folder" id="bulk_folder"
                                class="w-full px-3 py-2.5 bg-accent border border-border rounded text-sm focus:outline-none focus:border-primary">
                            <option value="">— Sin carpeta —</option>
                            @foreach(\App\Models\MediaFile::FOLDERS as $folder)
                                <option value="{{ $folder }}">{{ $folder }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="px-6 py-2.5 bg-secondary text-secondary-foreground rounded font-medium text-sm uppercase">
                        SUBIR TODOS
                    </button>
                </form>
            </div>
        </div>

    </div>

@endsection