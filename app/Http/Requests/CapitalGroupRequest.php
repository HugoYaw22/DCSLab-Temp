<?php

namespace App\Http\Requests;

use App\Rules\uniqueCode;
use App\Rules\validDropDownValue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Vinkla\Hashids\Facades\Hashids;

class BranchRequest extends FormRequest
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
        $investorId = $this->has('investor_id') ? Hashids::decode($this['investor_id'])[0]:null;
        $groupId = $this->has('group_id') ? Hashids::decode($this['group_id'])[0]:null;
        $cashId = $this->has('cash_id') ? Hashids::decode($this['cash_id'])[0]:null;

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
