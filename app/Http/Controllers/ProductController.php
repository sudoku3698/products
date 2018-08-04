<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Input;
use App\Product;
use DB;
use Session;
use Excel;
use App\Http\Requests\StoreProductsValdation;


class ProductController extends Controller
{
	//view page for import and export
    public function product_import_export()
    {
    	return view('product.import_export');
    }

    //Export excel sheet
    /**
     * Download Excel FIle 
     *
     * @param  file  $type (xls,csv,xlsx)
     * 
     * @return download excel file
     */
    public function downloadExcel($type)
    {
        return Excel::create('product_export_import', function($excel){

            $excel->sheet('mySheet', function($sheet)
            {
            	//$product=Product::all();
            	$reload=array();
                $reload[] = array(
                	'product_name' => "", 
                	'product_url' => "",
                	'product_sku' => "",
                	'product_description' => "",
                	'product_color' => "",
                	'product_size' => "",
                	'product_uuid' => ""
                	);
                $sheet->fromArray($reload);
            });
        })->download($type);
    }


    //Import excel sheet 
    /**
     * import excel file
     *
     * @param  excel file
     * 
     * @return view page with success and error
     */
    public function importExcel(Request $request)
    {
    	$inserted_count=0;
    	$updated_count=0;
    	$final_error=array();
        $product_data=array();
    	
        if($request->hasFile('import_file'))
        {

            Excel::load($request->file('import_file')->getRealPath(), function ($reader) use (&$inserted_count,&$updated_count,&$final_error,&$product_data) {
                foreach ($reader->toArray() as $key => $row) 
                {

                	//Validation Check For Each Row
                    $validator = \Validator::make($row,(new StoreProductsValdation)->rules());
					  if($validator->fails()) 
					  {
					  	$final_error[$key]=$validator->errors()->messages();
					  }else
					  {	

                        $product_data[]=$row;
					  	//check if uuid is present or not if it is so then update orelse save
					  	// $product=Product::where(['product_uuid'=>$row['product_uuid']])->first();
					  	// if($product)
					  	// {
					  	// 	$product->product_name=$row['product_name'];
					  	// 	$product->product_url=$row['product_url'];
					  	// 	$product->product_sku=$row['product_sku'];
					  	// 	$product->product_description=$row['product_description'];
					  	// 	$product->product_color=$row['product_color'];
					  	// 	$product->product_size=$row['product_size'];
					  	// 	$product->save();
					  	// 	$updated_count++;
					  	// }else
					  	// {
					  	// 	$product=new Product();
						  // 	$product->product_name=$row['product_name'];
					  	// 	$product->product_url=$row['product_url'];
					  	// 	$product->product_sku=$row['product_sku'];
					  	// 	$product->product_description=$row['product_description'];
					  	// 	$product->product_color=$row['product_color'];
					  	// 	$product->product_size=$row['product_size'];
					  	// 	$product->product_uuid=$row['product_uuid'];
					  	// 	$product->save();
					  	// 	$inserted_count++;
					  	// }
					  }	
                }
                
            });
            //ON DUPLICATE KEY UPDATE product_uuid=values(product_uuid)
            $Product_result=Product::insertOnDuplicateKey($product_data, ['product_uuid','product_color','product_name','product_url','product_sku','product_description','product_size']);
            print_r($Product_result);
            exit;
        }
        if($inserted_count>0 || $updated_count>0)
		        {
		        	if($final_error){
		        		return redirect()->back()->with('success', $inserted_count.' columns inserted and '.$updated_count.' columns updated')->with('errors',$final_error);
		        	}else
		        	{
		        		return redirect()->back()->with('success', $inserted_count.' columns inserted and '.$updated_count.' columns updated');
		        	}
		        	
		        }else
		        {
		        	return redirect()->back()->with('errors',$final_error);
		        }
        
    }
}
