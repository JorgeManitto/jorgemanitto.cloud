<?php

namespace App\Http\Requests;

use App\Models\WatchlistItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WatchlistItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'           => ['required', 'string', 'max:255'],
            'type'            => ['required', Rule::in(array_keys(WatchlistItem::TYPES))],
            'status'          => ['required', Rule::in(array_keys(WatchlistItem::STATUSES))],
            'genre'           => ['nullable', 'string', 'max:100'],
            'year'            => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 2)],
            'platform'        => ['nullable', 'string', 'max:100'],
            'rating'          => ['nullable', 'integer', 'min:1', 'max:10'],
            'current_episode' => ['nullable', 'integer', 'min:0'],
            'total_episodes'  => ['nullable', 'integer', 'min:1'],
            'seasons'         => ['nullable', 'integer', 'min:1'],
            'notes'           => ['nullable', 'string', 'max:2000'],
            'poster_url'      => ['nullable', 'url', 'max:500'],
            'started_at'      => ['nullable', 'date'],
            'finished_at'     => ['nullable', 'date', 'after_or_equal:started_at'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'           => 'El título es obligatorio.',
            'finished_at.after_or_equal' => 'La fecha de finalización debe ser igual o posterior al inicio.',
        ];
    }
}
