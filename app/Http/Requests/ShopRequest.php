<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShopRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'shop_name' => 'required|string|max:255',
            'shop_description' => 'nullable|string|max:255',
            'shop_address' => 'required|string|max:255',
            'opening_time' => 'required',
            'closing_time' => 'required',
        ];
    }
}
