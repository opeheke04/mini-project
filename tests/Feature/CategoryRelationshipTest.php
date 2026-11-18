<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_and_product_relationships_are_linked(): void
    {
        $category = Category::create([
            'name' => 'Minuman',
            'slug' => 'minuman',
        ]);
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'category' => $category->name,
        ]);

        $this->assertSame($category->id, $product->category()->firstOrFail()->id);
        $this->assertSame($category->id, $product->categoryRecord->id);
        $this->assertTrue($category->products->contains('id', $product->id));
    }
}
