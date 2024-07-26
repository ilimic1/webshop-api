<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\PriceList;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FunctionalDataSeeder extends Seeder
{
    private Carbon $date;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->date = new Carbon('2022-01-01 00:00:00');

        $start_time = microtime(true);
        $this->command->info('Starting functional data seeder.');

        $this->seedTestData();

        $elapsed_time = microtime(true) - $start_time;
        $this->command->info('Functional data seeder finished in ['.round($elapsed_time, 2).'] sec.');
    }

    /**
     * Hand crafted data for functional testing.
     */
    private function seedTestData(): void
    {
        $users = [];

        for ($i = 0; $i < 5; $i++) {
            $users[$i + 1] = [
                'id' => $i + 1,
                'name' => 'John Doe '.str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                'email' => 'john-doe-'.str_pad($i + 1, 2, '0', STR_PAD_LEFT).'@example.com',
                'password' => 'test',
                'contracts' => [],
            ];
        }

        $users[1]['contracts'] = [
            'SKU-laptop-01' => 400_00,
            'SKU-laptop-02' => 440_00,
            'SKU-laptop-11' => 1050_00,
            'SKU-laptop-12' => 1060_00,
        ];

        foreach ($users as $user) {
            User::factory()->create([
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => $user['password'],
                'email_verified_at' => $this->date,
                'created_at' => $this->date,
                'updated_at' => $this->date,
            ]);
        }

        $categories_data = [
            [
                'id' => 'cat_computers',
                'name' => 'Computers',
                'description' => 'Lorem ipsum dolor.',
                'subcategories' => [
                    [
                        'id' => 'cat_laptops',
                        'name' => 'Laptops',
                        'description' => 'Lorem ipsum dolor.',
                        'subcategories' => [],
                    ],
                    [
                        'id' => 'cat_keyboards',
                        'name' => 'Keyboards',
                        'description' => 'Lorem ipsum dolor.',
                        'subcategories' => [],
                    ],
                ],
            ],
            [
                'id' => 'cat_brands',
                'name' => 'Brands',
                'description' => 'Lorem ipsum dolor.',
                'subcategories' => [
                    [
                        'id' => 'cat_brands_lenovo',
                        'name' => 'Lenovo',
                        'description' => 'Lorem ipsum dolor.',
                        'subcategories' => [],
                    ],
                    [
                        'id' => 'cat_brands_apple',
                        'name' => 'Apple',
                        'description' => 'Lorem ipsum dolor.',
                        'subcategories' => [],
                    ],
                ],
            ],
            [
                'id' => 'cat_test_category_with_no_products_1',
                'name' => 'Test Category With no Products 1',
                'description' => 'Lorem ipsum dolor.',
                'subcategories' => [
                    [
                        'id' => 'cat_test_category_with_no_products_1_1',
                        'name' => 'Test Category With no Products 1.1',
                        'description' => 'Lorem ipsum dolor.',
                        'subcategories' => [
                            [
                                'id' => 'cat_test_category_with_no_products_1_1_1',
                                'name' => 'Test Category With no Products 1.1.1',
                                'description' => 'Lorem ipsum dolor.',
                                'subcategories' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $categories = $this->recursivelyAddCategories($categories_data);

        $products_data = [];

        for ($i = 0; $i < 25; $i++) {
            $sku = 'SKU-laptop-'.str_pad($i + 1, 2, '0', STR_PAD_LEFT);
            $products_data[$sku] = [
                'sku' => $sku,
                'name' => 'Laptop '.str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                'description' => 'Description '.str_pad($i + 1, 2, '0', STR_PAD_LEFT).'.',
                'price' => 500_00 + $i * 500_00,
                'published' => true,
                'categories' => ['cat_laptops'],
            ];
        }

        for ($i = 0; $i < 5; $i++) {
            $sku = 'SKU-keyboard-'.str_pad($i + 1, 2, '0', STR_PAD_LEFT);
            $products_data[$sku] = [
                'sku' => $sku,
                'name' => 'Keyboard '.str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                'description' => 'Description '.str_pad($i + 1, 2, '0', STR_PAD_LEFT).'.',
                'price' => 10_00 + $i * 10_00,
                'published' => true,
                'categories' => ['cat_keyboards'],
            ];
        }

        $products_data['SKU-laptop-11']['categories'][] = 'cat_brands_apple';
        $products_data['SKU-laptop-12']['categories'][] = 'cat_brands_apple';

        $products_data['SKU-laptop-16']['published'] = false;

        $products = [];

        foreach ($products_data as $sku => $product_data) {
            $products[$sku] = Product::factory()->create([
                'sku' => $sku,
                'name' => $product_data['name'],
                'description' => $product_data['description'],
                'price' => $product_data['price'],
                'published' => $product_data['published'],
                'created_at' => $this->date,
                'updated_at' => $this->date,
            ]);
            foreach ($product_data['categories'] as $cat) {
                $category = $categories[$cat] ?? null;
                if ($category) {
                    $products[$sku]->categories()->attach($category->id);
                }
            }
        }

        $price_lists_data = [
            [
                'name' => 'Partner Computer Service Shop 01',
                'prices' => [
                    ['sku' => 'SKU-laptop-01', 'price' => 450_00],
                    ['sku' => 'SKU-laptop-02', 'price' => 495_00],
                    ['sku' => 'SKU-laptop-11', 'price' => 1010_00],
                    ['sku' => 'SKU-laptop-12', 'price' => 1020_00],
                ],
            ],
            [
                'name' => 'Test Empty Price List',
                'prices' => [],
            ],
        ];

        foreach ($price_lists_data as $price_list_data) {
            $price_list = PriceList::factory()->create([
                'name' => $price_list_data['name'],
                'created_at' => $this->date,
                'updated_at' => $this->date,
            ]);
            foreach ($price_list_data['prices'] as $price_data) {
                DB::table('price_list_product')->insert([
                    'price_list_id' => $price_list->id,
                    'sku' => $price_data['sku'],
                    'price' => $price_data['price'],
                ]);
                // $price_list->products()->attach($price_data['sku'], ['price' => $price_data['price']]);
            }
        }

        foreach ($users as $user) {
            foreach ($user['contracts'] as $sku => $price) {
                DB::table('product_user')->insert([
                    'user_id' => $user['id'],
                    'sku' => $sku,
                    'price' => $price,
                ]);
            }
        }
    }

    private function recursivelyAddCategories(array $categories, ?int $parent_id = null): array
    {
        $models = [];

        foreach ($categories as $category) {
            $categoryModel = Category::factory()->create([
                'name' => $category['name'],
                'description' => $category['description'],
                'parent_id' => $parent_id,
                'created_at' => $this->date,
                'updated_at' => $this->date,
            ]);

            $models = [
                ...$models,
                $category['id'] => $categoryModel,
                ...$this->recursivelyAddCategories($category['subcategories'], $categoryModel->id),
            ];
        }

        return $models;
    }
}
