<?php

namespace App\Http\Controllers;

use App\Services\CustomerGroupService;

use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use App\Http\Requests\CustomerGroupRequest;
use App\Http\Resources\CustomerGroupResource;

class CustomerGroupController extends BaseController
{
    private $customerGroupService;
    
    public function __construct(CustomerGroupService $customerGroupService)
    {
        parent::__construct();

        $this->middleware('auth');
        $this->customerGroupService = $customerGroupService;
    }

    public function read(Request $request)
    {
        $search = $request->has('search') && !is_null($request['search']) ? $request['search']:'';
        $paginate = $request->has('paginate') ? $request['paginate']:true;
        $perPage = $request->has('perPage') ? $request['perPage']:10;

        $companyId = Hashids::decode($request['companyId'])[0];

        $result = $this->customerGroupService->read(
            companyId: $companyId,
            search: $search,
            paginate: $paginate,
            perPage: $perPage
        );

        if (is_null($result)) {
            return response()->error();
        } else {
            $response = CustomerGroupResource::collection($result);

            return $response;
        }
    }

    public function store(CustomerGroupRequest $customergroupRequest)
    {   
        $request = $customergroupRequest->validated();
        
        $company_id = Hashids::decode($request['company_id'])[0];
        $code = $request['code'];
        $name = $request['name'];
        $max_open_invoice = $request['max_open_invoice'];
        $max_outstanding_invoice = $request['max_outstanding_invoice'];
        $max_invoice_age = $request['max_invoice_age'];
        $payment_term = $request['payment_term'];
        $selling_point = $request['selling_point'];
        $selling_point_multiple = $request['selling_point_multiple'];
        $sell_at_cost = $request['sell_at_cost'];
        $price_markup_percent = $request['price_markup_percent'];
        $price_markup_nominal = $request['price_markup_nominal'];
        $price_markdown_percent = $request['price_markdown_percent'];
        $price_markdown_nominal = $request['price_markdown_nominal'];
        $round_on = $request['round_on'];
        $round_digit = $request['round_digit'];
        $remarks = $request['remarks'];
        $cash_id = Hashids::decode($request['cash_id'])[0];

        $result = $this->customerGroupService->create(
            $company_id,
            $code,
            $name,
            $max_open_invoice,
            $max_outstanding_invoice,
            $max_invoice_age,
            $payment_term,
            $selling_point,
            $selling_point_multiple,
            $sell_at_cost,
            $price_markup_percent,
            $price_markup_nominal,
            $price_markdown_percent,
            $price_markdown_nominal,
            $round_on,
            $round_digit,
            $remarks,
            $cash_id,
        );

        return is_null($result) ? response()->error():response()->success();
    }

    public function update($id, CustomerGroupRequest $customergroupRequest)
    {
        $request = $customergroupRequest->validated();

        $company_id = Hashids::decode($request['company_id'])[0];
        $code = $request['code'];
        $name = $request['name'];
        $max_open_invoice = $request['max_open_invoice'];
        $max_outstanding_invoice = $request['max_outstanding_invoice'];
        $max_invoice_age = $request['max_invoice_age'];
        $payment_term = $request['payment_term'];
        $selling_point = $request['selling_point'];
        $selling_point_multiple = $request['selling_point_multiple'];
        $sell_at_cost = $request['sell_at_cost'];
        $price_markup_percent = $request['price_markup_percent'];
        $price_markup_nominal = $request['price_markup_nominal'];
        $price_markdown_percent = $request['price_markdown_percent'];
        $price_markdown_nominal = $request['price_markdown_nominal'];
        $round_on = $request['round_on'];
        $round_digit = $request['round_digit'];
        $remarks = $request['remarks'];
        $cash_id = Hashids::decode($request['cash_id'])[0];

        $customergroup = $this->customerGroupService->update(
            $id,
            $company_id,
            $code,
            $name,
            $max_open_invoice,
            $max_outstanding_invoice,
            $max_invoice_age,
            $payment_term,
            $selling_point,
            $selling_point_multiple,
            $sell_at_cost,
            $price_markup_percent,
            $price_markup_nominal,
            $price_markdown_percent,
            $price_markdown_nominal,
            $round_on,
            $round_digit,
            $remarks,
            $cash_id,
        );

        return is_null($customergroup) ? response()->error() : response()->success();
    }

    public function delete($id)
    {
        $result = $this->customerGroupService->delete($id);

        return $result ? response()->error():response()->success();
    }
}
