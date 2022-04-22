<?php

namespace App\Services\Impls;

use App\Services\IncomeGroupService;
use App\Models\IncomeGroup;

use Exception;
use App\Actions\RandomGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class IncomeGroupServiceImpl implements IncomeGroupService
{
    public function __construct()
    {
        
    }
    
    public function create(
        int $company_id,
        string $code,
        string $name,
        int $status,
    ): ?IncomeGroup
    {
        DB::beginTransaction();

        try {
            if ($code == Config::get('const.DEFAULT.KEYWORDS.AUTO')) {
                $code = $this->generateUniqueCode($company_id);
            }

            $incomeGroup = new IncomeGroup();
            $incomeGroup->company_id = $company_id;
            $incomeGroup->code = $code;
            $incomeGroup->name = $name;
            $incomeGroup->status = $status;

            $incomeGroup->save();

            DB::commit();

            return $incomeGroup;
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

        $incomeGroup = IncomeGroup::with('company')
                    ->whereCompanyId($companyId);

        if (empty($search)) {
            $incomeGroup = $incomeGroup->latest();
        } else {
            $incomeGroup = $incomeGroup->where('name', 'like', '%'.$search.'%')->latest();
        }

        if ($paginate) {
            $perPage = is_numeric($perPage) ? $perPage : Config::get('const.DEFAULT.PAGINATION_LIMIT');
            return $incomeGroup->paginate($perPage);
        } else {
            return $incomeGroup->get();
        }
    }

    public function update(
        int $id,
        int $company_id,
        string $code,
        string $name,
        int $status,
    ): ?IncomeGroup
    {
        DB::beginTransaction();

        try {
            $incomeGroup = IncomeGroup::find($id);

            if ($code == Config::get('const.DEFAULT.KEYWORDS.AUTO')) {
                $code = $this->generateUniqueCode($company_id);
            }
    
            $incomeGroup->update([
                'company_id' => $company_id,
                'code' => $code,
                'name' => $name,
                'status' => $status,
            ]);

            DB::commit();

            return $incomeGroup->refresh();
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
            $incomeGroup = IncomeGroup::find($id);

            if ($incomeGroup) {
                $retval = $incomeGroup->delete();
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
        $result = IncomeGroup::whereCompanyId($companyId)->where('code', '=' , $code);

        if($exceptId)
            $result = $result->where('id', '<>', $exceptId);

        return $result->count() == 0 ? true:false;
    }
}