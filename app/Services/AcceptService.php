<?php 

namespace App\Services;
use App\Traits\ConsumesExternalServices;

class AcceptService{

    use ConsumesExternalServices;

    protected $baseUri;
    protected $api_key;
    
    public function __construct(){
        $this->baseUri = config('services.accept.base_uri');
        $this->api_key = config('services.accept.api_key');
    }

    public function getToken(){
        $response = $this->makeRequest(
            'POST',
            'api/auth/tokens',
            [],
            [
            "api_key" => $this->api_key
            ],
            [
            'Content-Type' => 'application/json'
            ],
            $isJsonReuest =  true
        );

        return $response->token;
    }


    public function createOrder($token,$amount ,$order_id, $currency = 'EGP' , $items=[]){
       
        return $this->makeRequest(
            'POST',
            'api/ecommerce/orders',
            [],
            [
            "auth_token" => $token,
            "delivery_needed" =>  "true",
            "merchant_id" => "23945",
            "amount_cents" => (int)$amount *100,
            "currency" => $currency,
            "items" => []
            ],
            [],
            $isJsonReuest =  true
        );
    }

    public function CardPaymentKey($user,$token,$amount,$order_id,$integration_id,$lock_order_when_paid=true,$expiration=3600,$currency="EGP"){ 
        $billing_data = [ 
            "apartment" => "NA", 
            "email"=> $user->email, 
            "floor"=> "NA", 
            "first_name"=> $user->name, 
            "street"=> "NA", 
            "building"=> "NA", 
            "phone_number"=>"+2".$user->phone, 
            "shipping_method"=> "NA", 
            "postal_code"=>"NA", 
            "city"=> $user->userAddress === null || $user->userAddress->city===null ? 'NA' : $user->userAddress->city, 
            "country"=> "egypt", 
            "last_name"=> $user->name, 
            "state"=>  $user->userAddress === null||$user->userAddress->region===null ?  'NA' :$user->userAddress->region
        ];

        //dd($billing_data)
        
        return $this->makeRequest(
            'POST',
            'api/acceptance/payment_keys',
            [],
            [
                "auth_token" => $token,
                "amount_cents"=> (int)$amount * 100, 
                "expiration" => $expiration, 
                "order_id" => $order_id,
                "billing_data"=> $billing_data, 
                "currency"=> $currency, 
                "integration_id"=>$integration_id,
                "lock_order_when_paid"=> $lock_order_when_paid
            ],
            [],
            $isJsonReuest = true
        );
    }

    public function handlePayment($order,$user,$integration_id){
            $token = $this->getToken();
            $payment_order = $this->createOrder($token,$order->amount,$order->id,'EGP',$order->products);
            $response = $this->CardPaymentKey($user,$token,$order->amount,$payment_order->id,$integration_id);
        $order->num =$payment_order->id;
        return $response->token;
    }
    
    public function cardPay($payment_token,$iframe_id){
        return $this->makeRequest(
            'GET',
            'api/acceptance/iframes/'.$iframe_id,
            [
                "payment_token" => $payment_token,
            ],
            [],
            [],
            $isJsonReuest=true
        );
    }

    public function kioskPay($payment_token){
        $source = [
            "identifier" => "AGGREGATOR", 
            "subtype" => "AGGREGATOR"
        ];
        return $this->makeRequest(
            'POST',
            'api/acceptance/payments/pay',
            [],
            [
                "source" => $source,
                "payment_token" => $payment_token
            ],
            [],
            $isJsonReuest=true
        );
    }


    public function mobileWalletsPay($payment_token,$phone){
    
        $source = [
            "identifier" => $phone, 
            "subtype" => "WALLET"
        ];
        
        return $this->makeRequest(
            'POST',
            'api/acceptance/payments/pay',
            [],
            [
                "source" => $source,
                "payment_token" => $payment_token
            ],
            [],
            $isJsonReuest=true
        );
    }
   

    public function decodeResponse($response){
        return json_decode($response);
    }
    
}


?>