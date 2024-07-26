<?php

namespace Tests\Performance;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;

class PerformanceTest extends TestCase
{
    public function test_products_filter_endpoint(): void
    {
        $urls = [
            '/api/products/filter?price=1000,2000&name=product 1&category_id=1&price_list_id=1&user_id=1&sort_col=price&sort_dir=asc&page=1',
            '/api/products/filter?price=1000,2000&category_id=1&price_list_id=1&user_id=1&sort_col=price&sort_dir=asc&page=1',
            '/api/products/filter?price=1000,2000&category_id=1&price_list_id=1&user_id=1&sort_col=price&sort_dir=desc&page=1',
            '/api/products/filter?price=1000,2000&category_id=1&price_list_id=1&user_id=1&sort_col=name&sort_dir=asc&page=1',
            '/api/products/filter?price=1000,2000&category_id=1&price_list_id=1&user_id=1&sort_col=name&sort_dir=desc&page=1',
            '/api/products/filter?price=1000,2000&category_id=1&price_list_id=1&sort_col=name&sort_dir=desc&page=1',
            '/api/products/filter?price=1000,2000&category_id=1&user_id=1&sort_col=name&sort_dir=desc&page=1',
            '/api/products/filter?price=1000,2000&name=product 1&price_list_id=1&user_id=1&sort_col=price&sort_dir=asc&page=1',
            '/api/products/filter?price=1000,2000&price_list_id=1&user_id=1&sort_col=price&sort_dir=asc&page=1',
            '/api/products/filter?price=1000,2000&price_list_id=1&user_id=1&sort_col=price&sort_dir=desc&page=1',
            '/api/products/filter?price=1000,2000&price_list_id=1&user_id=1&sort_col=name&sort_dir=asc&page=1',
            '/api/products/filter?price=1000,2000&price_list_id=1&user_id=1&sort_col=name&sort_dir=desc&page=1',
            '/api/products/filter?price=1000,2000&price_list_id=1&sort_col=name&sort_dir=desc&page=1',
            '/api/products/filter?price=1000,2000&user_id=1&sort_col=name&sort_dir=desc&page=1',
            '/api/products/filter?name=product 1&price_list_id=1&user_id=1&sort_col=price&sort_dir=asc&page=1',
            '/api/products/filter?price_list_id=1&user_id=1&sort_col=price&sort_dir=asc&page=1',
            '/api/products/filter?price_list_id=1&user_id=1&sort_col=price&sort_dir=desc&page=1',
            '/api/products/filter?price_list_id=1&user_id=1&sort_col=name&sort_dir=asc&page=1',
            '/api/products/filter?price_list_id=1&user_id=1&sort_col=name&sort_dir=desc&page=1',
            '/api/products/filter?price_list_id=1&sort_col=name&sort_dir=desc&page=1',
            '/api/products/filter?user_id=1&sort_col=name&sort_dir=desc&page=1',
            '/api/products/filter?page=1',
        ];

        // TODO: improve phpunit output to see which URL failed
        // TODO: don't stop after failing test

        foreach ($urls as $url) {
            $start = hrtime(true);
            $response = $this->json('GET', $url);
            $time = (hrtime(true) - $start) / 1_000_000;

            $json = $response->json();
            $response->assertStatus(200);
            $this->assertLessThanOrEqual(1000, $time);

            // also test the last page

            $last_page_url = $json['last_page_url'] ?? null;
            if (! $last_page_url) {
                continue;
            }

            $start = hrtime(true);
            $response = $this->json('GET', $last_page_url);
            $time = (hrtime(true) - $start) / 1_000_000;

            $json = $response->json();
            $response->assertStatus(200);
            $this->assertLessThanOrEqual(1000, $time);
        }
    }
}
