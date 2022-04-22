<?php

namespace App\Services\Impls;

use App\Services\ExpenseService;
use App\Models\Expense;

use Exception;
use App\Actions\RandomGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class ExpenseServiceImpl implements ExpenseService
{
    public function __construct()
    {
        
    }
    
    public function create(
        int $company_id,
        int $branch_id,
        int $expense_group_id,
        ?int $cash_id = null,
        string $code,
        ?string $date = null,
        string $payment_term_type,
        string $amount,
        string $amount_owed,
        ?string $remarks = null,
        int $posted,
    ): ?Expense
    {
        DB::beginTransaction();

        try {
            if ($code == Config::get('const.DEFAULT.KEYWORDS.AUTO')) {
                $code = $this->generateUniqueCode($company_id);
            }

            $expense = new Expense();
            $expense->company_id = $company_id;
            $expense->branch_id = $branch_id;
            $expense->expense_group_id = $expense_group_id;
            $expense->cash_id = $cash_id;
            $expense->code = $code;
            $expense->date = $date;
            $expense->payment_term_type = $payment_term_type;
            $expense->amount = $amount;
            $expense->amount_owed = $amount_owed;
            $expense->remarks = $remarks;
            $expense->posted = $posted;

            $expense->save();

            DB::commit();

            return $expense;
        } catch (Exception $e) {
            DB::rollBack();
            Log::debug($e);
            return Config::get('const.ERROR_RETURN_VALUE');
        }
    }

    public function read(
        int $companyId,
        string $search = '',
        bool $paginate = true,
        int $perPage = 10
    )
    {
        if (!$companyId) return null;

        $expense = Expense::with('company')
                    ->whereCompanyId($companyId);

        if (empty($search)) {
            $expense = $expense->latest();
        } else {
            $expense = $expense->where('name', 'like', '%'.$search.'%')->latest();
        }

        if ($paginate) {
            $perPage = is_numeric($perPage) ? $perPage : Config::get('const.DEFAULT.PAGINATION_LIMIT');
            return $expense->paginate($perPage);
        } else {
            return $expense->get();
        }
    }

    public function update(
        int $id,
        int $company_id,
        int $branch_id,
        int $expense_group_id,
        ?int $cash_id = null,
        string $code,
        ?string $date = null,
        string $payment_term_type,
        string $amount,
        string $amount_owed,
        ?string $remarks = null,
        int $posted,
    ): ?Expense
    {
        DB::beginTransaction();

        try {
            $expense = Expense::find($id);

            if ($code == Config::get('const.DEFAULT.KEYWORDS.AUTO')) {
                $code = $this->generateUniqueCode($company_id);
            }
    
            $expense->update([
                'company_id' => $company_id,
                'branch_id' => $branch_id,
                'expense_group_id' => $expense_group_id,
                'cash_id' => $cash_id,
                'code' => $code,
                'date' => $date,
                'payment_term_type' => $payment_term_type,
                'amount' => $amount,
                'amount_owed' => $amount_owed,
                'remarks' => $remarks,
                'posted' => $posted,
            ]);

            DB::commit();

            return $expense->refresh();
        } catch (Exception $e) {
            DB::rollBack();
            Log::debug($e);
            return Config::get('const.ERROR_RETURN_VALUE');
        }
    }

    public function delete(int $id): bool
    {
        DB::beginTransaction();

        $retval = false;
        try {
            $expense = Expense::find($id);

            if ($expense) {
                $retval = $expense->delete();
            }

            DB::commit();

            return $retval;
        } catch (Exception $e) {
            DB::rollBack();
            Log::debug($e);
            return Config::get('const.ERROR_RETURN_VALUE');
        }
    }

    public function generateUniqueCode(int $companyId): string
    {
        $rand = new RandomGenerator();
        $code = '';
        
        do {
            $code = $rand->generateAlphaNumeric(3).$rand->generateFixedLengthNumber(3);
        } while (!$this->isUniqueCode($code, $companyId));

        return $code;
    }

    public function isUniqueCode(string $code, int $companyId, ?int $exceptId = null): bool
    {
        $result = Expense::whereCompanyId($companyId)->where('code', '=' , $code);

        if($exceptId)
            $result = $result->where('id', '<>', $exceptId);

        return $result->count() == 0 ? true:false;
    }
}