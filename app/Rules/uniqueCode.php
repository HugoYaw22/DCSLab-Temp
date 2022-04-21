<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\Config;

use App\Services\BranchService;
use App\Services\BrandService;
use App\Services\CapitalGroupService;
use App\Services\CapitalService;
use App\Services\CashService;
use App\Services\CustomerGroupService;
use App\Services\CustomerService;
use App\Services\ExpenseGroupService;
use App\Services\ExpenseService;
use App\Services\IncomeGroupService;
use App\Services\IncomeService;
use App\Services\InvestorService;
use App\Services\ProductGroupService;
use App\Services\ProductService;
use App\Services\SupplierService;
use App\Services\UnitService;
use App\Services\WarehouseService;

class uniqueCode implements Rule
{
    private int $companyId;
    private ?int $exceptId;
    private string $table;

    private BranchService $branchService;
    private WarehouseService $warehouseService;
    private SupplierService $supplierService;
    private ProductService $productService;
    private CashService $cashService;
    private InvestorService $investorService;
    private CapitalService $capitalService;
    private CapitalGroupService $capitalGroupService;
    private IncomeGroupService $incomeGroupService;
    private IncomeService $incomeService;
    private ExpenseService $expenseService;
    private ExpenseGroupService $expenseGroupService;
    private ProductGroupService $productGroupService;
    private BrandService $brandService;
    private UnitService $unitService;
    private CustomerGroupService $customerGroupService;
    private CustomerService $customerService;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(string $table, int $companyId, ?int $exceptId = null)
    {
        $this->table = $table;
        $this->companyId = $companyId;
        $this->exceptId = $exceptId ? $exceptId : null;

        switch($this->table) {
            case 'branches':
                $this->branchService = Container::getInstance()->make(BranchService::class);
                break;
            case 'warehouses':
                $this->warehouseService = Container::getInstance()->make(WarehouseService::class);
                break;
            case 'suppliers':
                $this->supplierService = Container::getInstance()->make(SupplierService::class);
                break;
            case 'products':
                $this->productService = Container::getInstance()->make(ProductService::class);
                break;
            case 'cashes':
                $this->cashService = Container::getInstance()->make(CashService::class);
                break;
            case 'investors':
                $this->investorService = Container::getInstance()->make(InvestorService::class);
                break;
            case 'capitals':
                $this->capitalService = Container::getInstance()->make(CapitalService::class);
                break;
            case 'capitalgroups':
                $this->capitalGroupService = Container::getInstance()->make(CapitalGroupService::class);
                break;
            case 'incomegroups':
                $this->incomeGroupService = Container::getInstance()->make(IncomeGroupService::class);
                break;
            case 'incomes':
                $this->incomeService = Container::getInstance()->make(IncomeService::class);
                break;
            case 'expensegroups':
                $this->expenseGroupService = Container::getInstance()->make(ExpenseGroupService::class);
                break;
            case 'expenses':
                $this->expenseService = Container::getInstance()->make(ExpenseService::class);
                break;
            case 'productgroups':
                $this->productGroupService = Container::getInstance()->make(ProductGroupService::class);
                break;
            case 'brands':
                $this->brandService = Container::getInstance()->make(BrandService::class);
                break;
            case 'units':
                $this->unitService = Container::getInstance()->make(UnitService::class);
                break;
            case 'customergroups':
                $this->customerGroupService = Container::getInstance()->make(CustomerGroupService::class);
                break;
            case 'customers':
                $this->customerService = Container::getInstance()->make(CustomerService::class);
                break;
            default:
                break;
        }
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($value == Config::get('const.DEFAULT.KEYWORDS.AUTO')) return true;

        $is_duplicate = false;

        switch($this->table) {
            case 'branches':
                $is_duplicate = $this->branchService->isUniqueCode($value, $this->companyId, $this->exceptId);
                break;
            case 'warehouses':
                $is_duplicate = $this->warehouseService->isUniqueCode($value, $this->companyId, $this->exceptId);
                break;
            case 'suppliers':
                $is_duplicate = $this->supplierService->isUniqueCode($value, $this->companyId, $this->exceptId);
                break;
            case 'products':
                $is_duplicate = $this->productService->isUniqueCodeForProduct($value, $this->companyId, $this->exceptId);
                break;
            case 'cashes':
                $is_duplicate = $this->cashService->isUniqueCode($value, $this->companyId, $this->exceptId);
                break;
            case 'investors':
                $is_duplicate = $this->investorService->isUniqueCode($value, $this->companyId, $this->exceptId);
                break;
            case 'capitals':
                $is_duplicate = $this->capitalService->isUniqueCode($value, $this->companyId, $this->exceptId);
                break;
            case 'capitalgroups':
                $is_duplicate = $this->capitalGroupService->isUniqueCode($value, $this->companyId, $this->exceptId);
                break;
            case 'incomegroups':
                $is_duplicate = $this->incomeGroupService->isUniqueCode($value, $this->companyId, $this->exceptId);
                break;
            case 'incomes':
                $is_duplicate = $this->incomeService->isUniqueCode($value, $this->companyId, $this->exceptId);
                break;
            case 'expensegroups':
                $is_duplicate = $this->expenseGroupService->isUniqueCode($value, $this->companyId, $this->exceptId);
                break;
            case 'expenses':
                $is_duplicate = $this->expenseService->isUniqueCode($value, $this->companyId, $this->exceptId);
                break;
            case 'productgroups':
                $is_duplicate = $this->productGroupService->isUniqueCode($value, $this->companyId, $this->exceptId);
                break;
            case 'brands':
                $is_duplicate = $this->brandService->isUniqueCode($value, $this->companyId, $this->exceptId);
                break;
            case 'units':
                $is_duplicate = $this->unitService->isUniqueCode($value, $this->companyId, $this->exceptId);
                break;
            case 'customergroups':
                $is_duplicate = $this->customerGroupService->isUniqueCode($value, $this->companyId, $this->exceptId);
                break;
            case 'customers':
                $is_duplicate = $this->customerService->isUniqueCode($value, $this->companyId, $this->exceptId);
            default:
                break;
        }
        return $is_duplicate;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('rules.unique_code');
    }
}
