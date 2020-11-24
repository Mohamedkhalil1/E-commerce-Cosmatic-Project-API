<?php

namespace App\Exports;

use App\Order;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class OrdersExport implements FromQuery,WithMapping,WithHeadings,WithColumnFormatting
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
       /* $today = Carbon::now();
        $month = $today->month; // retrieve the month
        $year  = $today->year; // retrieve the year of the date
      
        return Order::whereMonth('created_at',$month)
        ->whereYear('created_at',$year);*/
        $now = Carbon::now();
        $start = $now->startOfWeek(Carbon::SATURDAY)->format('Y-m-d');
        $end = $now->endOfWeek(Carbon::FRIDAY)->format('Y-m-d');

        return Order::where('created_at','>=',$start)
        ->where('created_at','<=',$end);
    }

    public function map($order): array
    {
        return [
            $order->id,
            $order->user ? $order->user->name : '',
            $order->amount,    
            Date::dateTimeToExcel($order->created_at),
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'staff',
            'amount',
            'date'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
            'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
