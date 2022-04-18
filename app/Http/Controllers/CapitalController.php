<?php

namespace App\Http\Controllers;

use App\Services\CapitalService;

use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use App\Http\Requests\CapitalRequest;
use App\Http\Resources\CapitalResource;

class CapitalController extends BaseController
{
    private $capitalService;
    
    public function __construct(CapitalService $capitalService)
    {
        parent::__construct();

        $this->middleware('auth');
        $this->capitalService = $capitalService;
    }

    public function read(Request $request)
    {
        $search = $request->has('search') && !is_null($request['search']) ? $request['search']:'';
        $paginate = $request->has('paginate') ? $request['paginate']:true;
        $perPage = $request->has('perPage') ? $request['perPage']:10;

        $companyId = Hashids::decode($request['companyId'])[0];

        $result = $this->capitalService->read(
            companyId: $companyId,
            search: $search,
            paginate: $paginate,
            perPage: $perPage
        );

        if (is_null($result)) {
            return response()->error();
        } else {
            $response = CapitalResource::collection($result);

            return $response;
        }
    }

    public function store(CapitalRequest $capitalRequest)
    {   
        $request = $capitalRequest->validated();
        
        $company_id = Hashids::decode($request['company_id'])[0];
        $investor_id = Hashids::decode($request['investor_id'])[0];
        $group_id = Hashids::decode($request['group_id'])[0];
        $cash_id = Hashids::decode($request['cash_id'])[0];
        $date = $request['date'];
        $capital_status = $request['capital_status'];
        $amount = $request['amount'];
        $remarks = $request['remarks'];

        $result = $this->capitalService->create(
            $company_id,
            $investor_id, 
            $group_id,
            $cash_id,
            $date,
            $capital_status,
            $amount,
            $remarks,
        );

        return is_null($result) ? response()->error():response()->success();
    }

    public function update($id, CapitalRequest $capitalRequest)
    {
        $request = $capitalRequest->validated();

        $company_id = Hashids::decode($request['company_id'])[0];
        $investor_id = Hashids::decode($request['investor_id'])[0];
        $group_id = Hashids::decode($request['group_id'])[0];
        $cash_id = Hashids::decode($request['cash_id'])[0];
        $date = $request['date'];
        $capital_status = $request['capital_status'];
        $amount = $request['amount'];
        $remarks = $request['remarks'];

        $capital = $this->capitalService->update(
            $id,
            $company_id,
            $investor_id, 
            $group_id,
            $cash_id,
            $date,
            $capital_status,
            $amount,
            $remarks,
        );

        return is_null($capital) ? response()->error() : response()->success();
    }

    public function delete($id)
    {
        $result = $this->capitalService->delete($id);

        return $result ? response()->error():response()->success();
    }
}
