<?php

namespace App\Http\Controllers;

use App\Http\Requests\MediaFileRequest;
use App\Models\MediaFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaFileController extends Controller
{
    /**
     * Listado con filtros y estadísticas.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['type', 'folder', 'search', 'extension']);

        $files = MediaFile::query()
            ->filter($filters)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total'      => MediaFile::count(),
            'images'     => MediaFile::images()->count(),
            'videos'     => MediaFile::videos()->count(),
            'documents'  => MediaFile::documents()->count(),
            'links'      => MediaFile::links()->count(),
            'total_size' => MediaFile::sum('size'),
        ];

        $usedFolders = MediaFile::whereNotNull('folder')
            ->distinct()
            ->pluck('folder')
            ->sort()
            ->values();

        return view('pages.media.index', compact('files', 'filters', 'stats', 'usedFolders'));
    }

    /**
     * Formulario de subida / creación de link.
     */
    public function create()
    {
        return view('pages.media.create');
    }

    /**
     * Guardar archivo o link.
     */
    public function store(MediaFileRequest $request)
    {
        $tags = $this->parseTags($request->tags);

        // ── Link ──
        if ($request->input('mode') === 'link') {
            $url  = $request->external_url;
            $name = $request->filled('name') ? $request->name : $this->domainFromUrl($url);

            MediaFile::create([
                'name'         => $name,
                'type'         => 'link',
                'disk'         => 'private',
                'external_url' => $url,
                'folder'       => $request->folder ?: null,
                'description'  => $request->description,
                'tags'         => $tags,
            ]);

            return redirect()->route('media.index')
                ->with('success', "Enlace «{$name}» guardado correctamente.");
        }

        // ── Archivo ──
        $uploaded     = $request->file('file');
        $extension    = Str::lower($uploaded->getClientOriginalExtension());
        $type         = MediaFile::resolveType($extension);
        $originalName = $uploaded->getClientOriginalName();
        $name         = $request->filled('name') ? $request->name : pathinfo($originalName, PATHINFO_FILENAME);

        $directory = "media/{$type}/" . now()->format('Y/m');
        $path      = $uploaded->store($directory, 'private');

        $width = $height = null;
        if ($type === 'image' && function_exists('getimagesize')) {
            $info = @getimagesize($uploaded->getRealPath());
            if ($info) [$width, $height] = $info;
        }

        MediaFile::create([
            'name'          => $name,
            'original_name' => $originalName,
            'type'          => $type,
            'mime_type'     => $uploaded->getMimeType(),
            'extension'     => $extension,
            'size'          => $uploaded->getSize(),
            'path'          => $path,
            'disk'          => 'private',
            'folder'        => $request->folder ?: null,
            'description'   => $request->description,
            'tags'          => $tags,
            'width'         => $width,
            'height'        => $height,
        ]);

        return redirect()->route('media.index')
            ->with('success', "Archivo «{$name}» subido correctamente.");
    }

    /**
     * Detalle.
     */
    public function show(MediaFile $medium)
    {
        return view('pages.media.show', ['file' => $medium]);
    }

    /**
     * Formulario de edición (metadatos).
     */
    public function edit(MediaFile $medium)
    {
        return view('pages.media.edit', ['file' => $medium]);
    }

    /**
     * Actualizar metadatos (y URL si es link).
     */
    public function update(MediaFileRequest $request, MediaFile $medium)
    {
        $data = [
            'name'        => $request->name ?: $medium->name,
            'folder'      => $request->folder ?: null,
            'description' => $request->description,
            'tags'        => $this->parseTags($request->tags),
        ];

        // Si es link, permitir cambiar la URL
        if ($medium->is_link && $request->filled('external_url')) {
            $data['external_url'] = $request->external_url;
        }

        $medium->update($data);

        return redirect()->route('media.show', $medium)
            ->with('success', 'Actualizado correctamente.');
    }

    /**
     * Eliminar (archivo físico + registro).
     */
    public function destroy(MediaFile $medium)
    {
        $medium->deleteFile();
        $medium->delete();

        return redirect()->route('media.index')
            ->with('success', 'Eliminado correctamente.');
    }

    /* ------------------------------------------------------------------ */
    /*  Streaming privado — sirve archivos sin exponerlos públicamente     */
    /* ------------------------------------------------------------------ */

    /**
     * Stream del archivo desde storage privado.
     */
    public function stream(MediaFile $medium)
    {
        if ($medium->is_link) {
            return redirect($medium->external_url);
        }

        abort_unless($medium->fileExists(), 404, 'Archivo no encontrado en storage.');

        return Storage::disk($medium->disk)->response($medium->path, $medium->original_name, [
            'Content-Type' => $medium->mime_type,
        ]);
    }

    /**
     * Stream de thumbnail privado.
     */
    public function thumbnail(MediaFile $medium)
    {
        if (!$medium->thumbnail_path || !Storage::disk($medium->disk)->exists($medium->thumbnail_path)) {
            abort(404);
        }

        return Storage::disk($medium->disk)->response($medium->thumbnail_path);
    }

    /**
     * Descarga forzada del archivo.
     */
    public function download(MediaFile $medium)
    {
        if ($medium->is_link) {
            return redirect($medium->external_url);
        }

        abort_unless($medium->fileExists(), 404, 'Archivo no encontrado en storage.');

        $medium->incrementDownloads();

        return Storage::disk($medium->disk)->download(
            $medium->path,
            $medium->original_name
        );
    }

    /**
     * Subida múltiple.
     */
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'files'   => ['required', 'array', 'max:20'],
            'files.*' => ['file', 'mimes:' . implode(',', MediaFile::allAllowedExtensions())],
            'folder'  => ['nullable', 'string', 'max:100'],
        ]);

        $uploaded = [];

        foreach ($request->file('files') as $file) {
            $extension    = Str::lower($file->getClientOriginalExtension());
            $type         = MediaFile::resolveType($extension);
            $originalName = $file->getClientOriginalName();
            $directory    = "media/{$type}/" . now()->format('Y/m');
            $path         = $file->store($directory, 'private');

            $width = $height = null;
            if ($type === 'image' && function_exists('getimagesize')) {
                $info = @getimagesize($file->getRealPath());
                if ($info) [$width, $height] = $info;
            }

            $uploaded[] = MediaFile::create([
                'name'          => pathinfo($originalName, PATHINFO_FILENAME),
                'original_name' => $originalName,
                'type'          => $type,
                'mime_type'     => $file->getMimeType(),
                'extension'     => $extension,
                'size'          => $file->getSize(),
                'path'          => $path,
                'disk'          => 'private',
                'folder'        => $request->folder ?: null,
                'width'         => $width,
                'height'        => $height,
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'count' => count($uploaded)]);
        }

        return redirect()->route('media.index')
            ->with('success', count($uploaded) . ' archivos subidos correctamente.');
    }

    /* ------------------------------------------------------------------ */
    /*  Private helpers                                                    */
    /* ------------------------------------------------------------------ */

    private function parseTags(?string $raw): ?array
    {
        if (!$raw) return null;
        $tags = array_map('trim', explode(',', $raw));
        $tags = array_filter($tags);
        return count($tags) ? array_values($tags) : null;
    }

    private function domainFromUrl(string $url): string
    {
        return parse_url($url, PHP_URL_HOST) ?: $url;
    }
}