<?php

namespace App\Http\Controllers;

use App\Services\CapitalGroupService;

use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use App\Http\Requests\CapitalGroupRequest;
use App\Http\Resources\CapitalGroupResource;

class CapitalGroupController extends BaseController
{
    private $capitalGroupService;
    
    public function __construct(CapitalGroupService $capitalGroupService)
    {
        parent::__construct();

        $this->middleware('auth');
        $this->capitalGroupService = $capitalGroupService;
    }

    public function read(Request $request)
    {
        $search = $request->has('search') && !is_null($request['search']) ? $request['search']:'';
        $paginate = $request->has('paginate') ? $request['paginate']:true;
        $perPage = $request->has('perPage') ? $request['perPage']:10;

        $companyId = Hashids::decode($request['companyId'])[0];

        $result = $this->capitalGroupService->read(
            companyId: $companyId,
            search: $search,
            paginate: $paginate,
            perPage: $perPage
        );

        if (is_null($result)) {
            return response()->error();
        } else {
            $response = CapitalGroupResource::collection($result);

            return $response;
        }
    }

    public function store(CapitalGroupRequest $capitalgroupRequest)
    {   
        $request = $capitalgroupRequest->validated();
        
        $company_id = Hashids::decode($request['company_id'])[0];
        $code = $request['code'];
        $name = $request['name'];

        $result = $this->capitalGroupService->create(
            $company_id,
            $code,
            $name,
        );

        return is_null($result) ? response()->error():response()->success();
    }

    public function update($id, CapitalGroupRequest $capitalgroupRequest)
    {
        $request = $capitalgroupRequest->validated();

        $company_id = Hashids::decode($request['company_id'])[0];
        $code = $request['code'];
        $name = $request['name'];

        $capitalgroup = $this->capitalGroupService->update(
            $id,
            $company_id,
            $code,
            $name,
        );

        return is_null($capitalgroup) ? response()->error() : response()->success();
    }

    public function delete($id)
    {
        $result = $this->capitalGroupService->delete($id);

        return $result ? response()->error():response()->success();
    }
}
