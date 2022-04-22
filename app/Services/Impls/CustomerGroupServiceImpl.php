<?php

namespace App\Services\Impls;

use App\Services\CustomerGroupService;
use App\Models\CustomerGroup;

use Exception;
use App\Actions\RandomGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class CustomerGroupServiceImpl implements CustomerGroupService
{
    public function __construct()
    {
        
    }
    
    public function create(
        int $company_id,
        int $cash_id,
        string $code,
        string $name,
        int $max_open_invoice = null,
        int $max_outstanding_invoice = null,
        int $max_invoice_age = null,
        int $payment_term = null,
        int $selling_point,
        string $selling_point_multiple = null,
        ?int $sell_at_cost = null,
        int $price_markup_percent = null,
        int $price_markup_nominal = null,
        int $price_markdown_nominal,
        int $round_on = null,
        ?int $round_digit = null,
        ?int $remarks = null,
    ): ?CustomerGroup
    {
        DB::beginTransaction();

        try {
            if ($code == Config::get('const.DEFAULT.KEYWORDS.AUTO')) {
                $code = $this->generateUniqueCode($company_id);
            }

            $customerGroup = new CustomerGroup();
            $customerGroup->company_id = $company_id;
            $customerGroup->cash_id = $cash_id;
            $customerGroup->code = $code;
            $customerGroup->name = $name;
            $customerGroup->max_open_invoice = $max_open_invoice;
            $customerGroup->max_outstanding_invoice = $max_outstanding_invoice;
            $customerGroup->max_invoice_age = $max_invoice_age;
            $customerGroup->selling_point = $selling_point;
            $customerGroup->selling_point_multiple = $selling_point_multiple;
            $customerGroup->sell_at_cost = $sell_at_cost;
            $customerGroup->price_markup_percent = $price_markup_percent;
            $customerGroup->price_markup_nominal = $price_markup_nominal;
            $customerGroup->price_markdown_nominal = $price_markdown_nominal;
            $customerGroup->round_on = $round_on;
            $customerGroup->round_digit = $round_digit;
            $customerGroup->remarks = $remarks;

            $customerGroup->save();

            DB::commit();

            return $customerGroup;
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

        $customerGroup = CustomerGroup::with('company')
                    ->whereCompanyId($companyId);

        if (empty($search)) {
            $customerGroup = $customerGroup->latest();
        } else {
            $customerGroup = $customerGroup->where('name', 'like', '%'.$search.'%')->latest();
        }

        if ($paginate) {
            $perPage = is_numeric($perPage) ? $perPage : Config::get('const.DEFAULT.PAGINATION_LIMIT');
            return $customerGroup->paginate($perPage);
        } else {
            return $customerGroup->get();
        }
    }

    public function update(
        int $id,
        int $company_id,
        int $cash_id,
        string $code,
        string $name,
        int $max_open_invoice = null,
        int $max_outstanding_invoice = null,
        int $max_invoice_age = null,
        int $payment_term = null,
        int $selling_point,
        string $selling_point_multiple = null,
        ?int $sell_at_cost = null,
        int $price_markup_percent = null,
        int $price_markup_nominal = null,
        int $price_markdown_nominal,
        int $round_on = null,
        ?int $round_digit = null,
        ?int $remarks = null,
    ): ?CustomerGroup
    {
        DB::beginTransaction();

        try {
            $customerGroup = CustomerGroup::find($id);

            if ($code == Config::get('const.DEFAULT.KEYWORDS.AUTO')) {
                $code = $this->generateUniqueCode($company_id);
            }
    
            $customerGroup->update([
                'company_id' => $company_id,
                'cash_id' => $cash_id,
                'code' => $code,
                'name' => $name,
                'max_open_invoice' => $max_open_invoice,
                'max_outstanding_invoice' => $max_outstanding_invoice,
                'max_invoice_age' => $max_invoice_age,
                'payment_term' => $payment_term,
                'selling_point' => $selling_point,
                'selling_point_multiple' => $selling_point_multiple,
                'sell_at_cost' => $sell_at_cost,
                'price_markup_percent' => $price_markup_percent,
                'price_markup_nominal' => $price_markup_nominal,
                'price_markdown_nominal' => $price_markdown_nominal,
                'round_on' => $round_on,
                'round_digit' => $round_digit,
                'remarks' => $remarks,
            ]);

            DB::commit();

            return $customerGroup->refresh();
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
            $customerGroup = CustomerGroup::find($id);

            if ($customerGroup) {
                $retval = $customerGroup->delete();
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
        $result = CustomerGroup::whereCompanyId($companyId)->where('code', '=' , $code);

        if($exceptId)
            $result = $result->where('id', '<>', $exceptId);

        return $result->count() == 0 ? true:false;
    }
}