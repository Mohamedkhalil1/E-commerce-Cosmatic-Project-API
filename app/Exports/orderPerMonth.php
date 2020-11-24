<?php

namespace App\Exports;

use App\Order;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class orderPerMonth implements FromView
{
    private $month;
    private $year;

    public function __construct(int $year, int $month)
    {
        $this->month = $month;
        $this->year  = $year;
    }

    public function view(): View
    {
        $orders = Order::whereYear('created_at', $this->year)
        ->whereMonth('created_at', $this->month)->get();
        return view('exports.orders', [
            'orders' => $orders
        ]);
    }
}
