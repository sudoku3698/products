<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Yadakhov\InsertOnDuplicateKey;
use App\Http\Requests\StoreProductRules;
use Excel;
use App\Jobs\ImportProductsToDatabase;

class Product extends Model
{
	use InsertOnDuplicateKey;
	//table name from database
    protected $table="products";

    static $master_product_data=array();

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

     /**
     * return list of allowed extenstions
     *
     * @param  null
     * 
     * @return return array
     */
    public static function allowedFileExtentionsForProductImport()
    {
        return array('csv','xls','xlsx');
    }

    //insert and update product data in bulk
    /**
     * import data from excel file to mysql database
     *
     * @param  file_path
     * 
     * @return return valid data array, errors array and error
     */
    public static function importProductData($file)
    {

        $extention="";
        $product_data=array();
        $response_data=array();
        $response_data['Product_result']=0;
        $response_data['final_error']=array();
        $response_data['error']="";
        $extention=self::getFileExtention($file);
        if(in_array($extention, self::allowedFileExtentionsForProductImport()))
        {
            $result=self::load_excel_data($file);
            $response_data['Product_result']=$result['Product_result'];
            $response_data['final_error']=$result['final_error'];
        }else
        {
            $response_data['error']="Not a valid file";   
        }
        return $response_data;
    }

    public static function getFileExtention($get_file_path)
    {
        $info=pathinfo($get_file_path);
        if(isset($info['extension']))
        {
            $extention=$info['extension'];
        }else
        {
            $extention=$get_file_path->getClientOriginalExtension();
        }

        return $extention;
    }

    //insert and update product data in bulk
    /**
     * import excel file
     *
     * @param  file_path
     * 
     * @return return valid data array and errors array
     */
    public static function load_excel_data($get_file_path)
    { 
        $extention="";
        $count=0;
        $result=array();
        $result['product_data']=array();
        $result['final_error']=array();
        $extention=self::getFileExtention($get_file_path);
        if(in_array($extention, self::allowedFileExtentionsForProductImport()))
        {
            $get_file_path=is_object($get_file_path)?$get_file_path->getRealPath():$get_file_path;
            Excel::load($get_file_path, function ($reader) use (&$inserted_count,&$updated_count,&$final_error,&$product_data,&$result,&$count) {

                //return errors array and valid product_data array
                $result=self::validOrInvalidProductDataRequest($reader->toArray());

                //import data in chunk into the database
                $co=0;
                foreach(array_chunk($result['product_data'], config('constant.chuncks')) as $value){
                   $co=self::insertOnDuplicateKey($value, ['product_color','product_name','product_url','product_sku','product_description','product_size']);
                   $count=$count+$co;
                  }
                });
            $result['Product_result']=$count;
        }else
        {
            $result['error']="Not a valid file";
        }
        unset($result['product_data']);
        return $result;
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
            $result=0;
            foreach (array_chunk($product_data, 5000) as $idListBatch) {
                //ImportProductsToDatabase::dispatch($idListBatch)->delay(now()->addSecond(2));
                $result=$result+self::insertOnDuplicateKey($product_data, ['product_color','product_name','product_url','product_sku','product_description','product_size']);
            }
           // return self::insertOnDuplicateKey($product_data, ['product_color','product_name','product_url','product_sku','product_description','product_size']);
             return $result;
        }
        return false;
        
     }
}
