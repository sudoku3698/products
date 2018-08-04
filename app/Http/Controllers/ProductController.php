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
					  }	
                }
            });
            //ON DUPLICATE KEY UPDATE product_uuid=values(product_uuid)
            $Product_result=Product::insertOnDuplicateKey($product_data, ['product_color','product_name','product_url','product_sku','product_description','product_size']);
            // print_r($Product_result);
            // exit;
        }
        if(count($product_data)>0)
        {
        	if($final_error){
        		return redirect()->back()->with('success', $Product_result.' columns affected')->with('errors',$final_error);
        	}else
        	{
        		return redirect()->back()->with('success', $Product_result.' columns affected');
        	}
        	
        }else
        {
        	return redirect()->back()->with('errors',$final_error);
        }
        
    }
}
