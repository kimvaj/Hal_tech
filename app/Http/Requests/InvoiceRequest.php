<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
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
        if($this->isMethod('put') && $this->routeIs('edit.invoice')
            || $this->isMethod('delete') && $this->routeIs('delete.invoice')
            || $this->isMethod('put') && $this->routeIs('edit.invoice.detail')
            || $this->isMethod('post') && $this->routeIs('add.invoice.detail')
            || $this->isMethod('delete') && $this->routeIs('delete.invoice.detail')
            || $this->isMethod('put') && $this->routeIs('update.invoice.status')

        ){
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
        if($this->isMethod('put') && $this->routeIs('edit.invoice')){
            return [
                'id' => [
                    'required',
                    'numeric',
                    Rule::exists('invoices', 'id')
                ],
                'invoice_name' => [
                    'required',
                    'min:5',
                    'max:255'
                ],
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'note' => 'required',
                'company_id' => [
                    'required',
                    'numeric',
                    Rule::exists('companies', 'id')
                ],
                'currency_id' => [
                    'required',
                    'numeric',
                    Rule::exists('currencies', 'id')
                ],
                'tax' => 'required|numeric',
                'discount' => 'required|numeric',

            ];
        }

        if($this->isMethod('put') && $this->routeIs('edit.invoice.detail')){
            return [
                'id' => [
                    'required',
                    'numeric',
                    Rule::exists('invoice_details', 'id')
                ],
                'name' => 'required',
                'qty' => 'required|numeric',
                'price' => 'required|numeric',
                'description' => 'required',
            ];

        }

        if($this->isMethod('post') && $this->routeIs('add.invoice.detail')){
            return [
                'id' => [
                    'required',
                    'numeric',
                    Rule::exists('invoices', 'id')
                ],
                'order' => 'required|integer',
                'name' => 'required',
                'qty' => 'required|numeric',
                'price' => 'required|numeric',
                'description' => 'required'
            ];
        }

        if($this->isMethod('delete') && $this->routeIs('delete.invoice.detail')){
            return [
                'id' => [
                    'required',
                    'numeric',
                    Rule::exists('invoice_details', 'id')
                ]
            ];

        }

        if($this->isMethod('delete') && $this->routeIs('delete.invoice')){
            return [
                'id' => [
                    'required',
                    'numeric',
                    Rule::exists('invoices', 'id')
                ]
            ];

        }

        if($this->isMethod('put') && $this->routeIs('update.invoice.status')){
            return [
                'id' => [
                    'required',
                    'numeric',
                    Rule::exists('invoices', 'id')
                ],
                'status' => [
                    'required',
                    Rule::in('pending', 'completed', 'paid', 'cancel')
                ]
            ];
        }
        return [
            'invoice_name' =>[
                'required',
                'min:5',
                'max:255'
            ],
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'note' => 'required',
            'company_id' => [
                'required',
                'numeric',
                Rule::exists('companies', 'id')
            ],
            'currency_id' => [
                'required',
                'numeric',
                Rule::exists('currencies', 'id')
            ],
            'tax' => 'required|numeric',
            'discount' => 'required|numeric',
            'invoice_details' => 'required|array',
            'invoice_details.*.order' => 'required|numeric',
            'invoice_details.*.name' => 'required',
            'invoice_details.*.qty' => 'required|numeric',
            'invoice_details.*.price' => 'required|numeric',
            'invoice_details.*.description' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'start_date.required' => __('validation.required')
        ];
    }


}
