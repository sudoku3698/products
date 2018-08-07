<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Yadakhov\InsertOnDuplicateKey;
use App\Http\Requests\StoreProductRules;

class Product extends Model
{
	use InsertOnDuplicateKey;
	//table name from database
    protected $table="products";

    protected $fillable = ['product_name', 'product_url','product_sku','product_description','product_color','product_size','product_uuid'];



    public static function exportProductDataColumnArray()
    {
    	return array(
                	'product_name' => "", 
                	'product_url' => "",
                	'product_sku' => "",
                	'product_description' => "",
                	'product_color' => "",
                	'product_size' => "",
                	'product_uuid' => ""
                	);
    }

    //insert and update product data in bulk
    /**
     * import excel file
     *
     * @param  product data array
     * 
     * @return affected rows or false
     */
     public static function InsertOrUpdateInBulk($product_data)
     {
     	/*ON DUPLICATE KEY UPDATE product_color=values(product_color),product_name=values(product_name),product_url=values(product_url),product_sku=values(product_sku),product_description=values(product_description),product_size=values(product_size)

        INSERT INTO `products`(`product_name`,`product_url`,`product_sku`,`product_description`,`product_color`,`product_size`,`product_uuid`) VALUES\n
        (?,?,?,?,?,?,?), (?,?,?,?,?,?,?)\n
        ON DUPLICATE KEY UPDATE `product_color` = VALUES(`product_color`), `product_name` = VALUES(`product_name`), `product_url` = VALUES(`product_url`), `product_sku` = VALUES(`product_sku`), `product_description` = VALUES(`product_description`), `product_size` = VALUES(`product_size`)
        returns 1 on insert and 2 on update 
        */
        if($product_data)
        {
            return self::insertOnDuplicateKey($product_data, ['product_color','product_name','product_url','product_sku','product_description','product_size']);
        }
        return false;
     	
     }

    //check valid and invalid data
    /**
     * import excel file
     *
     * @param  product data array
     * 
     * @return errors and valid product data
     */
     public static function validOrInvalidProductDataRequest($product_data)
     {
     	$product_data_array=array();
     	$final_error=array();
     	foreach ($product_data as $key => $row) 
        {
            //Validation Check For Each Row
            $validator = \Validator::make($row,(new StoreProductRules)->rules());
              if($validator->fails()) 
              {
                $final_error[$key]=$validator->errors()->messages();
              }else
              { 
                $product_data_array[]=$row;
              } 
        }

        return array('final_error'=>$final_error,'product_data'=>$product_data_array);
     }
}
