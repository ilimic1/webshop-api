<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function products(Request $request, Category $category)
    {
        $validated = $request->validate([
            'price_list_id' => 'integer|gt:0|exists:price_lists,id',
            'user_id' => 'integer|gt:0|exists:users,id',
        ]);

        $price_list_id = $validated['price_list_id'] ?? null;
        $user_id = $validated['user_id'] ?? null;

        $qb = $category
            ->recursiveProducts()
            ->published();

        if ($price_list_id) {
            $qb->withPriceList($price_list_id);
        }

        if ($user_id) {
            $qb->withContract($user_id);
        }

        return $qb->paginate(10)->withQueryString();
    }
}
