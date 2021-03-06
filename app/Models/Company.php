<?php

namespace App\Models;

use App\Models\User;
use App\Models\Branch;
use App\Models\Warehouse;
use App\Models\Employee;

use App\Models\Cash;
use App\Models\Capital;
use App\Models\CapitalGroup;
use App\Models\Income;
use App\Models\IncomeGroup;
use App\Models\Expense;
use App\Models\ExpenseGroup;
use App\Models\Investor;

use App\Models\Brand;
use App\Models\Unit;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\ProductGroup;

use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\CustomerAddress;

use App\Models\Supplier;
use App\Models\SupplierProduct;

use Spatie\Activitylog\LogOptions;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory, LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'address',
        'default',
        'status'
    ];

    protected static $logAttributes = ['code', 'name', 'address', 'default', 'status'];

    protected static $logOnlyDirty = true;

    protected $hidden = [
        'id',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at',
        'pivot',
    ];

    public function hId() : Attribute
    {
        return Attribute::make(
            get: fn () => HashIds::encode($this->attributes['id'])
        );
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }
    
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function cashes()
    {
        return $this->hasMany(Cash::class);
    }
    
    public function capitals()
    {
        return $this->hasMany(Capital::class);
    }

    public function capital_groups()
    {
        return $this->hasMany(CapitalGroup::class);
    }

    public function expense()
    {
        return $this->hasMany(Expense::class);
    }

    public function expensegroups()
    {
        return $this->hasMany(ExpenseGroup::class);
    }

    public function investors()
    {
        return $this->hasMany(Investor::class);
    }

    public function income()
    {
        return $this->hasMany(Income::class);
    }
    
    public function incomegroups()
    {
        return $this->hasMany(IncomeGroup::class);
    }

    public function productgroups()
    {
        return $this->hasMany(ProductGroup::class);
    }

    public function brands()
    {
        return $this->hasMany(Brand::class);
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function productUnits()
    {
        return $this->hasMany(ProductUnit::class);
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }

    public function suppliers_product()
    {
        return $this->hasMany(SupplierProduct::class);
    }

    public function customergroups()
    {
        return $this->hasMany(CustomerGroup::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function customerAddresses()
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $user = Auth::check();
            if ($user) {
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            $user = Auth::check();
            if ($user) {
                $model->updated_by = Auth::id();
            }
        });

        static::deleting(function ($model) {
            $user = Auth::check();
            if ($user) {
                $model->deleted_by = Auth::id();
                $model->save();
            }
        });
    }
}
