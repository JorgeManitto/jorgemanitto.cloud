<?php

namespace App\Http\Requests;

use App\Models\MediaFile;
use Illuminate\Foundation\Http\FormRequest;

class MediaFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $maxSize = 50 * 1024; // 50MB

        $rules = [
            'mode'         => ['required', 'in:file,link'],
            'name'         => ['nullable', 'string', 'max:255'],
            'folder'       => ['nullable', 'string', 'max:100'],
            'description'  => ['nullable', 'string', 'max:1000'],
            'tags'         => ['nullable', 'string', 'max:500'],
        ];

        if ($this->isMethod('POST')) {
            if ($this->input('mode') === 'link') {
                $rules['external_url'] = ['required', 'url', 'max:2048'];
            } else {
                $allowed = implode(',', MediaFile::allAllowedExtensions());
                $rules['file'] = ['required', 'file', "max:{$maxSize}", "mimes:{$allowed}"];
            }
        }

        // En update no se reemplaza archivo ni URL
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['external_url'] = ['nullable', 'url', 'max:2048'];
            unset($rules['mode']);
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'file.required'         => 'Seleccioná un archivo para subir.',
            'file.max'              => 'El archivo no puede superar los 50 MB.',
            'file.mimes'            => 'Formato de archivo no permitido.',
            'external_url.required' => 'Ingresá una URL válida.',
            'external_url.url'      => 'La URL ingresada no es válida.',
        ];
    }
}