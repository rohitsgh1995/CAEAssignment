<?php

namespace App\Listeners;

use App\Models\Roaster;
use App\Models\Activity;
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

    protected $requiredCols = ['Date', 'Rev', 'DC', 'C/I(Z)', 'C/O(Z)', 'Activity', 'Remark', 'From', 'STD(Z)', 'To', 'STA(Z)', 'AC/Hotel', 'BLH', 'Flight Time', 'Night Time', 'Dur', 'Ext', 'Pax booked', 'ACReg'];

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

            // Log::info("file contents: $fileContents");

            $crawler = new Crawler($fileContents);

            $tableId = 'ctl00_Main_activityGrid';
            $table = $crawler->filter("#$tableId")->first();

            // Log::info("Table: " . $table->text());

            if ($table->count() > 0) {

                // table heads
                $headerId = 'ctl00_Main_activityGrid_-1';
                $headerRow = $table->filter("#$headerId")->first();
                $headers = $headerRow->filter('td')->each(function (Crawler $head) {
                    return $head->text();
                });

                // Log::info("All Headers: " . json_encode($headers));

                $requiredHeaders = [];

                foreach ($headers as $key => $head) {
                    if (in_array($head, $this->requiredCols)) {
                        $requiredHeaders[$key] = $head;
                    }
                }

                // Log::info("Required Headers: " . json_encode($requiredHeaders));

                //table rows
                $rows = $table->filter('tr')->each(function (Crawler $row) use ($requiredHeaders) {
                    $rowData = $row->filter('td')->each(function (Crawler $cell) {
                        return $cell->text();
                    });

                    $data = [];
                    foreach ($requiredHeaders as $key => $header) {
                        $data[$header] = trim($rowData[$key]);
                    }

                    return $data;
                });

                Log::info("All Rows: " . json_encode($rows));
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
