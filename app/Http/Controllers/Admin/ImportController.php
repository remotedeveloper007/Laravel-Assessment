<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Jobs\ProcessProductRow;
use Illuminate\Bus\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ImportController extends Controller
{
    // 
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function show()
    {
        return view('admin.import.show');
    }

    public function store(Request $request)
    {
        // dd($_SERVER['CONTENT_LENGTH'] ?? null);
        
        $request->validate(['file'=>'required|file|mimes:csv,txt|max:204800']);

        // dd(ini_get('upload_max_filesize'), ini_get('post_max_size'));

        $path = $request->file('file')->store('imports');
        
        $full = Storage::path($path);
        $file = new \SplFileObject($full);
        $file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY);
        $file->setCsvControl(',');

        $headers = null;
        $jobs = [];
        $chunkSize = 1000;
        $buffer = [];

        foreach ($file as $row) {
            if ($row === [null] || $row === false) continue;
            if ($headers === null) { 
                $headers = array_map('trim', $row); continue; 
            }
            $assoc = @array_combine($headers, $row);
            if (!$assoc) continue;
            $buffer[] = $assoc;
            if (count($buffer) >= $chunkSize) {
                $jobs[] = new ProcessProductRow($buffer);
                $buffer = [];
            }
        }
        if (count($buffer)) $jobs[] = new ProcessProductRow($buffer);

        $batch = Bus::batch($jobs)
            ->then(function (Batch $batch) {})
            ->catch(function (Batch $batch, Throwable $e) {
                Log::error('Batch job failed during product chunk upsert.', [
                    'batch_id' => $batch->id,
                    'batch_name' => $batch->name,
                    'job_count' => $batch->totalJobs,
                    'failed_job_count' => $batch->failedJobs,
                    'file' => basename($path),
                    'error_message' => $e->getMessage(),
                    'exception_class' => get_class($e),
                    'trace' => $e->getTraceAsString(),
                ]);                
            })
            ->name('products-import-'.basename($path))
            ->dispatch();

        return back()->with('status','Import queued. Batch ID: '.$batch->id);
    } 
}
