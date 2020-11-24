<?php

namespace App;

use App\Transformers\Company\CompanyTransformer;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
   public $timestamps = false;

   public $transformer = CompanyTransformer::class;
}
