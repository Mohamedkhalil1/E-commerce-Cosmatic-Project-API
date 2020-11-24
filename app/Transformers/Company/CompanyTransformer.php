<?php

namespace App\Transformers\Company;

use App\Company;
use App\Transformers\GeneralTransformer;

class CompanyTransformer extends GeneralTransformer
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
    public function transform(Company $company)
    {
        return [
            'name' => $company->name,
            'logo' => $this->end_point.$company->logo
        ];
    }
}
