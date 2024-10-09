<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Http\Resources\OrderDetailsResource;
use App\Http\Resources\OrderResource;
use App\Models\Book;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetails;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\Role;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class OrderService
{
    /**
     * show all  orders
     * @return OrderResource $orders 
     */
    public function allOrders()
    {
        try {
            if (AuthService::user_role(Auth::user()->id) == UserRole::DELIVERY->value) {
                $orders = Order::with('customer')
                    ->myOrder(Auth::user()->id)->get();
            } else {
                $orders = Order::with('delivery')->get();
            }
            $orders = OrderResource::collection($orders);
            return $orders;
        } catch (Exception $e) {
            Log::error("error in get all orders"  . $e->getMessage());
            throw new HttpResponseException(response()->json(
                [
                    'status' => 'error',
                    'message' => "there is something wrong in server",
                ],
                500
            ));
        }
    }
    /**
     * get one order and all  $order_details 
     * @return array  OrderResource $order and  OrderDetailsResource $order_details 
     */
    public function oneOrder($order)
    {
        try {
            $order = $order->load(['order_details.book', 'delivery', 'customer']);
            $order = OrderResource::make($order);
            $order_details = OrderDetailsResource::collection($order->order_details);
            return [
                'order' => $order,
                'order_details' => $order_details
            ];
        } catch (Exception $e) {
            Log::error("error in  show a order"  . $e->getMessage());
            throw new HttpResponseException(response()->json(
                [
                    'status' => 'error',
                    'message' => "there is something wrong in server",
                ],
                500
            ));
        }
    }
    /**
     * confirm order 
     * @return OrderResource order  
     */
    public function confirmOrder()
    {
        $carts = Cart::myCart(Auth::user()->id)->with('book')->get();
        if ($carts->isEmpty()) {
            throw new HttpResponseException(response()->json(
                [
                    'status' => 'error',
                    'message' => "لا يوجد لديك كتب لشراءها",
                ],
                422
            ));
        } else {
            $orderPrice = 0;
            foreach ($carts as $cart) {
                $book = $cart->book;
                if ($book->count < $cart->count) {
                    $message = "لا يمكنك شراء هذه الكمية من الكتاب " . $book->name . "الكمية المتوفرة هي " . $book->count;
                    throw new HttpResponseException(response()->json(
                        [
                            'status' => 'error',
                            'message' => $message,
                        ],
                        422
                    ));
                } else {
                    $orderPrice += ($book->price * $cart->count);
                }
            }
            try {
                $order = Order::create([
                    'customer_id' => Auth::user()->id,
                    'price' => $orderPrice,
                ]);
                foreach ($carts as $cart) {
                    OrderDetails::create([
                        'order_id' => $order->id,
                        'count' => $cart->count,
                        'price' => $cart->book->price,
                        'book_id'  => $cart->book->id,
                    ]);
                    $book = Book::find($cart->book->id);
                    $book->update([
                        'count' => $book->count - $cart->count,
                    ]);
                    $cart->delete();
                }
                $order = OrderResource::make($order);
                return $order;
            } catch (Exception $e) {
                Log::error("error in confirm a order"  . $e->getMessage());
                throw new HttpResponseException(response()->json(
                    [
                        'status' => 'error',
                        'message' => "there is something wrong in server",
                    ],
                    500
                ));
            }
        }
    }

    public function deleteOrder($order)
    {
        if ($order->status == OrderStatus::START_DELIVERY->value) {
            throw new HttpResponseException(response()->json(
                [
                    'status' => 'error',
                    'message' => "لا يمكنك حذف هذه الطلبية لقد تم بدء توصيلها",
                ],
                422
            ));
        } else if ($order->status == OrderStatus::DELIVERED->value || $order->status == OrderStatus::REJECTED->value) {
            try {
                $order->delete();
            } catch (Exception $e) {
                Log::error("error in create a  role"  . $e->getMessage());
                throw new HttpResponseException(response()->json(
                    [
                        'status' => 'error',
                        'message' => "there is something wrong in server",
                    ],
                    500
                ));
            }
        } else {
            try {
                $order_details = OrderDetails::where('order_id', '=', $order->id)->get();
                foreach ($order_details as $o) {
                    $book = Book::find($o->book_id);
                    $book->update([
                        'count' => $book->count + $o->count,
                    ]);
                    $o->delete();
                }
                $order->delete();
            } catch (Exception $e) {
                Log::error("error in delete order"  . $e->getMessage());
                throw new HttpResponseException(response()->json(
                    [
                        'status' => 'error',
                        'message' => "there is something wrong in server",
                    ],
                    500
                ));
            }
        }
    }

    /**
     * accept order 
     * @param Order $order  
     * @param  array $orderData  
     * @return OrderResource order  
     */
    public function acceptOrder(Order $order, $orderData)
    {
        if ($order->status != OrderStatus::PINDING->value) {
            throw new HttpResponseException(response()->json(
                [
                    'status' => 'error',
                    'message' => "لا يمكنك موافقة على هذه الطلبية لقد تمت معالجتها سابقا",
                ],
                422
            ));
        }
        try {
            $order->status = OrderStatus::ACCEPTED->value;
            $order->delivery_id = $orderData['delivery_id'];
            $order->delivery_date = $orderData['delivery_date'];
            $order->save();
            $order->load('delivery');
            $order = OrderResource::make($order);
            return $order;
        } catch (Exception $e) {
            Log::error("error in accept  order "  . $e->getMessage());
            throw new HttpResponseException(response()->json(
                [
                    'status' => 'error',
                    'message' => "there is something wrong in server",
                ],
                500
            ));
        }
    }
    /**
     * reject order 
     * @param Order $order  
     * @return OrderResource order  
     */
    public function rejectOrder(Order $order)
    {
        if ($order->status != OrderStatus::PINDING->value) {
            throw new HttpResponseException(response()->json(
                [
                    'status' => 'error',
                    'message' => "لا يمكنك رفض هذه الطلبية لقد تمت معالجتها سابقا",
                ],
                422
            ));
        }
        try {

            $order->status = OrderStatus::REJECTED->value;
            $order->save();

            $order_details = OrderDetails::where('order_id', '=', $order->id)->get();
            foreach ($order_details as $o) {
                $book = Book::find($o->book_id);
                $book->update([
                    'count' => $book->count + $o->count,
                ]);
                $o->delete();
            }
            $order = OrderResource::make($order);
            return $order;
        } catch (Exception $e) {
            Log::error("error in  reject order"  . $e->getMessage());
            throw new HttpResponseException(response()->json(
                [
                    'status' => 'error',
                    'message' => "there is something wrong in server",
                ],
                500
            ));
        }
    }
    /**
     * start  order 
     * @param Order $order  
     * @return OrderResource order  
     */
    public function startOrder(Order $order)
    {
        if ($order->status != OrderStatus::ACCEPTED->value) {
            throw new HttpResponseException(response()->json(
                [
                    'status' => 'error',
                    'message' => "لا يمكنك يدء تسليم  هذه الطلبية لقد تمت معالجتها سابقا",
                ],
                422
            ));
        }
        try {
            $order->status = OrderStatus::START_DELIVERY->value;
            $order->save();
            $order->load('delivery');
            $order = OrderResource::make($order);
            return $order;
        } catch (Exception $e) {
            Log::error("error in  start  order"  . $e->getMessage());
            throw new HttpResponseException(response()->json(
                [
                    'status' => 'error',
                    'message' => "there is something wrong in server",
                ],
                500
            ));
        }
    }
    /**
     * end  order 
     * @param Order $order  
     * @return OrderResource order  
     */
    public function endOrder(Order $order)
    {
        if ($order->status != OrderStatus::START_DELIVERY->value) {
            throw new HttpResponseException(response()->json(
                [
                    'status' => 'error',
                    'message' => "لا يمكنك انهاء تسليم  هذه الطلبية لقد تمت معالجتها سابقا",
                ],
                422
            ));
        }
        try {
            $order->status = OrderStatus::DELIVERED->value;
            $order->save();
            $order->load('delivery');
            $order = OrderResource::make($order);
            return $order;
        } catch (Exception $e) {
            Log::error("error in  soft delete a  order"  . $e->getMessage());
            throw new HttpResponseException(response()->json(
                [
                    'status' => 'error',
                    'message' => "there is something wrong in server",
                ],
                500
            ));
        }
    }
}
