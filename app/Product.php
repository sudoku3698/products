<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Yadakhov\InsertOnDuplicateKey;

class Product extends Model
{
	use InsertOnDuplicateKey;
	//table name from database
    protected $table="products";

     protected $fillable = ['product_name', 'product_url','product_sku','product_description','product_color','product_size','product_uuid'];
}
