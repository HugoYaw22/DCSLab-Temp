<?php

namespace App\Http\Controllers;

use App\Services\ExpenseService;

use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use App\Http\Requests\ExpenseRequest;
use App\Http\Resources\ExpenseResource;

class ExpenseController extends BaseController
{
    private $expenseService;
    
    public function __construct(ExpenseService $expenseService)
    {
        parent::__construct();

        $this->middleware('auth');
        $this->expenseService = $expenseService;
    }

    public function read(Request $request)
    {
        $search = $request->has('search') && !is_null($request['search']) ? $request['search']:'';
        $paginate = $request->has('paginate') ? $request['paginate']:true;
        $perPage = $request->has('perPage') ? $request['perPage']:10;

        $companyId = Hashids::decode($request['companyId'])[0];

        $result = $this->expenseService->read(
            companyId: $companyId,
            search: $search,
            paginate: $paginate,
            perPage: $perPage
        );

        if (is_null($result)) {
            return response()->error();
        } else {
            $response = ExpenseResource::collection($result);

            return $response;
        }
    }

    public function store(ExpenseRequest $expenseRequest)
    {   
        $request = $expenseRequest->validated();
        
        $company_id = Hashids::decode($request['company_id'])[0];
        $company_id = Hashids::decode($request['company_id'])[0];
        $code = $request['code'];
        $date = $request['date'];
        $payment_term_type = $request['payment_term_type'];
        $expense_group_id = Hashids::decode($request['expense_group_id'])[0];
        $cash_id = Hashids::decode($request['cash_id'])[0];
        $amount = $request['amount'];
        $amount_owed = $request['amount_owed'];
        $remarks = $request['remarks'];
        $posted = $request['posted'];

        $result = $this->expenseService->create(
            $company_id,
            $code, 
            $date,
            $payment_term_type,
            $expense_group_id,
            $cash_id,
            $amount,
            $amount_owed,
            $remarks,
            $posted,
        );

        return is_null($result) ? response()->error():response()->success();
    }

    public function update($id, ExpenseRequest $expenseRequest)
    {
        $request = $expenseRequest->validated();

        $company_id = Hashids::decode($request['company_id'])[0];
        $company_id = Hashids::decode($request['company_id'])[0];
        $code = $request['code'];
        $date = $request['date'];
        $payment_term_type = $request['payment_term_type'];
        $expense_group_id = Hashids::decode($request['expense_group_id'])[0];
        $cash_id = Hashids::decode($request['cash_id'])[0];
        $amount = $request['amount'];
        $amount_owed = $request['amount_owed'];
        $remarks = $request['remarks'];
        $posted = $request['posted'];

        $expense = $this->expenseService->update(
            $id,
            $company_id,
            $code, 
            $date,
            $payment_term_type,
            $expense_group_id,
            $cash_id,
            $amount,
            $amount_owed,
            $remarks,
            $posted,
        );

        return is_null($expense) ? response()->error() : response()->success();
    }

    public function delete($id)
    {
        $result = $this->expenseService->delete($id);

        return $result ? response()->error():response()->success();
    }
}
