<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use stdClass;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'price_list_id' => 'integer|gt:0|exists:price_lists,id',
            'user_id' => 'integer|gt:0|exists:users,id',
        ]);

        $price_list_id = $validated['price_list_id'] ?? null;
        $user_id = $validated['user_id'] ?? null;

        $qb = Product::query()
            ->published()
            ->with('categories');

        if ($price_list_id) {
            $qb->withPriceList($price_list_id);
        }

        if ($user_id) {
            $qb->withContract($user_id);
        }

        return $qb->paginate(10)->withQueryString();
    }

    public function filter(Request $request)
    {
        $validated = $request->validate([
            'price' => 'regex:/^\d+,\d+$/',
            'name' => 'string|min:3',
            'category_id' => 'integer|gt:0|exists:categories,id',
            'price_list_id' => 'integer|gt:0|exists:price_lists,id',
            'user_id' => 'integer|gt:0|exists:users,id',
            'sort_col' => [Rule::in(['price', 'name'])],
            'sort_dir' => [Rule::in(['asc', 'desc'])],
        ]);

        $price = isset($validated['price']) ? explode(',', $validated['price']) : null;
        $name = $validated['name'] ?? null;
        $category_id = $validated['category_id'] ?? null;
        $price_list_id = $validated['price_list_id'] ?? null;
        $user_id = $validated['user_id'] ?? null;
        $sort_col = $validated['sort_col'] ?? 'price';
        $sort_dir = $validated['sort_dir'] ?? 'asc';

        $category_ids = $category_id ? Category::findOrFail($category_id)->descendantsAndSelf : collect();
        // $category_names = $category_ids->pluck('name')->all();
        $category_ids = $category_ids->pluck('id')->all();

        $select = [
            'products.sku',
            'COALESCE(any_value(product_user.price), any_value(price_list_product.price), products.price) AS lowest_price',
        ];

        $qb = DB::table('products')->select(DB::raw(implode(',', $select)));

        $qb->leftJoin('price_list_product', function (JoinClause $join) use ($price_list_id) {
            $join
                ->on('price_list_product.sku', '=', 'products.sku')
                // ->on('price_list_product.price_list_id', '=', $price_list_id)
                ->where('price_list_product.price_list_id', '=', $price_list_id);
        });

        $qb->leftJoin('product_user', function (JoinClause $join) use ($user_id) {
            $join
                ->on('product_user.sku', '=', 'products.sku')
                ->where('product_user.user_id', '=', $user_id);
        });

        if ($category_id) {
            $qb->leftJoin('category_product', function (JoinClause $join) {
                $join->on('category_product.sku', '=', 'products.sku');
            });
            $qb->whereIn('category_product.category_id', $category_ids);
        }

        if ($name) {
            $qb->where('products.name', 'ILIKE', '%'.$name.'%');
        }

        if ($price) {
            $qb->whereRaw('COALESCE(product_user.price,price_list_product.price,products.price) BETWEEN ? AND ?', [$price[0], $price[1]]);
        }

        $qb->where('products.published', '=', true);

        if ($sort_col) {
            match ($sort_col) {
                'price' => $qb->orderBy('lowest_price', $sort_dir),
                'name' => $qb->orderBy('name', $sort_dir),
            };
        }

        $qb->groupBy('products.sku');

        $results = $qb->paginate(10)->withQueryString();

        if ($results->isNotEmpty()) {
            $productsQb = Product::query()->with('categories');

            if ($price_list_id) {
                $productsQb->withPriceList($price_list_id);
            }

            if ($user_id) {
                $productsQb->withContract($user_id);
            }

            $products = $productsQb->findMany($results->getCollection()->pluck('sku')->all());

            $results->through(function (stdClass $result) use ($products) {
                return $products->first(fn (Product $product) => $product->sku === $result->sku);
            });
        }

        return $results;
    }

    public function show(Request $request, string $sku)
    {
        $validated = $request->validate([
            'price_list_id' => 'integer|gt:0|exists:price_lists,id',
            'user_id' => 'integer|gt:0|exists:users,id',
        ]);

        $price_list_id = $validated['price_list_id'] ?? null;
        $user_id = $validated['user_id'] ?? null;

        $qb = Product::query()
            ->published()
            ->with('categories');

        if ($price_list_id) {
            $qb->withPriceList($price_list_id);
        }

        if ($user_id) {
            $qb->withContract($user_id);
        }

        return $qb->findOrFail($sku);
    }
}
