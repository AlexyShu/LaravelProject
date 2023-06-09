<?php

namespace App\Http\Controllers;

use App\Mail\OrderCompleted;
use App\Models\Good;
use App\Models\Order;
use App\Models\OrderGood;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function buy(int $id)
    {
        $good = Good::query()->find($id);

        if (!$good) {
            return redirect()->route('home');
        }

        $currentOrder = Order::getCurrentOrder(Auth::id());

        if (!$currentOrder) {
            ($currentOrder = new Order([
                'user_id' => Auth::id(),
                'state' => Order::STATE_CURRENT
            ]))->save();
        }

        (new OrderGood(['order_id' => $currentOrder->id, 'good_id' => $id]))->save();

        return redirect()->route('order.current');
    }

    public function current()
    {
        $order = Order::getCurrentOrder(Auth::id());

        return view('order.current', [
            'goods' => $order->goods ?? [],
            'sum' => $order ? $order->getSum() : 0
        ]);
    }

    public function process()
    {
        $currentOrder = Order::getCurrentOrder(Auth::id());

        if (!$currentOrder) {
            return redirect()->route('home');
        }

        Mail::to(User::query()->find(1))->send(new OrderCompleted($currentOrder, Auth::user()));

        /** @var Order $currentOrder */
        $currentOrder->saveAsProcessed();

        return view('order.completed');

    }
}
