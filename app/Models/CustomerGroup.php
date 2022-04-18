<?php

namespace App\Models;

use App\Models\Cash;
use App\Models\Company;
use App\Models\Customer;
use Spatie\Activitylog\LogOptions;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerGroup extends Model
{
    use HasFactory, LogsActivity;
    use SoftDeletes;
    
    protected $fillable = [
        'code',
        'name',
        'max_open_invoice',
        'max_outstanding_invoice',
        'max_invoice_age',
        'payment_term',
        'selling_point',
        'selling_point_multiple',
        'sell_at_cost',
        'price_markup_percent',
        'price_markup_nominal',
        'price_markdown_percent',
        'price_markdown_nominal',
        'round_on',
        'round_digit',
        'remarks',
        'cash_id'
    ];

    protected static $logAttributes = [
        'code',
        'name',
        'max_open_invoice',
        'max_outstanding_invoice',
        'max_invoice_age',
        'payment_term',
        'selling_point',
        'selling_point_multiple',
        'sell_at_cost',
        'price_markup_percent',
        'price_markup_nominal',
        'price_markdown_percent',
        'price_markdown_nominal',
        'round_on',
        'round_digit',
        'remarks',
        'cash_id'
    ];

    protected static $logOnlyDirty = true;

    protected $hidden = [
        'id',
        'cash_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function hId() : Attribute
    {
        return Attribute::make(
            get: fn () => HashIds::encode($this->attributes['id'])
        );
    }

    public function cash()
    {
        return $this->belongsTo(Cash::class);
    }

    public function customerGroup()
    {
        return $this->hasMany(Customer::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
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