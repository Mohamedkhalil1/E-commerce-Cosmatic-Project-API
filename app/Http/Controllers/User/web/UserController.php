<?php

namespace App\Http\Controllers\User\web;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Mail\UserCreated;
use App\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UserController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return $this->showAll($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = $this->add_user($request->name,$request->email,$request->password);
        if($user){
            return $this->getMessage("{$user->name} has been added successfully",200);
        }
        return $this->getMessage('something goes wrong , try again');
    }

    public function store_group(Request $request)
    {
        set_time_limit(0);
        $spreadsheet = IOFactory::load($request->file('users'));
        $worksheet   = $spreadsheet->getActiveSheet();
        $rows        = [];
        foreach ($worksheet->getRowIterator() AS $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE);
            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }
            $rows[] = $cells;
        }
        
        $titles    = [
            'Name','Email','password'
        ];
        $headers   = array_filter(array_shift($rows));
        $tmp = [];
        foreach ($rows as $k => $row) {
            foreach ($headers as $kk => $header) {
                $header = str_replace(' ', '', $header);
                $tmp[$k][$header] = $row[$kk];
            }
        }
        $index = 0;
        foreach ($tmp as $k => $v) {
            $user = new User();
            if(isset($v['Email'])){
                $user_exist = User::where('email',(string)$v['Email'])->get()->first();
                if($user_exist !== null)
                {
                    continue;
                }else{
                    $user->email= (string)$v['Email'];
                    $user->name = (string)$v['Email'];
                    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    $pin = mt_rand(1000000, 9999999)
                    . mt_rand(1000000, 9999999)
                    . $characters[rand(0, strlen($characters) - 1)];
                    $user->password = $pin;
                    $user->block = 0;
                    $user->pocket_money=5000;
                }
            }else{
                continue;
            }
            $user->save();
            $index++;
        }
        dd($index);
        return $this->getMessage('Success',200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        return $this->showOne($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if($user->delete()){
            return $this->getMessage("{$user->name} has been deleted.",200);
        }
        return $this->getMessage('something goes wrong, try again',409);
    }

    protected function add_user($name,$email,$password,$parent_id=null,$is_new=0){
        $user= New User();
        $user->name=$name;
        $user->email = $email;
        $user->password=$password;
        $user->parent_id = $parent_id;
        if($user->save()){
            return $user;
        }
        return false;
    }
}
