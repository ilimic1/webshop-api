<?php

namespace App\Http\Controllers;

use App\Models\LineItem;
use App\Models\Modifier;
use App\Models\Order;
use App\Models\Product;
use App\PriceModifiers\DiscountModifier;
use App\PriceModifiers\PdvModifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'products' => 'required|array|min:1',
            'products.*' => 'required|string|min:1|distinct|exists:products,sku',
            'price_list_id' => 'integer|gt:0|exists:price_lists,id',
            'user_id' => 'integer|gt:0|exists:users,id',
            'first_name' => 'required|string|min:1|max:255',
            'last_name' => 'required|string|min:1|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|min:1|max:255',
            'address' => 'required|string|min:1|max:255',
            'city' => 'required|string|min:1|max:255',
            'country' => 'required|string|min:1|max:255',
        ]);

        $price_list_id = $validated['price_list_id'] ?? null;
        $user_id = $validated['user_id'] ?? null;

        $productsQb = Product::query();

        if ($price_list_id) {
            $productsQb->withPriceList($price_list_id);
        }

        if ($user_id) {
            $productsQb->withContract($user_id);
        }

        $products = $productsQb->findMany($validated['products']);

        $line_items = $products->map(fn (Product $product) => new LineItem([
            'sku' => $product->sku,
            'name' => $product->name,
            'price' => $product->lowest_price,
            'quantity' => 1,
        ]))->all();

        $subtotal = array_reduce(
            $line_items,
            fn (int $acc, LineItem $line_item) => $acc + ($line_item->quantity * $line_item->price),
            0
        );
        $total = $subtotal;

        $order = new Order([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'country' => $validated['country'],
            'subtotal' => $subtotal,
            'total' => $total,
        ]);

        $modifiers = [
            new PdvModifier(0.25),
            new DiscountModifier(100_00, 0.10),
        ];
        $applied_modifiers = [];

        foreach ($modifiers as $modifier) {
            if ($modifier->applies($line_items, $order->total)) {
                $applied_modifier = $modifier->apply($line_items, $order->total);
                $order->total = $applied_modifier->new_total;

                $applied_modifiers[] = new Modifier([
                    'name' => $applied_modifier->name,
                    'amount' => $applied_modifier->amount,
                    'description' => $applied_modifier->description,
                ]);
            }
        }

        $order_id = DB::transaction(function () use ($order, $line_items, $applied_modifiers) {
            $order->save();
            $order->line_items()->saveMany($line_items);
            $order->modifiers()->saveMany($applied_modifiers);

            return $order->id;
        });

        return Order::query()->with(['line_items', 'modifiers'])->findOrFail($order_id);
    }
}
