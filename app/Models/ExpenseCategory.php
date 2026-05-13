<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
    ];

    // Sub-categories belonging to this category
    public function subCategories(): HasMany
    {
        return $this->hasMany(ExpenseSubCategory::class);
    }

    // Expenses filed under this category
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
