<?php

namespace App\Http\Controllers;

use App\Mail\IngredientStockAlert;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    // public function placeOrder(Request $request)
    // {
    //     try {
    //         $order = $request->input('order');

    //     foreach ($order as $orderItem) {
    //         $product = Product::with('ingredients')->find($orderItem['product_id']);

    //         foreach ($product->ingredients as $ingredient) {
    //             $requiredAmount = $ingredient->pivot->amount * $orderItem['quantity'];
    //             $ingredient->stock -= $requiredAmount;

    //             // Check if the stock has reached the re-order point and email has not been sent
    //             if ($ingredient->stock <= $ingredient->reOrder_point && !$ingredient->email_sent) {

    //                 // Send alert email assueming that the email is coming from our customers database
    //                 Mail::to('asaeeed@outlook.com')->send(new IngredientStockAlert($ingredient));

    //                 $ingredient->email_sent = true;
    //             }

    //             $ingredient->save();
    //         }
    //     }
    //         return response()->json([
    //             'message' => 'Order placed successfully!',
    //             'data' => $product,
    //             'error' => null
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => 'Something went wrong!',
    //             'data' => null,
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function placeOrder(Request $request)
    {
        try {
            $orderData = $request->input('order');

            // Create an order
            $order = Order::create();

            foreach ($orderData as $orderItem) {
                $product = Product::with('ingredients')->find($orderItem['product_id']);

                foreach ($product->ingredients as $ingredient) {
                    $requiredAmount = $ingredient->pivot->amount * $orderItem['quantity'];
                    $ingredient->stock -= $requiredAmount;

                    // Check if the stock has reached the re-order point and email has not been sent
                    if ($ingredient->stock <= $ingredient->reOrder_point && !$ingredient->email_sent) {
                        // Send alert email
                        Mail::to('asaeeed@outlook.com')->send(new IngredientStockAlert($ingredient));

                        $ingredient->email_sent = true;
                    }

                    $ingredient->save();
                }

                // Attach product to order with quantity
                $order->products()->attach($orderItem['product_id'], ['quantity' => $orderItem['quantity']]);
            }

            return response()->json([
                'message' => 'Order placed successfully!',
                'data' => $order,
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
