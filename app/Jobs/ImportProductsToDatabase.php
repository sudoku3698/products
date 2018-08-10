<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Product;
use Log;

class ImportProductsToDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $product_data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($product_data)
    {
        $this->product_data=$product_data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {   
        $count_records=0;
        try
        {      

          Log::info('Inserting data into database');

          $result=Product::insertOnDuplicateKey($this->product_data, ['product_color','product_name','product_url','product_sku','product_description','product_size']);

          Log::info('complete!!');

        } catch(\Exception $e) 
        {
        Log::error('Error in job: '. $e->getMessage());
        }
    }
}
