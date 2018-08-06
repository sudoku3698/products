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

     //insert and update product data in bulk
    /**
     * import excel file
     *
     * @param  product data array
     * 
     * @return affected rows
     */
     public static function InsertOrUpdateInBulk($product_data)
     {
     	return self::insertOnDuplicateKey($product_data, ['product_color','product_name','product_url','product_sku','product_description','product_size']);
     }
}
