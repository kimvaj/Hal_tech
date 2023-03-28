<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PurchaseOrderRequest extends FormRequest
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
        if($this->isMethod('put') && $this->routeIs('edit.purchaseOrder')
            ||$this->isMethod('put') && $this->routeIs('edit.purchase.detail')
            || $this->isMethod('delete') && $this->routeIs('delete.purchaseOrder')
            || $this->isMethod('delete') && $this->routeIs('delete.purchase.detail')
            || $this->isMethod('post') && $this->routeIs('add.purchase.detail')

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
        if($this->isMethod('post') && $this->routeIs('add.purchase.detail')){
            return [
                'id' =>[
                    'required',
                    'numeric',
                    Rule::exists('purchase_orders', 'id')
                ],
                'order' => 'required|integer',
                'name' => 'required',
                'qty' => 'required|numeric',
                'price' => 'required|numeric',
                'description' => 'required'
            ];
        }

        if($this->isMethod('put') && $this->routeIs('edit.purchase.detail')){
            return [
                'id' =>[
                    'required',
                    'numeric',
                    Rule::exists('purchase_details', 'id')
                ],
                'name' => 'required',
                'qty' => 'required|numeric',
                'price' => 'required|numeric',
                'description' => 'required',
            ];
        }

        if($this->isMethod('put') && $this->routeIs('edit.purchaseOrder')){
            return [
                'id' =>[
                    'required',
                    'numeric',
                    Rule::exists('purchase_Orders', 'id')
                ],
                'invoice_id' =>[
                    'required',
                    'numeric',
                    Rule::exists('invoices', 'id')
                ],
                'purchase_name' =>[
                    'required',
                    'min:5',
                    'max:225'
                ],
                'date' => 'required|date',
                'note' => 'required',
                'company_id' =>[
                    'required',
                    'numeric',
                    Rule::exists('companies', 'id')
                ],
                'currency_id' =>[
                    'required',
                    'numeric',
                    Rule::exists('currencies', 'id')
                ],
                'discount' => 'required|numeric',
                'tax' => 'required|numeric',
            ];
        }

        if($this->isMethod('delete') && $this->routeIs('delete.purchase.detail')){
            return [
                'id' =>[
                    'required',
                    'numeric',
                    Rule::exists('purchase_details', 'id')
                ]
            ];
        }

        if($this->isMethod('delete') && $this->routeIs('delete.purchaseOrder')){
            return [
                'id' =>[
                    'required',
                    'numeric',
                    Rule::exists('purchase_orders', 'id')

                ]
            ];
        }
        return [
            'invoice_id' =>[
                'required',
                'numeric',
                Rule::exists('invoices', 'id')
            ],
            'purchase_name' =>[
                'required',
                'min:2',
                'max:255'
            ],
            'date' =>'required|date',
            'note' => 'required'
        ];
    }
}
