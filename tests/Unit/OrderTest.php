<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Order;
use App\Models\Product;
use App\Models\Ingredient;
use App\Models\IngredientProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Mail\IngredientStockAlert;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_places_an_order_and_deducts_ingredient_stock()
    {
        Mail::fake();

        $beef = Ingredient::factory()->create(['name' => 'Beef', 'stock' => 20000, 'reOrder_point' => 10000, 'email_sent' => false]);
        $cheese = Ingredient::factory()->create(['name' => 'Cheese', 'stock' => 5000, 'reOrder_point' => 2500, 'email_sent' => false]);
        $onion = Ingredient::factory()->create(['name' => 'Onion', 'stock' => 1000, 'reOrder_point' => 500, 'email_sent' => false]);

        // Create product using factories
        $burger = Product::factory()->create(['name' => 'Burger', 'description' => 'Delicious beef burger', 'price' => 100]);

        // Attach ingredients to products using pivot model
        IngredientProduct::create(['product_id' => $burger->id, 'ingredient_id' => $beef->id, 'amount' => 150]);
        IngredientProduct::create(['product_id' => $burger->id, 'ingredient_id' => $cheese->id, 'amount' => 30]);
        IngredientProduct::create(['product_id' => $burger->id, 'ingredient_id' => $onion->id, 'amount' => 20]);

        // Place an order
        $response = $this->postJson('/api/place-order', [
            'order' => [
                ['product_id' => $burger->id, 'quantity' => 2],
            ]
        ]);

        // Check response
        $response->assertStatus(200)->assertJson(['message' => 'Order placed successfully!']);

        // Check stock levels
        $this->assertEquals(19700, $beef->fresh()->stock);
        $this->assertEquals(4940, $cheese->fresh()->stock);
        $this->assertEquals(960, $onion->fresh()->stock);

        // Check order details
        $this->assertDatabaseHas('orders', ['id' => 1]);
        $this->assertDatabaseHas('order_product', [
            'order_id' => 1,
            'product_id' => $burger->id,
            'quantity' => 2,
        ]);

        // Assert that no emails were sent
        Mail::assertNothingSent();
    }

    public function it_sends_an_email_when_stock_reaches_reorder_point()
    {

        Mail::fake();

        // Create ingredient using factory
        $ingredient = Ingredient::factory()->create([
            'name' => 'Cheese',
            'stock' => 100,
            'unit' => 'grams',
            'reOrder_point' => 100,
            'email_sent' => false,
        ]);

        // Create product using factory
        $product = Product::factory()->create(['name' => 'Cheese Pizza', 'description' => 'Delicious cheese pizza', 'price' => 200]);

        // Attach ingredient to product using pivot model
        IngredientProduct::create(['product_id' => $product->id, 'ingredient_id' => $ingredient->id, 'amount' => 50]);

        // Place an order
        $response = $this->postJson('/api/place-order', [
            'order' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ]
        ]);

        // Check response
        $response->assertStatus(200)->assertJson(['message' => 'Order placed successfully!']);

        // Check stock level
        $this->assertEquals(100, $ingredient->stock);

        // Check if email_sent is true
        $this->assertFalse($ingredient->email_sent);

        // Assert that an email was sent
        Mail::assertSent(IngredientStockAlert::class, function ($mail) use ($ingredient) {
            return $mail->ingredient->id === $ingredient->id;
        });
    }

    public function it_does_not_send_an_email_if_stock_is_below_reorder_point_and_email_already_sent()
    {
        Mail::fake();

        // Create ingredient using factory
        $ingredient = Ingredient::factory()->create([
            'name' => 'Cheese',
            'stock' => 200,
            'unit' => 'grams',
            'reOrder_point' => 100,
            'email_sent' => true,
        ]);

        // Create product using factory
        $product = Product::factory()->create(['name' => 'Cheese Pizza', 'description' => 'Delicious cheese pizza', 'price' => 200]);

        // Attach ingredient to product using pivot model
        IngredientProduct::create(['product_id' => $product->id, 'ingredient_id' => $ingredient->id, 'amount' => 100]);

        // Place an order that will trigger the email alert
        $response1 = $this->postJson('/api/place-order', [
            'order' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ]
        ]);

        // Check response
        $response1->assertStatus(200)->assertJson(['message' => 'Order placed successfully!']);

        // Check stock level
        $this->assertEquals(200, $ingredient->stock);

        // Check if email_sent is true
        $this->assertTrue($ingredient->email_sent);

        // Assert that an email was sent
        Mail::assertSent(IngredientStockAlert::class, 0);

        // Place another order that should not trigger the email alert again
        $response2 = $this->postJson('/api/place-order', [
            'order' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ]
        ]);

        // Check response
        $response2->assertStatus(200)->assertJson(['message' => 'Order placed successfully!']);

        // Check stock level
        $this->assertEquals(200, $ingredient->stock);

        // Assert that no additional emails were sent
        Mail::assertSent(IngredientStockAlert::class, 0);
    }
}
