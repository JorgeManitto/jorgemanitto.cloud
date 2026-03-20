{{-- resources/views/pages/watchlist/_form.blade.php --}}
@php
    $item = $item ?? null;
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

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Título --}}
    <div class="md:col-span-2">
        <label for="title" class="block text-xs font-medium text-muted-foreground uppercase mb-1.5">Título *</label>
        <input type="text" name="title" id="title"
               value="{{ old('title', $item?->title) }}"
               required
               class="w-full px-3 py-2.5 bg-accent border border-border rounded text-sm placeholder:text-muted-foreground focus:outline-none focus:border-primary"
               placeholder="Ej: Breaking Bad, Inception...">
    </div>

    {{-- Tipo --}}
    <div>
        <label for="type" class="block text-xs font-medium text-muted-foreground uppercase mb-1.5">Tipo *</label>
        <select name="type" id="type" required
                class="w-full px-3 py-2.5 bg-accent border border-border rounded text-sm focus:outline-none focus:border-primary">
            @foreach(\App\Models\WatchlistItem::TYPES as $key => $label)
                <option value="{{ $key }}" @selected(old('type', $item?->type) === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    {{-- Estado --}}
    <div>
        <label for="status" class="block text-xs font-medium text-muted-foreground uppercase mb-1.5">Estado *</label>
        <select name="status" id="status" required
                class="w-full px-3 py-2.5 bg-accent border border-border rounded text-sm focus:outline-none focus:border-primary">
            @foreach(\App\Models\WatchlistItem::STATUSES as $key => $label)
                <option value="{{ $key }}" @selected(old('status', $item?->status) === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    {{-- Género --}}
    <div>
        <label for="genre" class="block text-xs font-medium text-muted-foreground uppercase mb-1.5">Género</label>
        <select name="genre" id="genre"
                class="w-full px-3 py-2.5 bg-accent border border-border rounded text-sm focus:outline-none focus:border-primary">
            <option value="">— Seleccionar —</option>
            @foreach(\App\Models\WatchlistItem::GENRES as $genre)
                <option value="{{ $genre }}" @selected(old('genre', $item?->genre) === $genre)>{{ $genre }}</option>
            @endforeach
        </select>
    </div>

    {{-- Plataforma --}}
    <div>
        <label for="platform" class="block text-xs font-medium text-muted-foreground uppercase mb-1.5">Plataforma</label>
        <select name="platform" id="platform"
                class="w-full px-3 py-2.5 bg-accent border border-border rounded text-sm focus:outline-none focus:border-primary">
            <option value="">— Seleccionar —</option>
            @foreach(\App\Models\WatchlistItem::PLATFORMS as $platform)
                <option value="{{ $platform }}" @selected(old('platform', $item?->platform) === $platform)>{{ $platform }}</option>
            @endforeach
        </select>
    </div>

    {{-- Año --}}
    <div>
        <label for="year" class="block text-xs font-medium text-muted-foreground uppercase mb-1.5">Año</label>
        <input type="number" name="year" id="year"
               value="{{ old('year', $item?->year) }}"
               min="1900" max="{{ date('Y') + 2 }}"
               class="w-full px-3 py-2.5 bg-accent border border-border rounded text-sm placeholder:text-muted-foreground focus:outline-none focus:border-primary"
               placeholder="{{ date('Y') }}">
    </div>

    {{-- Rating --}}
    <div>
        <label for="rating" class="block text-xs font-medium text-muted-foreground uppercase mb-1.5">Rating (1-10)</label>
        <input type="number" name="rating" id="rating"
               value="{{ old('rating', $item?->rating) }}"
               min="1" max="10"
               class="w-full px-3 py-2.5 bg-accent border border-border rounded text-sm placeholder:text-muted-foreground focus:outline-none focus:border-primary"
               placeholder="1-10">
    </div>

    {{-- Campos específicos de Series --}}
    <div class="md:col-span-2 p-4 rounded border border-border bg-accent" id="series-fields">
        <p class="text-xs font-medium text-muted-foreground uppercase mb-3">📺 Datos de Serie (opcional)</p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label for="seasons" class="block text-xs text-muted-foreground mb-1">Temporadas</label>
                <input type="number" name="seasons" id="seasons"
                       value="{{ old('seasons', $item?->seasons) }}"
                       min="1"
                       class="w-full px-3 py-2 bg-background border border-border rounded text-sm focus:outline-none focus:border-primary">
            </div>
            <div>
                <label for="current_episode" class="block text-xs text-muted-foreground mb-1">Episodio actual</label>
                <input type="number" name="current_episode" id="current_episode"
                       value="{{ old('current_episode', $item?->current_episode) }}"
                       min="0"
                       class="w-full px-3 py-2 bg-background border border-border rounded text-sm focus:outline-none focus:border-primary">
            </div>
            <div>
                <label for="total_episodes" class="block text-xs text-muted-foreground mb-1">Total episodios</label>
                <input type="number" name="total_episodes" id="total_episodes"
                       value="{{ old('total_episodes', $item?->total_episodes) }}"
                       min="1"
                       class="w-full px-3 py-2 bg-background border border-border rounded text-sm focus:outline-none focus:border-primary">
            </div>
        </div>
    </div>

    {{-- Fechas --}}
    <div>
        <label for="started_at" class="block text-xs font-medium text-muted-foreground uppercase mb-1.5">Fecha inicio</label>
        <input type="date" name="started_at" id="started_at"
               value="{{ old('started_at', $item?->started_at?->format('Y-m-d')) }}"
               class="w-full px-3 py-2.5 bg-accent border border-border rounded text-sm focus:outline-none focus:border-primary">
    </div>

    <div>
        <label for="finished_at" class="block text-xs font-medium text-muted-foreground uppercase mb-1.5">Fecha fin</label>
        <input type="date" name="finished_at" id="finished_at"
               value="{{ old('finished_at', $item?->finished_at?->format('Y-m-d')) }}"
               class="w-full px-3 py-2.5 bg-accent border border-border rounded text-sm focus:outline-none focus:border-primary">
    </div>

    {{-- Poster URL --}}
    <div class="md:col-span-2">
        <label for="poster_url" class="block text-xs font-medium text-muted-foreground uppercase mb-1.5">Poster URL</label>
        <input type="url" name="poster_url" id="poster_url"
               value="{{ old('poster_url', $item?->poster_url) }}"
               class="w-full px-3 py-2.5 bg-accent border border-border rounded text-sm placeholder:text-muted-foreground focus:outline-none focus:border-primary"
               placeholder="https://image.tmdb.org/...">
    </div>

    {{-- Notas --}}
    <div class="md:col-span-2">
        <label for="notes" class="block text-xs font-medium text-muted-foreground uppercase mb-1.5">Notas</label>
        <textarea name="notes" id="notes" rows="3"
                  class="w-full px-3 py-2.5 bg-accent border border-border rounded text-sm placeholder:text-muted-foreground focus:outline-none focus:border-primary resize-y"
                  placeholder="Comentarios, recomendado por...">{{ old('notes', $item?->notes) }}</textarea>
    </div>

</div>

{{-- Toggle series fields visibility --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.getElementById('type');
        const seriesFields = document.getElementById('series-fields');

        function toggleFields() {
            seriesFields.style.display = typeSelect.value === 'series' ? 'block' : 'none';
        }

        typeSelect.addEventListener('change', toggleFields);
        toggleFields();
    });
</script>
