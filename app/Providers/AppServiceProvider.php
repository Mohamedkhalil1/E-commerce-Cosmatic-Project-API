<?php

namespace App\Providers;

use App\Card;
use App\Mail\UserCreated;
use App\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        User::created(function($user){
            $user->password = bcrypt($user->password);
            $user->save();
            $card = new Card();
            $card->user_id = $user->id;
            $card->save();

        });
    }
    protected function add_user($name,$email,$password){
        $user= New User();
        $user->name=$name;
        $user->email = $email;
        $user->password=$password;
        $user->block = 0;
        if($user->save()){
            return $user;
        }
        return false;
    }
}
