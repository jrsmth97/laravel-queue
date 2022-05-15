<?php

namespace App\Jobs;

use Exception;
use App\Events\UploadEvent;
use App\Models\Product;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CsvUploadProcess implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $header;
    public $data;
    public $completed;
    public $uploadId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($uploadId, $data, $header, $completed)
    {
        $this->data = $data;
        $this->header = $header;
        $this->completed = $completed;
        $this->uploadId = $uploadId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {

            foreach ($this->header as $i => $h) {
                $cleanHeader = strtolower($h);
                $this->header[$i] = preg_replace('/[^a-zA-Z_]/', '', $cleanHeader);
            }
            
            foreach ($this->data as $dt) {
                foreach ($dt as $i => $d) {
                    $cleanDt = mb_convert_encoding($d, 'UTF-8', 'UTF-8');
                    if (($i+1) === count($dt) && preg_match('/[+]/', $d)) {
                        $cleanDt  = number_format($cleanDt, 0,'','');
                    }
                    $dt[$i] = $cleanDt;
                }
                $csvData = array_combine($this->header, $dt);
                Product::updateOrCreate(['unique_key' => $dt[0]], $csvData);
            }
                
            if ($this->completed) {
                event(new UploadEvent($this->uploadId, 'completed'));
            }
        } catch (Exception $e) {
            event(new UploadEvent($this->uploadId, 'failed'));
        }
    }
}
