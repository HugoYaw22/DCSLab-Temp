<?php

namespace App\Services\Impls;

use App\Services\ExpenseGroupService;
use App\Models\ExpenseGroup;

use Exception;
use App\Actions\RandomGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class ExpenseGroupServiceImpl implements ExpenseGroupService
{
    public function __construct()
    {
        
    }
    
    public function create(
        int $company_id,
        string $code,
        string $name,
        int $status,
    ): ?ExpenseGroup
    {
        DB::beginTransaction();

        try {
            if ($code == Config::get('const.DEFAULT.KEYWORDS.AUTO')) {
                $code = $this->generateUniqueCode($company_id);
            }

            $expenseGroup = new ExpenseGroup();
            $expenseGroup->company_id = $company_id;
            $expenseGroup->code = $code;
            $expenseGroup->name = $name;
            $expenseGroup->status = $status;

            $expenseGroup->save();

            DB::commit();

            return $expenseGroup;
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

        $expenseGroup = ExpenseGroup::with('company')
                    ->whereCompanyId($companyId);

        if (empty($search)) {
            $expenseGroup = $expenseGroup->latest();
        } else {
            $expenseGroup = $expenseGroup->where('name', 'like', '%'.$search.'%')->latest();
        }

        if ($paginate) {
            $perPage = is_numeric($perPage) ? $perPage : Config::get('const.DEFAULT.PAGINATION_LIMIT');
            return $expenseGroup->paginate($perPage);
        } else {
            return $expenseGroup->get();
        }
    }

    public function update(
        int $id,
        int $company_id,
        string $code,
        string $name,
        int $status,
    ): ?ExpenseGroup
    {
        DB::beginTransaction();

        try {
            $expenseGroup = ExpenseGroup::find($id);

            if ($code == Config::get('const.DEFAULT.KEYWORDS.AUTO')) {
                $code = $this->generateUniqueCode($company_id);
            }
    
            $expenseGroup->update([
                'company_id' => $company_id,
                'code' => $code,
                'name' => $name,
                'status' => $status,
            ]);

            DB::commit();

            return $expenseGroup->refresh();
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
            $expenseGroup = ExpenseGroup::find($id);

            if ($expenseGroup) {
                $retval = $expenseGroup->delete();
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
        $result = ExpenseGroup::whereCompanyId($companyId)->where('code', '=' , $code);

        if($exceptId)
            $result = $result->where('id', '<>', $exceptId);

        return $result->count() == 0 ? true:false;
    }
}