<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;


class UsersFamilyOrders implements WithMultipleSheets
{
    use Exportable;
    public function sheets(): array
    {
        $sheets = [];
        $users = User::where('parent_id',null)->get();
        foreach($users as $user) {
            $sheets[] = new FamilyOrders($user);
        }
        return $sheets;
    }
}
