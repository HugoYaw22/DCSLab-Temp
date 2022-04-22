<?php

namespace App\Services\Impls;

use App\Services\InvestorService;
use App\Models\Investor;

use Exception;
use App\Actions\RandomGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class InvestorServiceImpl implements InvestorService
{
    public function __construct()
    {
        
    }
    
    public function create(
        int $company_id,
        string $code,
        string $name,
        ?string $address = null,
        ?string $city = null,
        ?string $contact = null,
        ?string $tax_number = null,
        ?string $remarks = null,
        int $status,
    ): ?Investor
    {
        DB::beginTransaction();

        try {
            if ($code == Config::get('const.DEFAULT.KEYWORDS.AUTO')) {
                $code = $this->generateUniqueCode($company_id);
            }

            $investor = new Investor();
            $investor->company_id = $company_id;
            $investor->code = $code;
            $investor->name = $name;
            $investor->address = $address;
            $investor->city = $city;
            $investor->contact = $contact;
            $investor->tax_number = $tax_number;
            $investor->remarks = $remarks;
            $investor->status = $status;

            $investor->save();

            DB::commit();

            return $investor;
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

        $investor = Investor::with('company')
                    ->whereCompanyId($companyId);

        if (empty($search)) {
            $investor = $investor->latest();
        } else {
            $investor = $investor->where('name', 'like', '%'.$search.'%')->latest();
        }

        if ($paginate) {
            $perPage = is_numeric($perPage) ? $perPage : Config::get('const.DEFAULT.PAGINATION_LIMIT');
            return $investor->paginate($perPage);
        } else {
            return $investor->get();
        }
    }

    public function update(
        int $id,
        int $company_id,
        string $code,
        string $name,
        ?string $address = null,
        ?string $city = null,
        ?string $contact = null,
        ?string $tax_number = null,
        ?string $remarks = null,
        int $status,
    ): ?Investor
    {
        DB::beginTransaction();

        try {
            $investor = Investor::find($id);

            if ($code == Config::get('const.DEFAULT.KEYWORDS.AUTO')) {
                $code = $this->generateUniqueCode($company_id);
            }
    
            $investor->update([
                'company_id' => $company_id,
                'code' => $code,
                'name' => $name,
                'address' => $address,
                'city' => $city,
                'contact' => $contact,
                'tax_number' => $tax_number,
                'remarks' => $remarks,
                'status' => $status,
            ]);

            DB::commit();

            return $investor->refresh();
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
            $investor = Investor::find($id);

            if ($investor) {
                $retval = $investor->delete();
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
        $result = Investor::whereCompanyId($companyId)->where('code', '=' , $code);

        if($exceptId)
            $result = $result->where('id', '<>', $exceptId);

        return $result->count() == 0 ? true:false;
    }
}