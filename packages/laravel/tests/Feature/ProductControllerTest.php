<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use Spatie\Snapshots\MatchesSnapshots;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use MatchesSnapshots;

    public function test_get_product(): void
    {
        $response = $this->json('GET', '/api/products/SKU-laptop-01');
        $response->assertStatus(200);
        $this->assertMatchesJsonSnapshot($response->json());
    }

    public function test_get_product_with_price_list(): void
    {
        $response = $this->json('GET', '/api/products/SKU-laptop-01?price_list_id=1');
        $response->assertStatus(200);
        $this->assertMatchesJsonSnapshot($response->json());
    }

    public function test_get_product_with_contract(): void
    {
        $response = $this->json('GET', '/api/products/SKU-laptop-01?user_id=1');
        $response->assertStatus(200);
        $this->assertMatchesJsonSnapshot($response->json());
    }

    public function test_get_product_with_price_list_and_contract(): void
    {
        $response = $this->json('GET', '/api/products/SKU-laptop-01?price_list_id=1&user_id=1');
        $response->assertStatus(200);
        $this->assertMatchesJsonSnapshot($response->json());
    }

    public function test_get_product_returns_404_for_non_existent_product(): void
    {
        $response = $this->json('GET', '/api/products/SKU-dummy-404');
        $response->assertStatus(404);
        $this->assertMatchesJsonSnapshot($response->json());
    }

    public function test_get_product_returns_404_for_not_published_product(): void
    {
        $response = $this->json('GET', '/api/products/SKU-laptop-16');
        $response->assertStatus(404);
        $this->assertMatchesJsonSnapshot($response->json());
    }

    public function test_filter_products_pagination(): void
    {
        $response = $this->json('GET', '/api/products/filter');
        $response->assertStatus(200);
        $this->assertMatchesJsonSnapshot($response->json());

        $response = $this->json('GET', '/api/products/filter?page=2');
        $response->assertStatus(200);
        $this->assertMatchesJsonSnapshot($response->json());
    }

    public function test_filter_products_prices(): void
    {
        $response = $this->json('GET', '/api/products/filter?price_list_id=1&sort_col=price&sort_dir=asc');
        $response->assertStatus(200);
        $this->assertMatchesJsonSnapshot($response->json());

        $response = $this->json('GET', '/api/products/filter?price_list_id=1&user_id=1&sort_col=price&sort_dir=asc');
        $response->assertStatus(200);
        $this->assertMatchesJsonSnapshot($response->json());
    }

    public function test_filter_products_category(): void
    {
        $response = $this->json('GET', '/api/products/filter?price_list_id=1&user_id=1&sort_col=price&sort_dir=asc&category_id=2');
        $response->assertStatus(200);
        $this->assertMatchesJsonSnapshot($response->json());
    }

    public function test_filter_products_price_range(): void
    {
        $response = $this->json('GET', '/api/products/filter?price_list_id=1&user_id=1&sort_col=price&sort_dir=asc&price=5000,50000');
        $response->assertStatus(200);
        $this->assertMatchesJsonSnapshot($response->json());
    }
}
