<?php

namespace App\Services\Impls;

use App\Services\CapitalService;
use App\Models\Capital;

use Exception;
use App\Actions\RandomGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class CapitalServiceImpl implements CapitalService
{
    public function __construct()
    {
        
    }
    
    public function create(
        int $company_id,
        int $investor_id,
        int $group_id,
        int $cash_id,
        ?int $ref_number = null,
        ?string $date = null,
        int $capial_status,
        int $amount,
        ?string $remarks = null,
    ): ?Capital
    {
        DB::beginTransaction();

        try {
            $capital = new Capital();
            $capital->company_id = $company_id;
            $capital->investor_id = $investor_id;
            $capital->group_id = $group_id;
            $capital->cash_id = $cash_id;
            $capital->ref_number = $ref_number;
            $capital->date = $date;
            $capital->capial_status = $capial_status;
            $capital->amount = $amount;
            $capital->remarks = $remarks;

            $capital->save();

            DB::commit();

            return $capital;
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

        $capital = Capital::with('company')
                    ->whereCompanyId($companyId);

        if (empty($search)) {
            $capital = $capital->latest();
        } else {
            $capital = $capital->where('name', 'like', '%'.$search.'%')->latest();
        }

        if ($paginate) {
            $perPage = is_numeric($perPage) ? $perPage : Config::get('const.DEFAULT.PAGINATION_LIMIT');
            return $capital->paginate($perPage);
        } else {
            return $capital->get();
        }
    }

    public function update(
        int $id,
        int $company_id,
        int $investor_id,
        int $group_id,
        int $cash_id,
        ?int $ref_number = null,
        ?string $date = null,
        int $capial_status,
        int $amount,
        ?string $remarks = null,
    ): ?Capital
    {
        DB::beginTransaction();

        try {
            $capital = Capital::find($id);
    
            $capital->update([
                'company_id' => $company_id,
                'investor_id' => $investor_id,
                'group_id' => $group_id,
                'cash_id' => $cash_id,
                'ref_number' => $ref_number,
                'date' => $date,
                'capial_status' => $capial_status,
                'amount' => $amount,
                'remarks' => $remarks,
            ]);

            DB::commit();

            return $capital->refresh();
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
            $capital = Capital::find($id);

            if ($capital) {
                $retval = $capital->delete();
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
        $result = Capital::whereCompanyId($companyId)->where('code', '=' , $code);

        if($exceptId)
            $result = $result->where('id', '<>', $exceptId);

        return $result->count() == 0 ? true:false;
    }
}