<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function placeOrder(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $orderItem) {
            $product = Product::find($orderItem['product_id']);

            foreach ($product->ingredients as $ingredient) {
                $requiredAmount = $ingredient->pivot->amount * $orderItem['quantity'];

                if ($ingredient->stock < $requiredAmount) {
                    return response()->json(['error' => 'Not enough stock for ingredient: ' . $ingredient->name], 400);
                }

                $ingredient->stock -= $requiredAmount;
                $ingredient->save();
            }
        }

        return response()->json(['message' => 'Order placed successfully!']);
    }
}
