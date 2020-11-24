<?php 

namespace App\Services;
use App\Traits\ConsumesExternalServices;

class R2SService{

    use ConsumesExternalServices;

    protected $baseUri;
    protected $api_key;
    
    public function __construct(){
        $this->baseUri = config('services.r2s.base_uri');
        $this->api_key = config('services.r2s.api_secure');
    }

    public function createWaybill($user,$order,$pud="PUD",$amount=0,$payment_mode="Cash"){
        $formParams = [
            "waybillRequestData" => [
                "FromOU" => "",
                "WaybillNumber"=> "",
                "DeliveryDate"=> "",
                "ClientCode"=> "1788", // 1788
                "CustomerCode"=> "1788", // 1788
                "ConsigneeCode"=> "00000",
                "ConsigneeAddress"=>  $user->userAddress === null || $user->userAddress->address===null ? 'NA' : $user->userAddress->address, 
                "ConsigneeCountry"=> "EG",
                "ConsigneeState"=> $order->city,
                "ConsigneeCity"=>  $order->region,
                "ConsigneeName"=> $user->name,
                "ConsigneePhone"=> $user->phone,
                "NumberOfPackages"=> "1",
                "ActualWeight"=> "2",
                "ChargedWeight"=> "",
                "CargoValue"=> "1",
                "PaymentMode"=> "TBB",
                "ServiceCode"=> $pud, // PUD form get product from our warehouses ,  DROPD , DROPDCC gives R2S   , PUDCC
                "WeightUnitType"=> "KILOGRAM",
                "Description"=>"-",
                "ReferenceNumber"=> "-",
                "COD"=> $amount,
                "CODPaymentMode"=> $payment_mode, // 
                "CreateWaybillWithoutStock"=>"false"
            ]
        ];
        $response = $this->makeRequest(
            'POST',
            'webservice/v2/CreateWaybill',
            [
                'secureKey' => $this->api_key
            ],
            $formParams,
            [
            'Content-Type' => 'application/json',
            'accessKey'    => 'logixerp'
            ],
            $isJsonReuest =  true
        );
     
        if($response->messageType === 'Success'){
            return $response;
        }else{
            return null;
        }
        
    }
    
    public function decodeResponse($response){
        return json_decode($response);
    }
}


?>