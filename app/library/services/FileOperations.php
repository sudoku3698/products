<?php
namespace App\Library\Services;
  
class FileOperations
{
	public function test_get_external_file($url)
    {
    	 
      	$ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);

        //save the data to disk
	    $file = storage_path().'/app/uploads/excel.xls';
	    file_put_contents($file, $data);
    }
    
    public function get_external_file($url)
    {
      	$ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary"); 
        header("Content-disposition: attachment; filename=\"Final_product.xls.xls\""); 
        return $data;
    }
}