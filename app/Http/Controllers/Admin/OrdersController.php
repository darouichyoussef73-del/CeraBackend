<?php



// namespace App\Http\Controllers\Admin;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;
// use App\Models\Order;
// use App\Models\OrderItem;
// use App\Models\Product;

// class OrdersController extends Controller
// {
//     // Generate unique order ID
//     private function generateOrderId(): string
//     {
//         return 'ORD-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
//     }

//     // GET /api/admin/orders
//     public function index()
//     {
//         try {
//             $orders = Order::with('items')->orderBy('created_at', 'desc')->get();
//             return response()->json($orders);
//         } catch (\Exception $e) {
//             Log::error('Orders index error: ' . $e->getMessage());
//             return response()->json(['error' => $e->getMessage()], 500);
//         }
//     }

//     // GET /api/admin/orders/{id}
//     public function show($id)
//     {
//         try {
//             $order = Order::with('items')->findOrFail($id);
//             return response()->json($order);
//         } catch (\Exception $e) {
//             Log::error('Orders show error: ' . $e->getMessage());
//             return response()->json(['error' => $e->getMessage()], 500);
//         }
//     }

//     // POST /api/admin/orders
//     public function store(Request $request)
//     {
//         try {
//             $validated = $request->validate([
//                 'customer_name' => 'required|string|max:255',
//                 'customer_email' => 'required|email|max:255',
//                 'customer_phone' => 'nullable|string|max:20',
//                 'shipping_address' => 'required|string',
//                 'city' => 'required|string|max:100',
//                 'country' => 'nullable|string|max:100',
//                 'zip_code' => 'nullable|string|max:20',
//                 'payment_method' => 'required|string|in:card,paypal,cod',
//                 'subtotal' => 'required|numeric|min:0',
//                 'shipping' => 'required|numeric|min:0',
//                 'discount' => 'nullable|numeric|min:0',
//                 'tax' => 'nullable|numeric|min:0',
//                 'total' => 'required|numeric|min:0',
//                 'coupon_code' => 'nullable|string|max:50',
//                 'coupon_discount' => 'nullable|numeric|min:0',
//                 'items' => 'required|array|min:1',
//                 'items.*.product_id' => 'nullable|integer|exists:products,id',
//                 'items.*.product_name' => 'required|string|max:255',
//                 'items.*.product_sku' => 'nullable|string|max:100',
//                 'items.*.product_image' => 'nullable|string',
//                 'items.*.price' => 'required|numeric|min:0',
//                 'items.*.quantity' => 'required|integer|min:1',
//                 'items.*.total' => 'required|numeric|min:0',
//             ]);

//             return DB::transaction(function () use ($validated) {
//                 // Create order
//                 $order = Order::create([
//                     'order_id' => $this->generateOrderId(),
//                     'user_id' => auth()->id(), // null if guest
//                     'customer_name' => $validated['customer_name'],
//                     'customer_email' => $validated['customer_email'],
//                     'customer_phone' => $validated['customer_phone'] ?? null,
//                     'shipping_address' => $validated['shipping_address'],
//                     'city' => $validated['city'],
//                     'country' => $validated['country'] ?? 'USA',
//                     'zip_code' => $validated['zip_code'] ?? null,
//                     'payment_method' => $validated['payment_method'],
//                     'payment_status' => $validated['payment_method'] === 'cod' ? 'pending' : 'paid',
//                     'subtotal' => $validated['subtotal'],
//                     'shipping' => $validated['shipping'],
//                     'discount' => $validated['discount'] ?? 0,
//                     'tax' => $validated['tax'] ?? 0,
//                     'total' => $validated['total'],
//                     'coupon_code' => $validated['coupon_code'] ?? null,
//                     'coupon_discount' => $validated['coupon_discount'] ?? 0,
//                     'status' => 'Processing',
//                     'paid_at' => $validated['payment_method'] === 'cod' ? null : now(),
//                 ]);

//                 // Create order items
//                 foreach ($validated['items'] as $item) {
//                     OrderItem::create([
//                         'order_id' => $order->id,
//                         'product_id' => $item['product_id'] ?? null,
//                         'product_name' => $item['product_name'],
//                         'product_sku' => $item['product_sku'] ?? null,
//                         'product_image' => $item['product_image'] ?? null,
//                         'price' => $item['price'],
//                         'quantity' => $item['quantity'],
//                         'total' => $item['total'],
//                     ]);

//                     // Decrease stock if product exists
//                     if (!empty($item['product_id'])) {
//                         $product = Product::find($item['product_id']);
//                         if ($product && $product->stock !== null) {
//                             $product->decrement('stock', $item['quantity']);
                            
//                             // Update status if low stock
//                             if ($product->stock <= 0) {
//                                 $product->update(['status' => 'out of stock']);
//                             } elseif ($product->stock <= 5) {
//                                 $product->update(['status' => 'low stock']);
//                             }
//                         }
//                     }
//                 }

//                 return response()->json([
//                     'success' => true,
//                     'message' => 'Order created successfully',
//                     'order' => $order->load('items'),
//                 ], 201);
//             });

//         } catch (\Illuminate\Validation\ValidationException $e) {
//             return response()->json(['errors' => $e->errors()], 422);
//         } catch (\Exception $e) {
//             Log::error('Order creation error: ' . $e->getMessage());
//             return response()->json(['error' => $e->getMessage()], 500);
//         }
//     }

//     // PUT /api/admin/orders/{id}/status
//     public function updateStatus(Request $request, $id)
//     {
//         try {
//             $validated = $request->validate([
//                 'status' => 'required|string|in:Processing,Shipped,Delivered,Cancelled',
//             ]);

//             $order = Order::findOrFail($id);
//             $order->update(['status' => $validated['status']]);

//             return response()->json([
//                 'success' => true,
//                 'message' => 'Order status updated',
//                 'order' => $order,
//             ]);
//         } catch (\Exception $e) {
//             Log::error('Order status update error: ' . $e->getMessage());
//             return response()->json(['error' => $e->getMessage()], 500);
//         }
//     }

//     // DELETE /api/admin/orders/{id}
//     public function destroy($id)
//     {
//         try {
//             $order = Order::findOrFail($id);
//             $order->delete();
//             return response()->json(['message' => 'Order deleted successfully']);
//         } catch (\Exception $e) {
//             Log::error('Order delete error: ' . $e->getMessage());
//             return response()->json(['error' => $e->getMessage()], 500);
//         }
//     }
// }

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class OrdersController extends Controller
{
    // Generate unique order ID
    private function generateOrderId(): string
    {
        return 'ORD-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }

    // GET /api/admin/orders
    public function index()
    {
        try {
            $orders = Order::with('items')->orderBy('created_at', 'desc')->get();
            return response()->json($orders);
        } catch (\Exception $e) {
            Log::error('Orders index error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // GET /api/admin/orders/{id}
    public function show($id)
    {
        try {
            $order = Order::with('items')->findOrFail($id);
            return response()->json($order);
        } catch (\Exception $e) {
            Log::error('Orders show error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // POST /api/admin/orders
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'nullable|integer|exists:users,id',
                'customer_name' => 'required|string|max:255',
                'customer_email' => 'required|email|max:255',
                'customer_phone' => 'nullable|string|max:20',
                'shipping_address' => 'required|string',
                'city' => 'required|string|max:100',
                'country' => 'nullable|string|max:100',
                'zip_code' => 'nullable|string|max:20',
                'payment_method' => 'required|string|in:card,paypal,cod',
                'subtotal' => 'required|numeric|min:0',
                'shipping' => 'required|numeric|min:0',
                'discount' => 'nullable|numeric|min:0',
                'tax' => 'nullable|numeric|min:0',
                'total' => 'required|numeric|min:0',
                'coupon_code' => 'nullable|string|max:50',
                'coupon_discount' => 'nullable|numeric|min:0',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'nullable|integer|exists:products,id',
                'items.*.product_name' => 'required|string|max:255',
                'items.*.product_sku' => 'nullable|string|max:100',
                'items.*.product_image' => 'nullable|string',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.total' => 'required|numeric|min:0',
            ]);

            return DB::transaction(function () use ($validated) {
                $order = Order::create([
                    'order_id' => $this->generateOrderId(),
                    'user_id' => $validated['user_id'] ?? auth()->id(),
                    'customer_name' => $validated['customer_name'],
                    'customer_email' => $validated['customer_email'],
                    'customer_phone' => $validated['customer_phone'] ?? null,
                    'shipping_address' => $validated['shipping_address'],
                    'city' => $validated['city'],
                    'country' => $validated['country'] ?? 'USA',
                    'zip_code' => $validated['zip_code'] ?? null,
                    'payment_method' => $validated['payment_method'],
                    'payment_status' => $validated['payment_method'] === 'cod' ? 'pending' : 'paid',
                    'subtotal' => $validated['subtotal'],
                    'shipping' => $validated['shipping'],
                    'discount' => $validated['discount'] ?? 0,
                    'tax' => $validated['tax'] ?? 0,
                    'total' => $validated['total'],
                    'coupon_code' => $validated['coupon_code'] ?? null,
                    'coupon_discount' => $validated['coupon_discount'] ?? 0,
                    'status' => 'Processing',
                    'paid_at' => $validated['payment_method'] === 'cod' ? null : now(),
                ]);

                foreach ($validated['items'] as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'] ?? null,
                        'product_name' => $item['product_name'],
                        'product_sku' => $item['product_sku'] ?? null,
                        'product_image' => $item['product_image'] ?? null,
                        'price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'total' => $item['total'],
                    ]);

                    if (!empty($item['product_id'])) {
                        $product = Product::find($item['product_id']);
                        if ($product && $product->stock !== null) {
                            $product->decrement('stock', $item['quantity']);
                            if ($product->stock <= 0) {
                                $product->update(['status' => 'out of stock']);
                            } elseif ($product->stock <= 5) {
                                $product->update(['status' => 'low stock']);
                            }
                        }
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Order created successfully',
                    'order' => $order->load('items'),
                ], 201);
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Order creation error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'status' => 'sometimes|required|string|in:Processing,Shipped,Delivered,Cancelled',
                'payment_status' => 'sometimes|required|string|in:pending,paid,failed,refunded',
                'notes' => 'nullable|string',
            ]);

            $order = Order::findOrFail($id);
            $order->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully',
                'order' => $order->fresh(),
            ]);
        } catch (\Exception $e) {
            Log::error('Order update error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // PUT /api/admin/orders/{id}/status (alternative endpoint)
    public function updateStatus(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|string|in:Processing,Shipped,Delivered,Cancelled',
            ]);

            $order = Order::findOrFail($id);
            $order->update(['status' => $validated['status']]);

            return response()->json([
                'success' => true,
                'message' => 'Order status updated',
                'order' => $order,
            ]);
        } catch (\Exception $e) {
            Log::error('Order status update error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // DELETE /api/admin/orders/{id}
    public function destroy($id)
    {
        try {
            $order = Order::findOrFail($id);
            $order->delete();
            return response()->json(['message' => 'Order deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Order delete error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}