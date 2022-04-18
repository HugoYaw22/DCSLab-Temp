<?php

namespace App\Http\Controllers;

use App\Services\InvestorService;

use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use App\Http\Requests\InvestorRequest;
use App\Http\Resources\InvestorResource;

class InvestorController extends BaseController
{
    private $investorService;
    
    public function __construct(InvestorService $investorService)
    {
        parent::__construct();

        $this->middleware('auth');
        $this->investorService = $investorService;
    }

    public function read(Request $request)
    {
        $search = $request->has('search') && !is_null($request['search']) ? $request['search']:'';
        $paginate = $request->has('paginate') ? $request['paginate']:true;
        $perPage = $request->has('perPage') ? $request['perPage']:10;

        $companyId = Hashids::decode($request['companyId'])[0];

        $result = $this->investorService->read(
            companyId: $companyId,
            search: $search,
            paginate: $paginate,
            perPage: $perPage
        );

        if (is_null($result)) {
            return response()->error();
        } else {
            $response = InvestorResource::collection($result);

            return $response;
        }
    }

    public function store(InvestorRequest $investorRequest)
    {   
        $request = $investorRequest->validated();
        
        $company_id = Hashids::decode($request['company_id'])[0];
        $code = $request['code'];
        $name = $request['name'];

        $result = $this->investorService->create(
            $company_id,
            $code,
            $name,
        );

        return is_null($result) ? response()->error():response()->success();
    }

    public function update($id, InvestorRequest $investorRequest)
    {
        $request = $investorRequest->validated();

        $company_id = Hashids::decode($request['company_id'])[0];
        $code = $request['code'];
        $name = $request['name'];

        $investor = $this->investorService->update(
            $id,
            $company_id,
            $code,
            $name,
        );

        return is_null($investor) ? response()->error() : response()->success();
    }

    public function delete($id)
    {
        $result = $this->investorService->delete($id);

        return $result ? response()->error():response()->success();
    }
}
