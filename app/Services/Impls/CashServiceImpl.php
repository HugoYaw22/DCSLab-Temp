<?php

namespace App\Services\Impls;

use App\Services\CashService;
use App\Models\Cash;

use Exception;
use App\Actions\RandomGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class CashServiceImpl implements CashService
{
    public function __construct()
    {
        
    }
    
    public function create(
        int $company_id,
        string $code,
        string $name,
        ?int $is_bank = null,
        int $status,
    ): ?Cash
    {
        DB::beginTransaction();

        try {
            if ($code == Config::get('const.DEFAULT.KEYWORDS.AUTO')) {
                $code = $this->generateUniqueCode($company_id);
            }

            $cash = new Cash();
            $cash->company_id = $company_id;
            $cash->code = $code;
            $cash->name = $name;
            $cash->is_bank = $is_bank;
            $cash->status = $status;

            $cash->save();

            DB::commit();

            return $cash;
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

        $cash = Cash::with('company')
                    ->whereCompanyId($companyId);

        if (empty($search)) {
            $cash = $cash->latest();
        } else {
            $cash = $cash->where('name', 'like', '%'.$search.'%')->latest();
        }

        if ($paginate) {
            $perPage = is_numeric($perPage) ? $perPage : Config::get('const.DEFAULT.PAGINATION_LIMIT');
            return $cash->paginate($perPage);
        } else {
            return $cash->get();
        }
    }

    public function update(
        int $id,
        int $company_id,
        string $code,
        string $name,
        ?int $is_bank = null,
        int $status,
    ): ?Cash
    {
        DB::beginTransaction();

        try {
            $cash = Cash::find($id);

            if ($code == Config::get('const.DEFAULT.KEYWORDS.AUTO')) {
                $code = $this->generateUniqueCode($company_id);
            }
    
            $cash->update([
                'company_id' => $company_id,
                'code' => $code,
                'name' => $name,
                'is_bank' => $is_bank,
                'status' => $status,
            ]);

            DB::commit();

            return $cash->refresh();
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
            $cash = Cash::find($id);

            if ($cash) {
                $retval = $cash->delete();
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
        $result = Cash::whereCompanyId($companyId)->where('code', '=' , $code);

        if($exceptId)
            $result = $result->where('id', '<>', $exceptId);

        return $result->count() == 0 ? true:false;
    }
}