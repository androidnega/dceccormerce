<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_home_search_query_redirects_to_product_catalog(): void
    {
        $this->get('/?search=iphone')
            ->assertRedirect(route('products.index', ['search' => 'iphone']));
    }

    public function test_home_category_query_redirects_to_product_catalog(): void
    {
        $this->get('/?category=iphones')
            ->assertRedirect(route('products.index', ['category' => 'iphones']));
    }

    public function test_product_catalog_page_loads(): void
    {
        $this->get(route('products.index'))
            ->assertOk();
    }
}
