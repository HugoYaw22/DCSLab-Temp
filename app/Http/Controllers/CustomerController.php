<?php

namespace App\Http\Controllers;

use App\Services\CustomerService;

use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CustomerResource;

class CustomerController extends BaseController
{
    private $customerService;
    
    public function __construct(CustomerService $customerService)
    {
        parent::__construct();

        $this->middleware('auth');
        $this->customerService = $customerService;
    }

    public function read(Request $request)
    {
        $search = $request->has('search') && !is_null($request['search']) ? $request['search']:'';
        $paginate = $request->has('paginate') ? $request['paginate']:true;
        $perPage = $request->has('perPage') ? $request['perPage']:10;

        $companyId = Hashids::decode($request['companyId'])[0];

        $result = $this->customerService->read(
            companyId: $companyId,
            search: $search,
            paginate: $paginate,
            perPage: $perPage
        );

        if (is_null($result)) {
            return response()->error();
        } else {
            $response = CustomerResource::collection($result);

            return $response;
        }
    }

    public function store(CustomerRequest $customerRequest)
    {   
        $request = $customerRequest->validated();
        
        $company_id = Hashids::decode($request['company_id'])[0];
        $code = $request['code'];
        $name = $request['name'];
        $is_member = $request['is_member'];
        $customer_group_id = Hashids::decode($request['customer_group_id'])[0];
        $zone = $request['zone'];
        $max_open_invoice = $request['max_open_invoice'];
        $max_outstanding_invoice = $request['max_outstanding_invoice'];
        $max_invoice_age = $request['max_invoice_age'];
        $payment_term = $request['payment_term'];
        $tax_id = $request['tax_id'];
        $remarks = $request['remarks'];
        $status = $request['status'];

        $customer_addresses = [];
        $count_address = count($request['address']);
        for ($i = 0; $i < $count_address; $i++) {
            array_push($customer_addresses, array (
                'company_id' => $company_id,
                'customer_id' => null,
                'address' => $request['address'][$i],
                'city' => $request['city'][$i],
                'contact' => $request['contact'][$i],
                'address_remarks' => $request['address_remarks'][$i]
            ));
        }

        $result = $this->customerService->create(
            $company_id,
            $code, 
            $name,
            $is_member,
            $customer_group_id,
            $zone,
            $max_open_invoice,
            $max_outstanding_invoice,
            $max_invoice_age,
            $payment_term,
            $tax_id,
            $remarks,
            $status,
            $customer_addresses,
        );

        return is_null($result) ? response()->error():response()->success();
    }

    public function update($id, CustomerRequest $customerRequest)
    {
        $request = $customerRequest->validated();

        $company_id = Hashids::decode($request['company_id'])[0];
        $code = $request['code'];
        $name = $request['name'];
        $is_member = $request['is_member'];
        $customer_group_id = Hashids::decode($request['customer_group_id'])[0];
        $zone = $request['zone'];
        $max_open_invoice = $request['max_open_invoice'];
        $max_outstanding_invoice = $request['max_outstanding_invoice'];
        $max_invoice_age = $request['max_invoice_age'];
        $payment_term = $request['payment_term'];
        $tax_id = $request['tax_id'];
        $remarks = $request['remarks'];
        $status = $request['status'];

        $customer_addresses = [];
        array_push($customer_addresses, array (
            'company_id' => $company_id,
            'customer_id' => null,
            'address' => $request['address'],
            'city' => $request['city'],
            'contact' => $request['contact'],
            'remarks' => ''
        ));
        
        $customer_addresses = [];
        if (empty($request['customer_address_hId']) === false) {
            $count_address = count($request['address']);

            for ($i = 0; $i < $count_address; $i++) {
                $customer_address_id = $request['customer_address_hId'][$i] != null ? Hashids::decode($request['customer_address_hId'][$i])[0] : null;
                
                array_push($customer_addresses, array (
                    'id' => $customer_address_id,
                    'company_id' => $company_id,
                    'customer_id' => null,
                    'address' => $request['address'][$i],
                    'city' => $request['city'][$i],
                    'contact' => $request['contact'][$i],
                    'address_remarks' => $request['address_remarks'][$i]
                ));
            }
        }

        $customer = $this->customerService->update(
            $id,
            $company_id,
            $code, 
            $name,
            $is_member,
            $customer_group_id,
            $zone,
            $max_open_invoice,
            $max_outstanding_invoice,
            $max_invoice_age,
            $payment_term,
            $tax_id,
            $remarks,
            $status,
            $customer_addresses,
        );

        return is_null($customer) ? response()->error() : response()->success();
    }

    public function delete($id)
    {
        $result = $this->customerService->delete($id);

        return $result ? response()->error():response()->success();
    }
}
