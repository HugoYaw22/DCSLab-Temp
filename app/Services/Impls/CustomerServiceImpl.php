<?php

namespace App\Services\Impls;

use App\Services\CustomerService;
use App\Models\Customer;

use Exception;
use App\Actions\RandomGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class CustomerServiceImpl implements CustomerService
{
    public function __construct()
    {
        
    }
    
    public function create(
        int $company_id,
        int $customer_group_id,
        string $code,
        int $is_member,
        string $name,
        ?string $zone = null,
        int $max_open_invoice,
        int $max_outstanding_invoice,
        int $max_invoice_age,
        int $payment_term,
        int $tax_id,
        ?string $remarks = null,
        int $status,
    ): ?Customer
    {
        DB::beginTransaction();

        try {
            if ($code == Config::get('const.DEFAULT.KEYWORDS.AUTO')) {
                $code = $this->generateUniqueCode($company_id);
            }

            $customer = new Customer();
            $customer->company_id = $company_id;
            $customer->customer_group_id = $customer_group_id;
            $customer->code = $code;
            $customer->is_member = $is_member;
            $customer->name = $name;
            $customer->zone = $zone;
            $customer->max_open_invoice = $max_open_invoice;
            $customer->max_outstanding_invoice = $max_outstanding_invoice;
            $customer->max_invoice_age = $max_invoice_age;
            $customer->payment_term = $payment_term;
            $customer->tax_id = $tax_id;
            $customer->remarks = $remarks;
            $customer->status = $status;

            $customer->save();

            DB::commit();

            return $customer;
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

        $customer = Customer::with('company')
                    ->whereCompanyId($companyId);

        if (empty($search)) {
            $customer = $customer->latest();
        } else {
            $customer = $customer->where('name', 'like', '%'.$search.'%')->latest();
        }

        if ($paginate) {
            $perPage = is_numeric($perPage) ? $perPage : Config::get('const.DEFAULT.PAGINATION_LIMIT');
            return $customer->paginate($perPage);
        } else {
            return $customer->get();
        }
    }

    public function update(
        int $id,
        int $company_id,
        int $customer_group_id,
        string $code,
        int $is_member,
        string $name,
        ?string $zone = null,
        int $max_open_invoice,
        int $max_outstanding_invoice,
        int $max_invoice_age,
        int $payment_term,
        int $tax_id,
        ?string $remarks = null,
        int $status,
    ): ?Customer
    {
        DB::beginTransaction();

        try {
            $customer = Customer::find($id);

            if ($code == Config::get('const.DEFAULT.KEYWORDS.AUTO')) {
                $code = $this->generateUniqueCode($company_id);
            }
    
            $customer->update([
                'company_id' => $company_id,
                'customer_group_id' => $customer_group_id,
                'code' => $code,
                'is_member' => $is_member,
                'name' => $name,
                'zone' => $zone,
                'max_open_invoice' => $max_open_invoice,
                'max_outstanding_invoice' => $max_outstanding_invoice,
                'max_invoice_age' => $max_invoice_age,
                'payment_term' => $payment_term,
                'tax_id' => $tax_id,
                'remarks' => $remarks,
                'status' => $status,
            ]);

            DB::commit();

            return $customer->refresh();
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
            $customer = Customer::find($id);

            if ($customer) {
                $retval = $customer->delete();
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
        $result = Customer::whereCompanyId($companyId)->where('code', '=' , $code);

        if($exceptId)
            $result = $result->where('id', '<>', $exceptId);

        return $result->count() == 0 ? true:false;
    }
}