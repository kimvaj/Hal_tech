<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
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
        if($this->isMethod('post') && $this->routeIs('edit.company') || $this->isMethod('delete') && $this->routeIs('delete.company')){
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
        if($this->isMethod('post') && $this->routeIs('add.company')){
            return [
                'name' => [
                    'required',
                    'min:2',
                    'max:25',
                    Rule::unique('companies', 'name')
                ],
                'phone' => 'required|max:16|min:6',
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    'min:2'
                ],
                'address' => 'required',
                'logo' => [
                    'required',
                    'mimes:jpg,png,jpeg,gif,raw,tiff,psd',
                    'max:2048'
                ]
            ];
        }
        //Validation edit company
        if($this->isMethod('post') && $this->routeIs('edit.company')){
            return [
                'id' => [
                    'required',
                    'numeric',
                    Rule::exists('companies', 'id')
                ],
                'name' => [
                    'required',
                    'min:2',
                    'max:25',
                    Rule::unique('companies', 'name')
                    ->ignore($this->id)
                ],
                'phone' => 'required|max:16|min:6',
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    'min:2'
                ],
                'address' => 'required',
                'logo' => [
                    'nullable',
                    'mimes:jpg,png,jpeg,gif,raw,tiff,psd',
                    'max:2048'
                ]
            ];
        }

        //Delete
        if($this->isMethod('delete') && $this->routeIs('delete.company')){
            return [
                'id' => [
                    'required',
                    'numeric',
                    Rule::exists('companies', 'id')
                ]
            ];
        }
    }
}
