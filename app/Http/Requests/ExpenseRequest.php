<?php

namespace App\Http\Requests;

use App\Rules\uniqueCode;
use App\Rules\validDropDownValue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Vinkla\Hashids\Facades\Hashids;

class ExpenseRequest extends FormRequest
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
            'remarks' => 'nullable',
            'posted' => 'nullable',
        ];

        $currentRouteMethod = $this->route()->getActionMethod();
        switch($currentRouteMethod) {
            case 'store':
                $rules_store = [
                    'company_id' => ['required', 'bail'],
                    'branch_id' => ['required', 'bail'],
                    'expense_group_id' => ['required', 'bail'],
                    'cash_id' => ['required', 'bail'],
                    'code' => ['required', 'max:255', new uniqueCode(table: 'branches', companyId: $companyId)],
                    'payment_term_type' => 'required|integer|digits_between:1,11',
                    'amount' => 'required|integer|digits_between:1,19',
                    'amount_owed' => 'required|integer|digits_between:1,19',
                ];
                return array_merge($rules_store, $nullableArr);
            case 'update':
                $rules_update = [
                    'company_id' => ['required', 'bail'],
                    'branch_id' => ['required', 'bail'],
                    'expense_group_id' => ['required', 'bail'],
                    'cash_id' => ['required', 'bail'],
                    'code' => new uniqueCode(table: 'branches', companyId: $companyId, exceptId: $this->route('id')),
                    'payment_term_type' => 'required|integer|digits_between:1,11',
                    'amount' => 'required|integer|digits_between:1,19',
                    'amount_owed' => 'required|integer|digits_between:1,19',
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
