<?php

namespace App\Http\Controllers;

use App\Enums\UserPermission;
use App\Http\Requests\Order\AcceptOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use App\Enums\UserRole;
use App\Services\AuthService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected $orderService;
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }
    /**
     * Display a listing of the resource.
     */
    /**
     * all orders
     * @return response  of the status of operation : orders
     */
    public function index()
    {
        $orders = $this->orderService->allOrders();

        return response()->json([
            'status' => 'success',
            'data' => [
                'orders' => $orders
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */

    /**
     * get a order
     * @param Order $order
     * @return response  of the status of operation : order
     */
    public function show(Order $order)
    {
        $order = $this->orderService->oneOrder($order);

        return response()->json([
            'status' => 'success',
            'data' => [
                'order' => $order
            ]
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */

    /**
     * delete  a order
     * @param Order $order
     * @return response  of the status of operation 
     */
    public function destroy(Order $order)
    {


        AuthService::canDo(UserPermission::DELETE_ORDER->value);
        Gate::authorize('delete-order', [Auth::user(), $order]);
        $this->orderService->deleteOrder($order);

        return response()->json(status: 204);
    }
    /**
     * confirm  a order
     * @return response  of the status of operation  : order
     */
    public function confirm()
    {
        AuthService::canDo(UserPermission::CONFIRM_ORDER->value);
        $order = $this->orderService->confirmOrder();
        return response()->json([
            'status' => 'success',
            'data' => [
                'order' => $order
            ]
        ], 200);
    }

    public function accept(AcceptOrderRequest $request, Order $order)
    {
        if (AuthService::user_role(Auth::user()->id) == UserRole::SALES_MANAGER->value) {
            AuthService::canDo(UserPermission::ACCEPT_ORDER->value);
        }
        $orderData = $request->validated();
        $order = $this->orderService->acceptOrder($order, $orderData);
        return response()->json([
            'status' => 'success',
            'data' => [
                'order' => $order
            ]
        ], 200);
    }
    /**
     * reject  a order
     * @param  Order $order
     * @return response  of the status of operation  : order
     */
    public function reject(Order $order)
    {
        if (AuthService::user_role(Auth::user()->id) == UserRole::SALES_MANAGER->value) {
            AuthService::canDo(UserPermission::REJECT_ORDER->value);
        }
        $order = $this->orderService->rejectOrder($order);
        return response()->json([
            'status' => 'success',
            'data' => [
                'order' => $order
            ]
        ], 200);
    }
    /**
     * start  a order
     * @param  Order $order
     * @return response  of the status of operation  : order
     */
    public function start(Order $order)
    {
        AuthService::canDo(UserPermission::START_ORDER->value);
        Gate::allows('start-order', [Auth::user(), $order]);

        $order = $this->orderService->startOrder($order);
        return response()->json([
            'status' => 'success',
            'data' => [
                'order' => $order
            ]
        ], 200);
    }

    /**
     * end  a order
     * @param  Order $order
     * @return response  of the status of operation  : order
     */
    public function end(Order $order)
    {
        AuthService::canDo(UserPermission::END_ORDER->value);
        Gate::allows('end-order', [Auth::user(), $order]);

        $order = $this->orderService->endOrder($order);
        return response()->json([
            'status' => 'success',
            'data' => [
                'order' => $order
            ]
        ], 200);
    }
}
