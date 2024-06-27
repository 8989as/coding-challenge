<?php

namespace App\Http\Controllers;

use App\Mail\IngredientStockAlert;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function placeOrder(Request $request)
    {
        try {
            $order = $request->input('order');
            $orderData = [];

            foreach ($order as $orderItem) {
                $product = Product::with('ingredients')->find($orderItem['product_id']);

                foreach ($product->ingredients as $ingredient) {
                    $requiredAmount = $ingredient->pivot->amount * $orderItem['quantity'];
                    $ingredient->stock -= $requiredAmount;

                    // Check if stock is below reorder point and email has not been sent
                    if ($ingredient->stock <= $ingredient->reOrder_point && !$ingredient->email_sent) {
                        Mail::to('asaeeed@outlook.com')->send(new IngredientStockAlert($ingredient));
                        $ingredient->email_sent = true;
                    }

                    $ingredient->save();
                }
                $orderData[$product->id] = ['quantity' => $orderItem['quantity']];
            }

            // Create a new order
            $newOrder = Order::create();

            foreach ($orderData as $productId => $data) {
                $newOrder->products()->attach($productId, ['quantity' => $data['quantity']]);
            }

            return response()->json([
                'message' => 'Order placed successfully!',
                'data' => $newOrder,
                'error' => null
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong!',
                'data' => null,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
