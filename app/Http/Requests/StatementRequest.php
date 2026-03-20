<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StatementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'holder_name'    => ['required', 'string', 'max:255'],
            'cvu'            => ['nullable', 'string', 'max:50'],
            'cuit'           => ['nullable', 'string', 'max:20'],
            'period'         => ['required', 'string', 'max:255'],
            'saldo_inicial'  => ['required', 'numeric'],
            'entradas'       => ['required', 'numeric'],
            'salidas'        => ['required', 'numeric'],
            'saldo_final'    => ['required', 'numeric'],
            'pdf'            => ['nullable', 'file', 'mimes:pdf', 'max:5120'],

            // Movimientos dinámicos
            'movements'                 => ['nullable', 'array'],
            'movements.*.date'          => ['required', 'date'],
            'movements.*.description'   => ['required', 'string', 'max:500'],
            'movements.*.operation_id'  => ['nullable', 'string', 'max:20'],
            'movements.*.amount'        => ['required', 'numeric'],
            'movements.*.balance'       => ['required', 'numeric'],
            'movements.*.type'          => ['required', 'in:income,expense'],
            'movements.*.category'      => ['nullable', 'string', 'max:100'],
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'movements.*.date.required'        => 'La fecha es obligatoria.',
            'movements.*.description.required'  => 'La descripción es obligatoria.',
            'movements.*.amount.required'       => 'El monto es obligatorio.',
            'movements.*.balance.required'      => 'El saldo es obligatorio.',
            'movements.*.type.required'         => 'El tipo es obligatorio.',
        ];
    }
}
