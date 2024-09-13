<?php

namespace App\Models;

use App\Traits\SoftDeletesWithUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory, SoftDeletesWithUser;

    public function subcategories(): HasMany
    {
        return $this->hasMany(SubCategory::class);
    }


    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

}
