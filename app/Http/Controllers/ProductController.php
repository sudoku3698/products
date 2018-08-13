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
    public function downloadExcel(FileOperations $FileOperations,$type)
    {
        return $FileOperations->get_external_file("http://127.0.0.1:8787/Final_product.xls");

        // return Excel::create('product_export_import', function($excel){

        //     $excel->sheet('mySheet', function($sheet)
        //     {
        //     	$reload=array();
        //         $reload[] = Product::exportProductDataColumnArray();
        //         $sheet->fromArray($reload);
        //     });
        // })->download($type);
    }

    //Export excel sheet
    /**
     * Download Excel FIle 
     *
     * @param  $url
     * 
     * @return download excel file and put in local server
     */
    public function test_importExcel(FileOperations $FileOperations)
    {
        return $FileOperations->test_get_external_file("http://127.0.0.1:8787/Final_product.xls");
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
        // echo "<pre>";
        // print_r($request->all());
        // exit;
        if($request->hasFile('import_file'))
        {
            //dd('ok');
            $result=Product::importProductData(storage_path().'/app/uploads/A.xls');
            //$result=Product::importProductData($request->file('import_file')); 
            if($result['Product_result']>0)
            {
              if($result['final_error'])
              {
                return redirect()->back()->with('success', $result['Product_result'].' rows affected')->with('errors',$result['final_error']);
              }else
              {
                return redirect()->back()->with('success', $result['Product_result'].' rows affected');
              }
                
            }else
            {
                return redirect()->back()->with('error',!empty($result['error'])?$result['error']:"Data is up to date")->with($result['final_error']?'errors':'',$result['final_error']);
            }
        }else
        {
            dd('not ok');
            return redirect()->back()->with('error','Please import valid file');
        }
    }
}
