<?php

namespace BaWe\ProsCons\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProsConsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id'    => ['required','integer'],
            'pros'          => ['array'],
            'cons'          => ['array'],
            'pros.*.text'   => ['nullable','string','max:500'],
            'cons.*.text'   => ['nullable','string','max:500'],
        ];
    }
}
