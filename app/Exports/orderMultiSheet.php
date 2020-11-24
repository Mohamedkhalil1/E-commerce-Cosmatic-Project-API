<?php

namespace App\Exports;

use App\Order;
use App\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class orderMultiSheet implements WithMultipleSheets
{
    use Exportable;

    
    public function __construct()
    {
    }

    public function sheets(): array
    {
        $sheets = [];
        $array_ids=[];
       /* for($index =0 ; $index <= 1000 ;$index++){
            array_push($array_ids,$index);
        }*/
        $users = User::all();
        foreach($users as $user){
            $sheets[] = new FamilyOrders($user);
        }
        return $sheets;
    }
}
