<?php

namespace App\Http\Controllers;

use App\Services\IncomeGroupService;

use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use App\Http\Requests\IncomeGroupRequest;
use App\Http\Resources\IncomeGroupResource;

class IncomeGroupController extends BaseController
{
    private $incomeGroupService;
    
    public function __construct(IncomeGroupService $incomeGroupService)
    {
        parent::__construct();

        $this->middleware('auth');
        $this->incomeGroupService = $incomeGroupService;
    }

    public function read(Request $request)
    {
        $search = $request->has('search') && !is_null($request['search']) ? $request['search']:'';
        $paginate = $request->has('paginate') ? $request['paginate']:true;
        $perPage = $request->has('perPage') ? $request['perPage']:10;

        $companyId = Hashids::decode($request['companyId'])[0];

        $result = $this->incomeGroupService->read(
            companyId: $companyId,
            search: $search,
            paginate: $paginate,
            perPage: $perPage
        );

        if (is_null($result)) {
            return response()->error();
        } else {
            $response = IncomeGroupResource::collection($result);

            return $response;
        }
    }

    public function store(IncomeGroupRequest $incomegroupRequest)
    {   
        $request = $incomegroupRequest->validated();
        
        $company_id = Hashids::decode($request['company_id'])[0];
        $code = $request['code'];
        $name = $request['name'];
        $address = $request['address'];
        $city = $request['city'];
        $contact = $request['contact'];
        $remarks = $request['remarks'];
        $status = $request['status'];

        $result = $this->incomeGroupService->create(
            $company_id,
            $code, 
            $name,
            $address,
            $city,
            $contact,
            $remarks,
            $status,
        );

        return is_null($result) ? response()->error():response()->success();
    }

    public function update($id, IncomeGroupRequest $incomegroupRequest)
    {
        $request = $incomegroupRequest->validated();

        $company_id = Hashids::decode($request['company_id'])[0];
        $code = $request['code'];
        $name = $request['name'];
        $address = $request['address'];
        $city = $request['city'];
        $contact = $request['contact'];
        $remarks = $request['remarks'];
        $status = $request['status'];

        $incomegroup = $this->incomeGroupService->update(
            $id,
            $company_id,
            $code, 
            $name,
            $address,
            $city,
            $contact,
            $remarks,
            $status,
        );

        return is_null($incomegroup) ? response()->error() : response()->success();
    }

    public function delete($id)
    {
        $result = $this->incomeGroupService->delete($id);

        return $result ? response()->error():response()->success();
    }
}
