<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidUrl;

class StoreProductRules extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'product_name' => 'required',
            'product_url' => ['required',new ValidUrl],
            'product_sku' => 'required',
            'product_description' => 'required',
            'product_color' => 'required',
            'product_size' => 'required',
            'product_uuid' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'product_name.required' => 'Field product_name is required',
            'product_url.required'  => 'Field product_url is required',
        ];
    }
}
