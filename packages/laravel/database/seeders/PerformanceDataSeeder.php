<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\PriceList;
use App\Models\Product;
use App\Models\User;
use Exception;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerformanceDataSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $start_time = microtime(true);
        $this->command->info('Starting performance data seeder.');

        $this->seedPerformanceData();

        $elapsed_time = microtime(true) - $start_time;
        $this->command->info('Performance data seeder finished in ['.round($elapsed_time, 2).'] sec.');
    }

    /**
     * Generate a ton of data for performance testing.
     * - 5K users
     * - 20K products
     * - 1K categories
     * - 500K contract prices
     * - 5M price list prices
     */
    private function seedPerformanceData(): void
    {
        $users_insert_data = [];
        $products_insert_data = [];
        $category_product_insert_data = [];
        $price_lists_insert_data = [];
        $price_list_product_insert_data = [];

        for ($i = 0; $i < 5000; $i++) {
            $users_insert_data[$i + 1] = [
                'id' => $i + 1,
                'name' => 'John Doe '.$i + 1,
                'email' => 'john-doe-'.$i + 1 .'@example.com',
                'password' => 'test',
            ];
        }

        for ($i = 0; $i < 10; $i++) {
            $c1 = Category::factory()->create([
                'name' => 'Category '.$i + 1,
                'description' => 'Description '.$i + 1,
                'parent_id' => null,
            ]);
            $categories[$c1->id] = $c1;

            for ($j = 0; $j < 10; $j++) {
                $c2 = Category::factory()->create([
                    'name' => 'Category '.$i + 1 .'.'.$j + 1,
                    'description' => 'Description '.$i + 1 .'.'.$j + 1,
                    'parent_id' => $c1->id,
                ]);
                $categories[$c2->id] = $c2;

                for ($z = 0; $z < 10; $z++) {
                    $c3 = Category::factory()->create([
                        'name' => 'Category '.$i + 1 .'.'.$j + 1 .'.'.$z + 1,
                        'description' => 'Description '.$i + 1 .'.'.$j + 1 .'.'.$z + 1,
                        'parent_id' => $c2->id,
                    ]);
                    $categories[$c3->id] = $c3;
                }
            }
        }

        for ($i = 0; $i < 250; $i++) {
            $price_lists_insert_data[$i + 1] = [
                'name' => 'Price List '.$i + 1,
            ];
        }

        for ($i = 0; $i < 20_000; $i++) {

            $sku = 'SKU-'.str_pad($i + 1, 5, '0', STR_PAD_LEFT); // generate SKUs like "SKU-00010"
            $price = 1_000 + $i + 1; // start with price of $10

            $products_insert_data[] = [
                'sku' => $sku,
                'name' => 'Product '.$i + 1,
                'description' => 'Lorem ipsum dolor '.($i + 1).'.',
                'price' => $price,
                'published' => (bool) ($i % 5), // make 20% of products not published
            ];

            // put 10 products in each category
            $category = $categories[$i / 10] ?? null;
            if ($category) {
                // $product->categories()->attach($category->id);
                $category_product_insert_data[] = [
                    'category_id' => $category->id,
                    'sku' => $sku,
                ];
            }

            // for ($j = 0; $j < 250; $j++) {
            //     $price_list_product_insert_data[] = [
            //         'price_list_id' => $j + 1,
            //         'sku'           => $sku,
            //         'price'         => $price * 0.90,
            //     ];
            // }
        }

        foreach (array_chunk($users_insert_data, 1000) as $data) {
            User::insert($data);
        }

        foreach (array_chunk($products_insert_data, 1000) as $data) {
            Product::insert($data);
        }

        foreach (array_chunk($category_product_insert_data, 1000) as $data) {
            DB::table('category_product')->insert($data);
        }

        foreach (array_chunk($price_lists_insert_data, 1000) as $data) {
            PriceList::insert($data);
        }

        $this->seedProductUser($products_insert_data, $users_insert_data);
        $this->seedPriceListProduct($products_insert_data, $price_lists_insert_data);

        // foreach (array_chunk($price_list_product_insert_data, 1000) as $data) {
        //     DB::table('price_list_product')->insert($data);
        // }
    }

    private function seedProductUser(array $products_insert_data, array $users_insert_data): void
    {
        $products_per_user = 100;

        if ($products_per_user > count($products_insert_data)) {
            throw new Exception('products per user can\'t be greater than number of products');
        }

        foreach ($users_insert_data as $user_id => $user_insert_data) {

            $data = [];

            for ($i = 0; $i < $products_per_user; $i++) {
                $product_index = (($user_id - 1) * $products_per_user + ($i + 1)) % count($products_insert_data);
                $data[] = [
                    'user_id' => $user_id,
                    'sku' => $products_insert_data[$product_index]['sku'],
                    'price' => round($products_insert_data[$product_index]['price'] * 0.80),
                ];
            }

            DB::table('product_user')->insert($data);
        }
    }

    private function seedPriceListProduct(array $products_insert_data, array $price_lists_insert_data): void
    {
        $data = [];
        $count = 0;

        foreach ($products_insert_data as $product_insert_data) {
            foreach ($price_lists_insert_data as $price_list_id => $price_list_insert_data) {
                $data[] = [
                    'price_list_id' => $price_list_id,
                    'sku' => $product_insert_data['sku'],
                    'price' => round($product_insert_data['price'] * 0.90),
                ];
                $count++;
            }
            if ($count >= 1000) {
                DB::table('price_list_product')->insert($data);
                $data = [];
                $count = 0;
            }
        }
    }
}
