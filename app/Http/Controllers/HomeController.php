<?php

namespace App\Http\Controllers;

use Exception;
use App\Events\UploadEvent;
use App\Jobs\CsvUploadProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Validation\ValidationException;
use Symfony\Component\CssSelector\Exception\InternalErrorException;

class HomeController extends Controller
{
    public function index() {
        return view('home');
    }

    public function uploadCsv(Request $request) {
        $request->validate([
            'csv' => 'required|mimes:csv,txt'
        ]);

        if ($request->file('csv')->getClientOriginalExtension() !== 'csv') {
            throw ValidationException::withMessages(['csv only !']);
        }

        $uploadId = $request->header('X-UPLOAD-ID');
        event(new UploadEvent($uploadId, 'pending'));
        $csv    = file($request->csv);
        $chunks = array_chunk($csv, 1000);
        $header = [];
        try {
            $batch  = Bus::batch([])->dispatch();
            foreach ($chunks as $key => $chunk) {
                $data = array_map('str_getcsv', $chunk);
                $completed = ($key+1) === count($chunks) ? true : false;
                if ($key === 0) {
                    $header = $data[0];
                    unset($data[0]);
                }
     
                event(new UploadEvent($uploadId, 'processing'));
                $batch->add(new CsvUploadProcess($uploadId, $data, $header, $completed));
            }
        } catch (Exception $e) {
            event(new UploadEvent($uploadId, 'failed'));
            throw new InternalErrorException($e->getMessage());
        }

        return $batch;
    }
}
