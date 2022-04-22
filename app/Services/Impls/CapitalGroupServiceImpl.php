<?php

namespace App\Services\Impls;

use App\Services\CapitalGroupService;
use App\Models\CapitalGroup;

use Exception;
use App\Actions\RandomGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class CapitalGroupServiceImpl implements CapitalGroupService
{
    public function __construct()
    {
        
    }
    
    public function create(
        int $company_id,
        string $code,
        string $name,
    ): ?CapitalGroup
    {
        DB::beginTransaction();

        try {
            if ($code == Config::get('const.DEFAULT.KEYWORDS.AUTO')) {
                $code = $this->generateUniqueCode($company_id);
            }

            $capitalGroup = new CapitalGroup();
            $capitalGroup->company_id = $company_id;
            $capitalGroup->code = $code;
            $capitalGroup->name = $name;

            $capitalGroup->save();

            DB::commit();

            return $capitalGroup;
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

        $capitalGroup = CapitalGroup::with('company')
                    ->whereCompanyId($companyId);

        if (empty($search)) {
            $capitalGroup = $capitalGroup->latest();
        } else {
            $capitalGroup = $capitalGroup->where('name', 'like', '%'.$search.'%')->latest();
        }

        if ($paginate) {
            $perPage = is_numeric($perPage) ? $perPage : Config::get('const.DEFAULT.PAGINATION_LIMIT');
            return $capitalGroup->paginate($perPage);
        } else {
            return $capitalGroup->get();
        }
    }

    public function update(
        int $id,
        int $company_id,
        string $code,
        string $name,
    ): ?CapitalGroup
    {
        DB::beginTransaction();

        try {
            $capitalGroup = CapitalGroup::find($id);

            if ($code == Config::get('const.DEFAULT.KEYWORDS.AUTO')) {
                $code = $this->generateUniqueCode($company_id);
            }
    
            $capitalGroup->update([
                'company_id' => $company_id,
                'code' => $code,
                'name' => $name,
            ]);

            DB::commit();

            return $capitalGroup->refresh();
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
            $capitalGroup = CapitalGroup::find($id);

            if ($capitalGroup) {
                $retval = $capitalGroup->delete();
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
        $result = CapitalGroup::whereCompanyId($companyId)->where('code', '=' , $code);

        if($exceptId)
            $result = $result->where('id', '<>', $exceptId);

        return $result->count() == 0 ? true:false;
    }
}