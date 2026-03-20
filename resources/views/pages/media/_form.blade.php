{{-- resources/views/pages/media/_form.blade.php --}}
@php
    $file   = $file ?? null;
    $isEdit = (bool) $file;
    // Default mode: on edit use existing type, on create check query param
    $mode   = $isEdit
        ? ($file->is_link ? 'link' : 'file')
        : old('mode', request('mode', 'file'));
@endphp

{{-- Errors --}}
@if($errors->any())
    <div class="p-3 rounded border" style="border-color: var(--destructive); background-color: color-mix(in srgb, var(--destructive) 8%, transparent);">
        <ul class="text-sm text-destructive space-y-1">
            @foreach($errors->all() as $error)
                <li>• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Mode toggle (solo en create) --}}
@unless($isEdit)
    <div class="flex items-center gap-1 p-1 bg-accent rounded w-fit">
        <button type="button" id="btn-mode-file"
                class="px-4 py-2 rounded text-sm font-medium uppercase transition-colors"
                onclick="setMode('file')">
            📎 Archivo
        </button>
        <button type="button" id="btn-mode-link"
                class="px-4 py-2 rounded text-sm font-medium uppercase transition-colors"
                onclick="setMode('link')">
            🔗 Enlace
        </button>
    </div>
    <input type="hidden" name="mode" id="input-mode" value="{{ $mode }}">
@endunless

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- ═══ FILE FIELDS (solo create, solo modo file) ═══ --}}
    @unless($isEdit)
        <div class="md:col-span-2" id="field-file">
            <label for="file" class="block text-xs font-medium text-muted-foreground uppercase mb-1.5">Archivo *</label>
            <input type="file" name="file" id="file"
                   accept="{{ implode(',', array_map(fn($e) => '.' . $e, \App\Models\MediaFile::allAllowedExtensions())) }}"
                   class="w-full px-3 py-2.5 bg-accent border border-border rounded text-sm focus:outline-none focus:border-primary file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:uppercase file:bg-secondary file:text-secondary-foreground file:cursor-pointer">
            <p class="text-xs text-muted-foreground mt-1">
                Máx. 50 MB — Los archivos se almacenan de forma privada.
            </p>
        </div>
    @endunless

    {{-- ═══ LINK FIELDS ═══ --}}
    <div class="md:col-span-2" id="field-url" style="display: none;">
        <label for="external_url" class="block text-xs font-medium text-muted-foreground uppercase mb-1.5">URL *</label>
        <input type="url" name="external_url" id="external_url"
               value="{{ old('external_url', $file?->external_url) }}"
               class="w-full px-3 py-2.5 bg-accent border border-border rounded text-sm placeholder:text-muted-foreground focus:outline-none focus:border-primary"
               placeholder="https://ejemplo.com/recurso">
        <p class="text-xs text-muted-foreground mt-1">
            El enlace se mostrará en un iframe dentro del detalle.
        </p>
    </div>

    {{-- ═══ CAMPOS COMUNES ═══ --}}

    {{-- Nombre --}}
    <div>
        <label for="name" class="block text-xs font-medium text-muted-foreground uppercase mb-1.5">
            Nombre {{ $isEdit ? '' : '(opcional)' }}
        </label>
        <input type="text" name="name" id="name"
               value="{{ old('name', $file?->name) }}"
               class="w-full px-3 py-2.5 bg-accent border border-border rounded text-sm placeholder:text-muted-foreground focus:outline-none focus:border-primary"
               placeholder="Ej: Logo empresa, Tutorial React...">
    </div>

    {{-- Carpeta --}}
    <div>
        <label for="folder" class="block text-xs font-medium text-muted-foreground uppercase mb-1.5">Carpeta</label>
        <select name="folder" id="folder"
                class="w-full px-3 py-2.5 bg-accent border border-border rounded text-sm focus:outline-none focus:border-primary">
            <option value="">— Sin carpeta —</option>
            @foreach(\App\Models\MediaFile::FOLDERS as $folder)
                <option value="{{ $folder }}" @selected(old('folder', $file?->folder) === $folder)>{{ $folder }}</option>
            @endforeach
        </select>
    </div>

    {{-- Tags --}}
    <div class="md:col-span-2">
        <label for="tags" class="block text-xs font-medium text-muted-foreground uppercase mb-1.5">Tags</label>
        <input type="text" name="tags" id="tags"
               value="{{ old('tags', $file?->tags ? implode(', ', $file->tags) : '') }}"
               class="w-full px-3 py-2.5 bg-accent border border-border rounded text-sm placeholder:text-muted-foreground focus:outline-none focus:border-primary"
               placeholder="Separados por coma: logo, marca, 2026">
    </div>

    {{-- Descripción --}}
    <div class="md:col-span-2">
        <label for="description" class="block text-xs font-medium text-muted-foreground uppercase mb-1.5">Descripción</label>
        <textarea name="description" id="description" rows="3"
                  class="w-full px-3 py-2.5 bg-accent border border-border rounded text-sm placeholder:text-muted-foreground focus:outline-none focus:border-primary resize-y"
                  placeholder="Descripción opcional...">{{ old('description', $file?->description) }}</textarea>
    </div>

</div>

{{-- Mode toggle script --}}
@unless($isEdit)
<script>
    function setMode(mode) {
        document.getElementById('input-mode').value = mode;

        const btnFile = document.getElementById('btn-mode-file');
        const btnLink = document.getElementById('btn-mode-link');
        const fieldFile = document.getElementById('field-file');
        const fieldUrl  = document.getElementById('field-url');
        const fileInput = document.getElementById('file');
        const urlInput  = document.getElementById('external_url');

        if (mode === 'link') {
            btnLink.style.backgroundColor = 'var(--primary)';
            btnLink.style.color = 'var(--primary-foreground)';
            btnFile.style.backgroundColor = 'transparent';
            btnFile.style.color = 'inherit';
            fieldFile.style.display = 'none';
            fieldUrl.style.display  = 'block';
            fileInput.removeAttribute('required');
            urlInput.setAttribute('required', 'required');
        } else {
            btnFile.style.backgroundColor = 'var(--primary)';
            btnFile.style.color = 'var(--primary-foreground)';
            btnLink.style.backgroundColor = 'transparent';
            btnLink.style.color = 'inherit';
            fieldFile.style.display = 'block';
            fieldUrl.style.display  = 'none';
            fileInput.setAttribute('required', 'required');
            urlInput.removeAttribute('required');
        }
    }

    document.addEventListener('DOMContentLoaded', () => setMode('{{ $mode }}'));
</script>
@else
    @if($file->is_link)
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const fieldUrl = document.getElementById('field-url');
                if (fieldUrl) fieldUrl.style.display = 'block';
            });
        </script>
    @endif
@endunless