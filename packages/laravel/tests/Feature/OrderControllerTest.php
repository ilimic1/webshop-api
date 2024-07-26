<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use Spatie\Snapshots\MatchesSnapshots;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use MatchesSnapshots;

    public function test_create_order(): void
    {
        $response = $this->json('POST', '/api/orders', [
            'products' => [
                'SKU-laptop-01',
                'SKU-laptop-02',
                'SKU-laptop-03',
            ],
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john-doe-customer@example.com',
            'phone' => '555-555-5555',
            'address' => 'Fake St. 22b',
            'city' => 'Berlin',
            'country' => 'Germany',
        ]);

        $data = [
            'subtotal' => 3000_00,
            'total' => 3375_00,
            'modifiers' => [
                [
                    'name' => 'PDV',
                    'amount' => 750_00,
                ],
                [
                    'name' => 'Discount',
                    'amount' => -375_00,
                ],
            ],
        ];

        $response->assertStatus(200);
        $response->assertJson($data);
    }

    public function test_create_order_with_price_overrides(): void
    {
        $response = $this->json('POST', '/api/orders', [
            'products' => [
                'SKU-laptop-01',
                'SKU-laptop-02',
                'SKU-laptop-03',
            ],
            'price_list_id' => 1,
            'user_id' => 1,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john-doe-customer@example.com',
            'phone' => '555-555-5555',
            'address' => 'Fake St. 22b',
            'city' => 'Berlin',
            'country' => 'Germany',
        ]);

        $data = [
            'subtotal' => 2340_00,
            'total' => 2632_50,
            'modifiers' => [
                [
                    'name' => 'PDV',
                    'amount' => 585_00,
                ],
                [
                    'name' => 'Discount',
                    'amount' => -292_50,
                ],
            ],
        ];

        $response->assertStatus(200);
        $response->assertJson($data);
    }

    public function test_create_order_no_discount_under_100(): void
    {
        $response = $this->json('POST', '/api/orders', [
            'products' => [
                'SKU-keyboard-01',
                'SKU-keyboard-02',
                'SKU-keyboard-03',
            ],
            'price_list_id' => 1,
            'user_id' => 1,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john-doe-customer@example.com',
            'phone' => '555-555-5555',
            'address' => 'Fake St. 22b',
            'city' => 'Berlin',
            'country' => 'Germany',
        ]);

        $data = [
            'subtotal' => 60_00,
            'total' => 75_00,
            'modifiers' => [
                [
                    'name' => 'PDV',
                    'amount' => 15_00,
                ],
            ],
        ];

        $response->assertStatus(200);
        $response->assertJson($data);

        $this->assertNull(
            collect($response->json()['modifiers'])->first(fn (array $modifier) => $modifier['name'] === 'Discount'),
            'Discount modifier should not be applied.',
        );
    }
}
