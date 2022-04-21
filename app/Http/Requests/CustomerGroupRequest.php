<?php

namespace App\Http\Requests;

use App\Rules\uniqueCode;
use App\Rules\validDropDownValue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Vinkla\Hashids\Facades\Hashids;

class CustomerGroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $companyId = $this->has('company_id') ? Hashids::decode($this['company_id'])[0]:null;

        $nullableArr = [
            'sell_at_cost' => 'nullable',
            'round_on' => 'nullable',
            'remarks' => 'nullable',
        ];

        $currentRouteMethod = $this->route()->getActionMethod();
        switch($currentRouteMethod) {
            case 'store':
                $rules_store = [
                    'code' => ['required', 'max:255', new uniqueCode(table: 'branches', companyId: $companyId)],
                    'name' => 'required|min:3|max:255',
                    'max_open_invoice' => 'required|integer|digits_between:1,11',
                    'max_outstanding_invoice' => 'required|integer|digits_between:1,16',
                    'max_invoice_age' => 'required|integer|digits_between:1,11',
                    'payment_term' => 'required|integer|digits_between:1,11',
                    'selling_point' => 'required|integer|digits_between:1,8',
                    'selling_point_multiple' => 'required|integer|digits_between:1,16',
                    'price_markup_percent' => 'required|integer|digits_between:1,16',
                    'price_markup_nominal' => 'required|integer|digits_between:1,16',
                    'price_markdown_percent' => 'required|integer|digits_between:1,16',
                    'price_markdown_nominal' => 'required|integer|digits_between:1,16',
                    'round_digit' => 'required|integer|digits_between:1,11',
                    'cash_id' => ['required', 'bail'],
                ];
                return array_merge($rules_store, $nullableArr);
            case 'update':
                $rules_update = [         
                    'code' => new uniqueCode(table: 'branches', companyId: $companyId, exceptId: $this->route('id')),
                    'name' => 'required|min:3|max:255',
                    'max_open_invoice' => 'required|integer|digits_between:1,11',
                    'max_outstanding_invoice' => 'required|integer|digits_between:1,16',
                    'max_invoice_age' => 'required|integer|digits_between:1,11',
                    'payment_term' => 'required|integer|digits_between:1,11',
                    'selling_point' => 'required|integer|digits_between:1,8',
                    'selling_point_multiple' => 'required|integer|digits_between:1,16',
                    'price_markup_percent' => 'required|integer|digits_between:1,16',
                    'price_markup_nominal' => 'required|integer|digits_between:1,16',
                    'price_markdown_percent' => 'required|integer|digits_between:1,16',
                    'price_markdown_nominal' => 'required|integer|digits_between:1,16',
                    'round_digit' => 'required|integer|digits_between:1,11',
                    'cash_id' => ['required', 'bail'],
                ];
                return array_merge($rules_update, $nullableArr);
            default:
                return [
                    '' => 'required'
                ];
        }
    }

    public function attributes()
    {
        return [
            'company_id' => trans('validation_attributes.company'),
        ];
    }
}
