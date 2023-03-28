<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ReceiptRequest extends FormRequest
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
        if($this->isMethod('put') && $this->routeIs('edit.receipt')
            ||$this->isMethod('put') && $this->routeIs('edit.receipt.detail')
            || $this->isMethod('delete') && $this->routeIs('delete.receipt')
            || $this->isMethod('delete') && $this->routeIs('delete.receipt.detail')
            || $this->isMethod('post') && $this->routeIs('add.receipt.detail')

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
        if($this->isMethod('post') && $this->routeIs('add.receipt.detail')){
            return [
                'id' => [
                    'required',
                    'numeric',
                    Rule::exists('receipts', 'id')
                ],
                'order' => 'required|integer',
                'name' => 'required',
                'qty' => 'required|numeric',
                'price' => 'required|numeric',
                'description' => 'required'
            ];
        }


        if($this->isMethod('put') && $this->routeIs('edit.receipt.detail')){
            return [
                'id' => [
                    'required',
                    'numeric',
                    Rule::exists('receipt_details', 'id')
                ],
                'name' => 'required',
                'qty' => 'required|numeric',
                'price' => 'required|numeric',
                'description' => 'required',
            ];
        }


        if($this->isMethod('put') && $this->routeIs('edit.receipt')){
            return [
                'id' =>[
                    'required',
                    'numeric',
                    Rule::exists('receipts', 'id')
                ],
                'invoice_id' =>[
                    'required',
                    'numeric',
                    Rule::exists('invoices', 'id')
                ],
                'receipt_name' =>[
                    'required',
                    'min:5',
                    'max:225'
                ],
                'receipt_date' => 'required|date',
                'note' => 'required',
                'company_id' => [
                    'required',
                    'numeric',
                    Rule::exists('companies', 'id')
                ],
                'currency_id' =>[
                    'required',
                    'numeric',
                    Rule::exists('currencies', 'id')
                ],
                'tax' => 'required|numeric',
                'discount' => 'required|numeric',
            ];
        }


        if($this->isMethod('delete') && $this->routeIs('delete.receipt.detail')){
            return [
                'id' =>[
                    'required',
                    'numeric',
                    Rule::exists('receipt_Details', 'id')

                ]
            ];
        }
        if($this->isMethod('delete') && $this->routeIs('delete.receipt')){
            return [
                'id' =>[
                    'required',
                    'numeric',
                    Rule::exists('Receipts', 'id')

                ]
            ];
        }
        return [
            'invoice_id' =>[
                'required',
                'numeric',
                Rule::exists('invoices', 'id')
            ],
            'receipt_name' =>[
                'required',
                'min:2',
                'max:255'
            ],
            'receipt_date' =>'required|date',
            'note' => 'required'
        ];
    }
}
