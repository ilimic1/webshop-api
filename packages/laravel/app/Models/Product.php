<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'sku';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $appends = ['lowest_price'];

    public function scopePublished(Builder $query): void
    {
        $query->where('published', true);
    }

    public function scopeWithPriceList(Builder $query, int $price_list_id): void
    {
        $query->with(['priceLists' => function ($qb) use ($price_list_id) {
            $qb->where('price_lists.id', $price_list_id);
        }]);
    }

    public function scopeWithContract(Builder $query, int $user_id): void
    {
        $query->with(['users' => function ($qb) use ($user_id) {
            $qb->where('users.id', $user_id);
        }]);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, null, 'sku', null);
    }

    public function priceLists(): BelongsToMany
    {
        return $this->belongsToMany(PriceList::class, null, 'sku', null)->withPivot('price');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, null, 'sku', null)->withPivot('price');
    }

    protected function lowestPrice(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {

                $lowest_price = $attributes['price'];

                if ($this->relationLoaded('priceLists')) {
                    $price_list = $this->getRelation('priceLists')->first();
                    if ($price_list instanceof PriceList) {
                        $lowest_price = $price_list->pivot->price;
                    }
                }

                if ($this->relationLoaded('users')) {
                    $user = $this->getRelation('users')->first();
                    if ($user instanceof User) {
                        $lowest_price = $user->pivot->price;
                    }
                }

                return $lowest_price;
            },
        );
    }
}
