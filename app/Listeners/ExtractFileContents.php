<?php

namespace App\Listeners;

use App\Models\RoasterFile;
use App\Events\FileUploaded;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\Support\Facades\Storage;

class ExtractFileContents implements ShouldQueue
{
    protected $roasterFileId;

    public function __construct()
    {
        //
    }

    public function handle(FileUploaded $event)
    {
        try {
            $this->roasterFileId = $event->roasterFileId;
            $file = RoasterFile::findOrFail($this->roasterFileId);
            $file->setProcessing();

            if ($file->extension == 'html') {
                $this->extractHTML($file->path);
                $file->setCompleted();
            } else {
                $file->setFailed("Unsupported file extension: {$file->extension}" . PHP_EOL);
            }
        } catch (Throwable $exception) {
            Log::error("Error processing RoasterFile ID: $file->id. Error: " . $exception->getMessage());
            $this->handleFailed($exception->getMessage());
        }
    }

    public function extractHTML($filePath)
    {
        try {
            $fileContents = Storage::disk('local')->get($filePath);

            Log::info("file contents: $fileContents");

            $crawler = new Crawler($fileContents);

            $tableId = 'ctl00_Main_activityGrid';
            $table = $crawler->filter("#$tableId")->first();

            if ($table->count() > 0) {
                $rows = $table->filter('tr')->each(function (Crawler $row) {
                    return $row->text();
                });

                foreach ($rows as $row) {
                    echo $row . "\n";
                }
            } else {
                $this->handleFailed("Table with ID '$tableId' not found.");
            }
        } catch (\Throwable $exception) {
            Log::error("Error retrieving file: " . $exception->getMessage());
            $this->handleFailed("Error retrieving file: " . $exception->getMessage());
        }
    }


    public function failed(Throwable $exception)
    {
        Log::error("Error processing RoasterFile ID: {$this->roasterFileId}. Error: " . $exception->getMessage());
        $this->handleFailed($exception->getMessage());
    }

    protected function handleFailed($errorMessage)
    {
        $file = RoasterFile::findOrFail($this->roasterFileId);
        $file->setFailed($errorMessage . PHP_EOL);
    }
}
