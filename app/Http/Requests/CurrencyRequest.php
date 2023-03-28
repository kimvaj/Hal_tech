<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CurrencyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth('api')->user()->hasRole('superadmin|admin');
    }

    public function prepareForValidation()
    {
        if($this->isMethod('put') && $this->routeIs('edit.currency') || $this->isMethod('delete') && $this->routeIs('delete.currency')){
            $this->merge([
                'id' => $this->route()->parameters['id'],
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        //Edit
        if($this->isMethod('put') && $this->routeIs('edit.currency')){
            return [
                'id' => [
                    'required',
                    'numeric',
                    Rule::exists('currencies', 'id')
                ],
                'name' => [
                    'required',
                    'max:25',
                    'min:2',
                    Rule::unique('currencies', 'name')
                    ->ignore($this->id)
                ],
                'short_name' => 'required'
            ];

        }

        if($this->isMethod('delete') && $this->routeIs('delete.currency')){
            return [
                'id' => [
                    'required',
                    'numeric',
                    Rule::exists('currencies', 'id')
                ]
            ];
        }
        if($this->isMethod('post') && $this->routeIs('add.currency')){
            return [
                'name' => [
                    'required',
                    'max:25',
                    'min:2',
                    Rule::unique('currencies', 'name')
                 ],
                'short_name' => 'required'
          ];
        }
    }
}
