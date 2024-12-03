<?php

namespace App\Jobs;

use App\Repositories\AuthRepositoryImpl;
use App\Repositories\DocumentRepository;
use App\Repositories\DocumentRepositoryImp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessDocumentEncryption implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $data;
    protected DocumentRepositoryImp $repository;


    public function __construct( DocumentRepositoryImp $repository,array $data)
    {
        $this->data = $data;
        $this->repository = $repository;
    }

    public function handle()
    {
        try {
            Log::info('ProcessDocumentEncryption started.', ['data' => $this->data]);

            // Use the repository
            $this->repository->storeOrUpdateDocumentKey($this->data);

            Log::info('ProcessDocumentEncryption completed successfully.');
        } catch (\Exception $e) {
            Log::error('ProcessDocumentEncryption failed.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $this->data,
            ]);

            throw $e;
        }
    }
}
