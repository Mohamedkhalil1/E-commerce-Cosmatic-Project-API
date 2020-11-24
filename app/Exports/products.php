<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Product;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class products implements FromView
{
     /**
    * @return \Illuminate\Support\Collection
    */
    public function view() : view
    {
        $products = Product::where('available_for_family',1)->get();
        return view('exports.products', [
            'products' => $products
        ]);
    }
}
