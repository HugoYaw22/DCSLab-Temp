<?php

namespace App\Http\Controllers;

use App\Services\ExpenseGroupService;

use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use App\Http\Requests\ExpenseGroupRequest;
use App\Http\Resources\ExpenseGroupResource;

class ExpenseGroupController extends BaseController
{
    private $expenseGroupService;
    
    public function __construct(ExpenseGroupService $expenseGroupService)
    {
        parent::__construct();

        $this->middleware('auth');
        $this->expenseGroupService = $expenseGroupService;
    }

    public function read(Request $request)
    {
        $search = $request->has('search') && !is_null($request['search']) ? $request['search']:'';
        $paginate = $request->has('paginate') ? $request['paginate']:true;
        $perPage = $request->has('perPage') ? $request['perPage']:10;

        $companyId = Hashids::decode($request['companyId'])[0];

        $result = $this->expenseGroupService->read(
            companyId: $companyId,
            search: $search,
            paginate: $paginate,
            perPage: $perPage
        );

        if (is_null($result)) {
            return response()->error();
        } else {
            $response = ExpenseGroupResource::collection($result);

            return $response;
        }
    }

    public function store(ExpenseGroupRequest $expensegroupRequest)
    {   
        $request = $expensegroupRequest->validated();
        
        $company_id = Hashids::decode($request['company_id'])[0];
        $code = $request['code'];
        $name = $request['name'];

        $result = $this->expenseGroupService->create(
            $company_id,
            $code, 
            $name,
        );

        return is_null($result) ? response()->error():response()->success();
    }

    public function update($id, ExpenseGroupRequest $expensegroupRequest)
    {
        $request = $expensegroupRequest->validated();

        $company_id = Hashids::decode($request['company_id'])[0];
        $code = $request['code'];
        $name = $request['name'];

        $expensegroup = $this->expenseGroupService->update(
            $id,
            $company_id,
            $code, 
            $name,
        );

        return is_null($expensegroup) ? response()->error() : response()->success();
    }

    public function delete($id)
    {
        $result = $this->expenseGroupService->delete($id);

        return $result ? response()->error():response()->success();
    }
}
