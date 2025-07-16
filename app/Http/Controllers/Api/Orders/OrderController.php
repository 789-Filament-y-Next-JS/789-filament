<?php

namespace App\Http\Controllers\Api\Orders;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // VALIDAR CLIENTE
        $customer = $request->customer;

        $customerExists = Customer::where('email', $customer['email'])->first();

        if( !$customerExists ){
            Customer::create($customer);
        }

        // CREAR VENTA
        $order = new Order();

        $order->customer_id = $customerExists->id;
        $order->total = $request->total;

        $order->save();

        // ASIGNAR PRODUCTOS A LA VENTA
        $products = $request->orderDetails;
        $details = [];

        foreach( $products as $product )
        {
            $details[] = [
                "order_id" => $order->id,
                "product_id" => $product['productId'],
                "quantity" => $product['quantity'],
                "sub_total" => $product['quantity'] * $product['productPrice'],
            ];
        }

        DB::table('order_products')->insert($details);

        return response()->json([
            'message' => 'Venta creada con exito',
            "details" => "Numero de venta: #{$order->id}",
        ], 201);
    }
}
