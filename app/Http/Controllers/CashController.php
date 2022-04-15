<?php

namespace App\Http\Controllers;

use App\Services\CashService;

use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use App\Http\Requests\CashRequest;
use App\Http\Resources\CashResource;

class CashController extends BaseController
{
    private $cashService;
    
    public function __construct(CashService $cashService)
    {
        parent::__construct();

        $this->middleware('auth');
        $this->cashService = $cashService;
    }

    public function read(Request $request)
    {
        $search = $request->has('search') && !is_null($request['search']) ? $request['search']:'';
        $paginate = $request->has('paginate') ? $request['paginate']:true;
        $perPage = $request->has('perPage') ? $request['perPage']:10;

        $companyId = Hashids::decode($request['companyId'])[0];

        $result = $this->cashService->read(
            companyId: $companyId,
            search: $search,
            paginate: $paginate,
            perPage: $perPage
        );

        if (is_null($result)) {
            return response()->error();
        } else {
            $response = CashResource::collection($result);

            return $response;
        }
    }

    public function store(CashRequest $cashRequest)
    {   
        $request = $cashRequest->validated();
        
        $company_id = Hashids::decode($request['company_id'])[0];
        $code = $request['code'];
        $name = $request['name'];
        $is_bank = $request['is_bank'];
        $status = $request['status'];

        $result = $this->cashService->create(
            $company_id,
            $code, 
            $name,
            $is_bank,
            $status,
        );

        return is_null($result) ? response()->error():response()->success();
    }

    public function update($id, CashRequest $cashRequest)
    {
        $request = $cashRequest->validated();

        $company_id = Hashids::decode($request['company_id'])[0];
        $code = $request['code'];
        $name = $request['name'];
        $is_bank = $request['is_bank'];
        $status = $request['status'];

        $cash = $this->cashService->update(
            $id,
            $company_id,
            $code, 
            $name,
            $is_bank,
            $status,
        );

        return is_null($cash) ? response()->error() : response()->success();
    }

    public function delete($id)
    {
        $result = $this->cashService->delete($id);

        return $result ? response()->error():response()->success();
    }
}
