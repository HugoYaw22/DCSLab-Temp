<?php

namespace App\Services\Impls;

use App\Services\IncomeService;
use App\Models\Income;

use Exception;
use App\Actions\RandomGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class IncomeServiceImpl implements IncomeService
{
    public function __construct()
    {
        
    }
    
    public function create(
        int $company_id,
        int $branch_id,
        int $income_group_id,
        ?int $cash_id = null,
        string $code,
        ?string $date = null,
        string $payment_term_type,
        string $amount,
        string $amount_owed,
        ?string $remarks = null,
        int $posted,
    ): ?Income
    {
        DB::beginTransaction();

        try {
            if ($code == Config::get('const.DEFAULT.KEYWORDS.AUTO')) {
                $code = $this->generateUniqueCode($company_id);
            }

            $income = new Income();
            $income->company_id = $company_id;
            $income->branch_id = $branch_id;
            $income->income_group_id = $income_group_id;
            $income->cash_id = $cash_id;
            $income->code = $code;
            $income->date = $date;
            $income->payment_term_type = $payment_term_type;
            $income->amount = $amount;
            $income->amount_owed = $amount_owed;
            $income->remarks = $remarks;
            $income->posted = $posted;

            $income->save();

            DB::commit();

            return $income;
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

        $income = Income::with('company')
                    ->whereCompanyId($companyId);

        if (empty($search)) {
            $income = $income->latest();
        } else {
            $income = $income->where('name', 'like', '%'.$search.'%')->latest();
        }

        if ($paginate) {
            $perPage = is_numeric($perPage) ? $perPage : Config::get('const.DEFAULT.PAGINATION_LIMIT');
            return $income->paginate($perPage);
        } else {
            return $income->get();
        }
    }

    public function update(
        int $id,
        int $company_id,
        int $branch_id,
        int $income_group_id,
        ?int $cash_id = null,
        string $code,
        ?string $date = null,
        string $payment_term_type,
        string $amount,
        string $amount_owed,
        ?string $remarks = null,
        int $posted,
    ): ?Income
    {
        DB::beginTransaction();

        try {
            $income = Income::find($id);

            if ($code == Config::get('const.DEFAULT.KEYWORDS.AUTO')) {
                $code = $this->generateUniqueCode($company_id);
            }
    
            $income->update([
                'company_id' => $company_id,
                'branch_id' => $branch_id,
                'income_group_id' => $income_group_id,
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

            return $income->refresh();
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
            $income = Income::find($id);

            if ($income) {
                $retval = $income->delete();
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
        $result = Income::whereCompanyId($companyId)->where('code', '=' , $code);

        if($exceptId)
            $result = $result->where('id', '<>', $exceptId);

        return $result->count() == 0 ? true:false;
    }
}