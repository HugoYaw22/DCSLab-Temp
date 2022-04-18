<?php

namespace App\Http\Controllers;

use App\Services\IncomeService;

use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use App\Http\Requests\IncomeRequest;
use App\Http\Resources\IncomeResource;

class IncomeController extends BaseController
{
    private $incomeService;
    
    public function __construct(IncomeService $incomeService)
    {
        parent::__construct();

        $this->middleware('auth');
        $this->incomeService = $incomeService;
    }

    public function read(Request $request)
    {
        $search = $request->has('search') && !is_null($request['search']) ? $request['search']:'';
        $paginate = $request->has('paginate') ? $request['paginate']:true;
        $perPage = $request->has('perPage') ? $request['perPage']:10;

        $companyId = Hashids::decode($request['companyId'])[0];

        $result = $this->incomeService->read(
            companyId: $companyId,
            search: $search,
            paginate: $paginate,
            perPage: $perPage
        );

        if (is_null($result)) {
            return response()->error();
        } else {
            $response = IncomeResource::collection($result);

            return $response;
        }
    }

    public function store(IncomeRequest $incomeRequest)
    {   
        $request = $incomeRequest->validated();
        
        $company_id = Hashids::decode($request['company_id'])[0];
        $company_id = Hashids::decode($request['company_id'])[0];
        $code = $request['code'];
        $date = $request['date'];
        $payment_term_type = $request['payment_term_type'];
        $income_group_id = Hashids::decode($request['income_group_id'])[0];
        $cash_id = Hashids::decode($request['cash_id'])[0];
        $amount = $request['amount'];
        $amount_owed = $request['amount_owed'];
        $remarks = $request['remarks'];
        $posted = $request['posted'];

        $result = $this->incomeService->create(
            $company_id,
            $code,
            $date,
            $payment_term_type,
            $income_group_id,
            $cash_id,
            $amount,
            $amount_owed,
            $remarks,
            $posted,
        );

        return is_null($result) ? response()->error():response()->success();
    }

    public function update($id, IncomeRequest $incomeRequest)
    {
        $request = $incomeRequest->validated();

        $company_id = Hashids::decode($request['company_id'])[0];
        $company_id = Hashids::decode($request['company_id'])[0];
        $code = $request['code'];
        $date = $request['date'];
        $payment_term_type = $request['payment_term_type'];
        $income_group_id = Hashids::decode($request['income_group_id'])[0];
        $cash_id = Hashids::decode($request['cash_id'])[0];
        $amount = $request['amount'];
        $amount_owed = $request['amount_owed'];
        $remarks = $request['remarks'];
        $posted = $request['posted'];

        $income = $this->incomeService->update(
            $id,
            $company_id,
            $code,
            $date,
            $payment_term_type,
            $income_group_id,
            $cash_id,
            $amount,
            $amount_owed,
            $remarks,
            $posted,
        );

        return is_null($income) ? response()->error() : response()->success();
    }

    public function delete($id)
    {
        $result = $this->incomeService->delete($id);

        return $result ? response()->error():response()->success();
    }
}
