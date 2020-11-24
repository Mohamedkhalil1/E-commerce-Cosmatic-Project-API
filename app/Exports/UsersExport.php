<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

use PhpOffice\PhpSpreadsheet\Shared\Date;

class UsersExport implements FromQuery,WithMapping,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        return User::where('id', '>',3);
    }

    public function map($user): array
    {
        return [
            $user->name,
            $user->email,    
            $user->created_at,
        ];
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Date',
        ];
    }
}
