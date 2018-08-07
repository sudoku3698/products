<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Input;
use App\Product;
use DB;
use Session;
use Excel;
use App\Library\Services\FileOperations;

class ProductController extends Controller
{
    protected $FileOperations;
    public function __construct(FileOperations $FileOperations) 
    {
        $this->FileOperations=$FileOperations;
    }

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
        return $this->FileOperations->get_external_file("http://127.0.0.1:8787/Final_product.xls");

        // return Excel::create('product_export_import', function($excel){

        //     $excel->sheet('mySheet', function($sheet)
        //     {
        //     	$reload=array();
        //         $reload[] = Product::exportProductDataColumnArray();
        //         $sheet->fromArray($reload);
        //     });
        // })->download($type);
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
                
                //return errors array and valid product_data array
                $result=Product::validOrInvalidProductDataRequest($reader->toArray());

                $final_error=$result['final_error'];
                $product_data=$result['product_data'];
            });

            $Product_result=Product::InsertOrUpdateInBulk($product_data);
        }
        if(count($product_data)>0)
        {
            if($final_error){
                return redirect()->back()->with('success', $Product_result.' rows affected')->with('errors',$final_error);
            }else
            {
                return redirect()->back()->with('success', $Product_result.' rows affected');
            }
            
        }else
        {
            return redirect()->back()->with('errors',$final_error);
        }
        
    }
}
