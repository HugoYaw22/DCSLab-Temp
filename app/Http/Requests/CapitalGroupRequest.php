<?php

namespace App\Http\Requests;

use App\Rules\uniqueCode;
use App\Rules\validDropDownValue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Vinkla\Hashids\Facades\Hashids;

class CapitalGroupRequest extends FormRequest
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

        $nullableArr = [
            'date' => 'nullable',
            'remarks' => 'nullable',
        ];

        $currentRouteMethod = $this->route()->getActionMethod();
        switch($currentRouteMethod) {
            case 'store':
                $rules_store = [
                    'investor_id' => ['required', 'bail'],
                    'group_id' => ['required', 'bail'],
                    'cash_id' => ['required', 'bail'],
                    'ref_number' => 'required|integer|digits_between:1,255',
                    'group_id' => 'required',
                    'capital_status' => 'required',
                    'amount' => 'required|integer|digits_between:1,19',
                ];
                return array_merge($rules_store, $nullableArr);
            case 'update':
                $rules_update = [
                    'company_id' => ['required', 'bail'],
                    'ref_number' => 'required|integer|digits_between:1,255',
                    'group_id' => 'required',
                    'capital_status' => 'required',
                    'amount' => 'required|integer|digits_between:1,19',
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
