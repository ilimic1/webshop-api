<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class Category extends Model
{
    use HasFactory;
    use HasRecursiveRelationships;

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, null, null, 'sku');
    }

    public function recursiveProducts()
    {
        return $this->belongsToManyOfDescendantsAndSelf(Product::class, null, null, 'sku');
    }
}
