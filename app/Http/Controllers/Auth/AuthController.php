<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Traits\ApiResponser;
use App\Transformers\User\UserAddressTransfor;
use App\User;
use App\UserAddress;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AuthController extends ApiController
{
    use ApiResponser;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','registeration','socialRegisteration','socialLogin']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
       
        $credentials = request(['email', 'password']);
        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user= auth()->user();

        $api_token= "";
        if(request()->has('api_token') && request()->has('api_token') !== null){
            $api_token = request()->get('api_token');
            $user->api_token = $api_token; 
            $user->save();
        }else{
            $api_token  = $user->api_token ; 
            $user->save();
        }
        return $this->respondWithToken($token , $api_token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return $this->showOne(auth()->user());
        //return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $user=auth()->user();
        $api_token= "";
        if(request()->has('api_token') && request()->has('api_token') !== null){
            $api_token = request()->get('api_token');
            $user->api_token = $api_token; 
            $user->save();
        }else{
            $api_token  = $user->api_token ; 
            $user->save();
        }
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        $user = auth()->user();
       
        if($user->userAddress !== null){
            $shipping_address = $this->transformData($user->userAddress,UserAddressTransfor::class);
        }else{
            $shipping_address = null;
        }
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,   
            'id'   => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'admin' =>(int)$user->admin,
            'avatar' => $user->avatar ? $user->avatar : '' ,
            'notification' => (int) $user->notification,
            'is_shipping' => (bool)$user->is_shipping,
            'shipping_address' => $shipping_address,
            'phone'        =>  $user->phone
        ]);      
    }

    public function registeration(Request $request)
    {
        $rules = [
            "name"         => "required",
            "email"        => "required|email|unique:users,email",
            "phone"        => "required|numeric",
            "password"     => "required|confirmed",
            "address"      => "required",
            "city"         => "required",
            "district"     => "required"
        ];

        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return $this->returnValidationError($validator);
        }
        $user = $this->add_user($request->name,$request->email,$request->password,$request->phone,$request->address,$request->city,$request->district);
        if($user){
            $credentials = request(['email', 'password']);
            if (!$token = auth()->attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
            return $this->respondWithToken($token , '');
            return $this->getMessage("{$user->name} has been added successfully",200);
        }
        return $this->getMessage('something goes wrong , try again',404);
    }
    
    public function socialRegisteration(Request $request){
        try{
            
            $user = User::where('email',$request->email)->first();
            $password = "faceLogin";
            if($user === null){
                $user= new User();
                $user->email = $request->email;
                $user->name = $request->name;
                $user->password = $password;
                $user->save();
            }
            $credentials =[
                'email' => $request->email,
                'password' => $password
            ];
            if (!$token = auth()->attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            $user= auth()->user();
            $api_token= "";
            return $this->respondWithToken($token , $api_token);

        }catch(\Exception $ex){
            return $this->getMessage('something goes wrong , try again',404);
        }
       
    }

    public function socialLogin(Request $request){

        $rules = [
            "email"        => "required|email|exists:users,email",
        ];

        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return $this->returnValidationError($validator);
        }

        $credentials =[
            'email' => $request->email,
            'password' => 'faceLogin'
        ];
        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user= auth()->user();

        $api_token= "";
        if(request()->has('api_token') && request()->has('api_token') !== null){
            $api_token = request()->get('api_token');
            $user->api_token = $api_token; 
            $user->save();
        }else{
            $api_token  = $user->api_token ; 
            $user->save();
        }
        return $this->respondWithToken($token , $api_token);
    }

    protected function add_user($name,$email,$password,$phone,$user_address,$city,$district){
        try{
            DB::beginTransaction();
            $user= New User();
            $user->name=$name;
            $user->email = $email;
            $user->password=$password;
            $user->phone = $phone;
            $user->save();
            
            $address = new UserAddress();
            $address->user_id = $user->id;
           
            $address->address = $user_address;
            $address->city = $city;
            $address->region = $district;
            $address->save();
            DB::commit();
            return $user;
        }catch(Exception $ex){
           
            DB::rollback();
            dd($ex);
            return false;
        }
       
    }
}
