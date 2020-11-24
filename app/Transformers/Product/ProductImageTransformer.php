<?php

namespace App\Transformers\Product;

use App\ProductImage;
use App\Transformers\GeneralTransformer;

class ProductImageTransformer extends GeneralTransformer
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];
    
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        //
    ];
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(ProductImage $productImage)
    {
        return [
            'image'                  => $this->end_point.$productImage->image,
            'phone_image'            => $this->end_point.$productImage->phone_image
          ];
    }
}
