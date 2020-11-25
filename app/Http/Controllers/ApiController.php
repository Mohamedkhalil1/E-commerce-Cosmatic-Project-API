<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;


class ApiController extends Controller
{
    use ApiResponser;
    
    protected $map = [
        'Shipment Received' => 'Accepted',
        'At Warehouse'      => 'Prepared',
        'Out For Delivery'  => 'Shipped',
        'Delivered'         => 'Delivered',
        'Cancelled'         => 'Cancelled'
    ];

    protected $color = [
        'white' => '#FFF',
        'dark blue' => '#00008B',
        'gray'  => '#808080',
        'black'         => '#000',
        'indigo'         => '#4b0082',
        'blue'       => '#0000FF',
        'red'       => '#FF0000',
        'yellow'    =>'#FFFF00',
        'pink'     => '#FFC0CB',
        'fuchsia' => '#FF00FF',
        'rose' => '#ff007f',
        'purple'=>'#800080',
        'purble'=>'#800080',
        'beige' => '#F5F5DC',
        'baby blue' =>'#89CFF0' ,
        'silver' =>'#C0C0C0',
        'cool intentions' => '#6B3330',
        'camel' => '#c19a6b',
        'burgundy' => '#800020',
        'mint' =>'#98ff98',
        'orange' => '#FFA500',
        'green' => '#008000',
        'navy' => '#000080',
        'jeans' =>'#1560bd', 
        'royal blue' => '#002366',
        'stripe blue'=>'#008cdd',
        'ombre' =>'#a86e64',
        'light pink' =>'#FFB6C1',
        'moody' => '#a67173',
        'aloe vera' =>'#759966',
        'brown'=>'#A52A2A',
        'grey'=>'#808080',
        'denim' => '#1560bd',
        'dark simon'=>'#e9967a',
        'turquoise' => '#40E0D0',
        'sky' => '87ceeb',
        'alabaster'=>'#f2f0e6',
        'pink porcelaine' => '#F9CED8',
        'marble' =>'#E6E4D8',
        'ivory beige' => '#FFFFF0',
        'soft sand' => '#edc9af',
        'beige nude' => '#E3BC9A',
        'pink beige' => '#b39283',
        'sand' => '#c2b280',
        'warm ivory'=>'#efebd8',
        'dark sand' => '#a88f59',
        'natural beige' => '#f5f5dc',
        'desert' => '#edc9af',
        'golden sand' => '#F2D16B',
        'golden honey' => '#ebaf4c',
        'golden beige' => '#a7825d',
        'almond' => '#efdecd',
        'amber' => '#ffbf00',
        'neutral beige' => '#f5f5dc',
        'pearl' =>'#eae0c8',
        'nude ivory'=> '#fff4c6',
        'cinnamon' => '#d2691e',
        'sand beige' => '#a0a494',
        'macadamia' => '#d5c6ac',
        'apricot beige' => '#fbceb1',
        'caramel' => '#FFD59A',
        'honey' => '#8b6647',
        'hazelut'=>'b8a894',
        'praline'=>'#f09816',
        'chestnut' => '#954535',

    ];

    protected $colorName = [
         '#FFF' => 'white',
         '#00008B'=>'dark blue',
         '#808080' => 'gray',
         '#000'    => 'black',
         '#4b0082'=> 'indigo'  ,
         '#0000FF'       => 'blue',
         '#FF0000'  => 'red',
         '#FFFF00'  =>'yellow',
         '#FFC0CB'=> 'pink',
         '#FF00FF' => 'fuchsia',
         '#ff007f'=>  'rose',
         '#800080'=> 'purple',
         '#F5F5DC'=> 'beige',
         '#89CFF0' =>'baby blue' ,
         '#C0C0C0' =>'silver',
         '#6B3330' =>'cool intentions' ,
         '#c19a6b' =>'camel' ,
         '#800020' =>'burgundy' ,
         '#98ff98'=>'mint',
         '#FFA500' => 'orange',
         '#008000'=> 'green',
         '#000080' =>'navy' ,
         '#1560bd' =>'jeans', 
         '#002366' =>'royal blue' ,
         '#008cdd'=>'stripe blue',
         '#a86e64' =>'ombre',
         '#FFB6C1' =>'light pink',
         '#a67173' =>'moody',
         '#759966' =>'aloe vera',
         '#A52A2A'=>'brown',
         '#808080'=>'grey'
    ];

    protected function generate_token($user_id){
        $token = md5($user_id.'_'. microtime());
        return $token;
    }
    
    public function notification($token,$title)
    {   
        
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
        $token=$token;
        

        $notification = [
            'body' => $title,
            'sound' => true,
        ];
        
        $extraNotificationData = ["message" => $notification,"moredata" =>'dd'];

        $fcmNotification = [
            //'registration_ids' => $tokenList, //multple token array
            'to'        => $token, //single token
            'notification' => $notification,
            'data' => $extraNotificationData
        ];

        $headers = [
            'Authorization: key=AAAAq_z2ruo:APA91bHlPrQ1t6dDbbP4GpJnwpPPNDJMs1I3W8KrULFIxteXpg0XNwyH2F9-TIei3Cu7mMsrJSL2Wk-3A82SprFwa73qqRURH-VnOQgLgXRL0sCFL_zzjXWonzWuyMkHNyjS2-ZS0tOF',
            'Content-Type: application/json'
        ];


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);
        //dd($result);
        return true;
    }	


    protected function doCreateThumbnail($max_width, $max_height, $source_file, $dst_dir, $quality = 80)
    {
        $imgsize = getimagesize($source_file);
        $width = $imgsize[0];
        $height = $imgsize[1];
        $mime = $imgsize['mime'];

        switch($mime){
            case 'image/gif':
                $image_create = "imagecreatefromgif";
                $image = "imagegif";
                break;

            case 'image/png':
                $image_create = "imagecreatefrompng";
                $image = "imagepng";
                $quality = 7;
                break;

            case 'image/jpeg':
                $image_create = "imagecreatefromjpeg";
                $image = "imagejpeg";
                $quality = 80;
                break;

            default:
                return false;
                break;
        }

        $dst_img = imagecreatetruecolor($max_width, $max_height);
        $src_img = $image_create($source_file);

        $width_new = $height * $max_width / $max_height;
        $height_new = $width * $max_height / $max_width;
        //if the new width is greater than the actual width of the image, then the height is too large and the rest cut off, or vice versa
        if($width_new > $width){
            //cut point by height
            $h_point = (($height - $height_new) / 2);
            //copy image
            imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
        }
        else
        {
            //cut point by width
            $w_point = (($width - $width_new) / 2);
            imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
        }

        $image($dst_img, $dst_dir, $quality);

        if($dst_img)imagedestroy($dst_img);
        if($src_img)imagedestroy($src_img);
    }
}
