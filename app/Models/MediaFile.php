<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaFile extends Model
{
    protected $fillable = [
        'name',
        'original_name',
        'type',
        'mime_type',
        'extension',
        'size',
        'path',
        'disk',
        'external_url',
        'folder',
        'description',
        'tags',
        'thumbnail_path',
        'width',
        'height',
        'duration',
        'downloads',
    ];

    protected $casts = [
        'tags'      => 'array',
        'size'      => 'integer',
        'width'     => 'integer',
        'height'    => 'integer',
        'duration'  => 'integer',
        'downloads' => 'integer',
    ];

    /* ------------------------------------------------------------------ */
    /*  Constants                                                         */
    /* ------------------------------------------------------------------ */

    public const TYPES = [
        'image'    => 'Imagen',
        'video'    => 'Video',
        'document' => 'Documento',
        'link'     => 'Enlace',
    ];

    public const ALLOWED_EXTENSIONS = [
        'image'    => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico'],
        'video'    => ['mp4', 'avi', 'mov', 'mkv', 'webm', 'wmv', 'flv'],
        'document' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv', 'zip', 'rar'],
    ];

    public const FOLDERS = [
        'General',
        'Proyectos',
        'Clientes',
        'Facturas',
        'Contratos',
        'Marketing',
        'Personal',
        'Otro',
    ];

    /* ------------------------------------------------------------------ */
    /*  Scopes                                                            */
    /* ------------------------------------------------------------------ */

    public function scopeImages($query)
    {
        return $query->where('type', 'image');
    }

    public function scopeVideos($query)
    {
        return $query->where('type', 'video');
    }

    public function scopeDocuments($query)
    {
        return $query->where('type', 'document');
    }

    public function scopeLinks($query)
    {
        return $query->where('type', 'link');
    }

    public function scopeFolder($query, string $folder)
    {
        return $query->where('folder', $folder);
    }

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when($filters['type'] ?? null, fn ($q, $type) => $q->where('type', $type))
            ->when($filters['folder'] ?? null, fn ($q, $folder) => $q->where('folder', $folder))
            ->when($filters['search'] ?? null, fn ($q, $search) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('original_name', 'like', "%{$search}%")
                  ->orWhere('external_url', 'like', "%{$search}%");
            }))
            ->when($filters['extension'] ?? null, fn ($q, $ext) => $q->where('extension', $ext));
    }

    /* ------------------------------------------------------------------ */
    /*  Accessors                                                         */
    /* ------------------------------------------------------------------ */

    protected function typeLabel(): Attribute
    {
        return Attribute::get(fn () => self::TYPES[$this->type] ?? $this->type);
    }

    protected function typeEmoji(): Attribute
    {
        return Attribute::get(fn () => match ($this->type) {
            'image'    => '🖼️',
            'video'    => '🎥',
            'document' => '📄',
            'link'     => '🔗',
            default    => '📎',
        });
    }

    protected function typeColor(): Attribute
    {
        return Attribute::get(fn () => match ($this->type) {
            'image'    => 'primary',
            'video'    => 'warning',
            'document' => 'success',
            'link'     => 'secondary',
            default    => 'secondary',
        });
    }

    protected function sizeFormatted(): Attribute
    {
        return Attribute::get(function () {
            $bytes = $this->size;
            if (!$bytes) return '—';
            if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
            if ($bytes >= 1048576)    return number_format($bytes / 1048576, 2) . ' MB';
            if ($bytes >= 1024)       return number_format($bytes / 1024, 2) . ' KB';
            return $bytes . ' B';
        });
    }

    protected function durationFormatted(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->duration) return null;
            $h = intdiv($this->duration, 3600);
            $m = intdiv($this->duration % 3600, 60);
            $s = $this->duration % 60;
            return $h > 0
                ? sprintf('%d:%02d:%02d', $h, $m, $s)
                : sprintf('%d:%02d', $m, $s);
        });
    }

    protected function dimensionsFormatted(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->width || !$this->height) return null;
            return "{$this->width} × {$this->height}";
        });
    }

    /**
     * Para archivos privados NO hay URL pública directa.
     * Se usa la ruta de streaming del controller.
     */
    protected function streamUrl(): Attribute
    {
        return Attribute::get(function () {
            if ($this->isLink) return $this->external_url;
            return route('media.stream', $this);
        });
    }

    protected function thumbnailStreamUrl(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->thumbnail_path) return null;
            return route('media.thumbnail', $this);
        });
    }

    protected function isLink(): Attribute
    {
        return Attribute::get(fn () => $this->type === 'link');
    }

    protected function isFile(): Attribute
    {
        return Attribute::get(fn () => $this->type !== 'link');
    }

    protected function isPreviewable(): Attribute
    {
        return Attribute::get(fn () =>
            $this->type === 'image'
            || $this->type === 'video'
            || $this->type === 'link'
            || in_array($this->extension, ['pdf', 'txt', 'csv'])
        );
    }

    protected function extensionBadge(): Attribute
    {
        return Attribute::get(fn () => $this->extension ? Str::upper($this->extension) : 'LINK');
    }

    /**
     * Extraer dominio del link para mostrar.
     */
    protected function linkDomain(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->external_url) return null;
            return parse_url($this->external_url, PHP_URL_HOST);
        });
    }

    /* ------------------------------------------------------------------ */
    /*  Helpers                                                           */
    /* ------------------------------------------------------------------ */

    public static function resolveType(string $extension): string
    {
        $ext = Str::lower($extension);

        foreach (self::ALLOWED_EXTENSIONS as $type => $extensions) {
            if (in_array($ext, $extensions)) {
                return $type;
            }
        }

        return 'document';
    }

    public static function allAllowedExtensions(): array
    {
        return array_merge(...array_values(self::ALLOWED_EXTENSIONS));
    }

    public function incrementDownloads(): void
    {
        $this->increment('downloads');
    }

    /**
     * Delete the physical file from private storage.
     */
    public function deleteFile(): bool
    {
        if ($this->is_link || !$this->path) return true;

        $deleted = Storage::disk($this->disk)->delete($this->path);

        if ($this->thumbnail_path) {
            Storage::disk($this->disk)->delete($this->thumbnail_path);
        }

        return $deleted;
    }

    /**
     * Check if the physical file exists in storage.
     */
    public function fileExists(): bool
    {
        if ($this->is_link) return true;
        if (!$this->path) return false;
        return Storage::disk($this->disk)->exists($this->path);
    }
}