<?php

namespace App\Exports;

use App\Order;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class DayOrder implements FromView
{

    use Exportable;

    protected $date ;

    public function __construct(string $date)
    {
        $this->date = $date;
    }

      /**
    * @return \Illuminate\Support\Collection
    */
    public function view() : view
    {
        $next = date('Y-m-d', strtotime("+1 day", strtotime($this->date)));
        return view('exports.orders', [
            'orders' => Order::where('created_at','>=',$this->date)
                ->where('created_at','<',$next)->get()
        ]);
    }

}
