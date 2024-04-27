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
use Facades\App\Services\Helpers;

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

            $crawler = new Crawler($fileContents);

            $tableId = 'ctl00_Main_activityGrid';
            $table = $crawler->filter("#$tableId")->first();

            if ($table->count() > 0) {
                $headerId = 'ctl00_Main_activityGrid_-1';
                $headerRow = $table->filter("#$headerId")->first();
                $headers = $headerRow->filter('td')->each(function (Crawler $head) {
                    return $head->text();
                });

                $requiredHeaders = [];

                foreach ($headers as $key => $head) {
                    if (in_array($head, $this->requiredCols)) {
                        $requiredHeaders[$key] = $head;
                    }
                }

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

                $datesWithCheckInCheckOut = $this->getAllDatesWithCheckInCheckOut($rows);

                foreach ($datesWithCheckInCheckOut as $k => $d) {
                    $roaster = $this->createRoaster($d);

                    for ($i = $d['keystart']; $i <= $d['keyend']; $i++) {
                        $activity = $rows[$i];
                        $this->createActivity($roaster->id, $activity);
                    }
                }

                $file = RoasterFile::findOrFail($this->roasterFileId);
                $file->setCompleted();
            } else {
                $this->handleFailed("Table with ID '$tableId' not found.");
            }
        } catch (\Throwable $exception) {
            Log::error("Error retrieving file: " . $exception->getMessage());
            $this->handleFailed("Error retrieving file: " . $exception->getMessage());
        }
    }

    protected function getAllDatesWithCheckInCheckOut($rows)
    {
        $data = [];
        $currentDate = null;
        $checkin = null;
        $checkout = null;
        $keystart = null;
        $keyend = null;

        foreach ($rows as $k => $row) {
            if ($k > 0) {
                if (!empty($row['Date'])) {
                    if (!is_null($currentDate)) {
                        $data[] = [
                            'date' => $currentDate,
                            'checkin' => $checkin,
                            'checkout' => $checkout,
                            'keystart' => $keystart,
                            'keyend' => $keyend
                        ];
                    }
                    $currentDate = $row['Date'];
                    $checkin = null;
                    $checkout = null;
                    $keystart = null;
                    $keyend = null;
                }
                if (!empty($row['C/I(Z)']) && empty($checkin)) {
                    $checkin = $row['C/I(Z)'];
                }
                if (empty($keystart)) {
                    $keystart = $k;
                }
                if (!empty($row['C/O(Z)'])) {
                    $checkout = $row['C/O(Z)'];
                }
                $keyend = $k;
            }
        }
        $data[] = [
            'date' => $currentDate,
            'checkin' => $checkin,
            'checkout' => $checkout,
            'keystart' => $keystart,
            'keyend' => $keyend
        ];
        return $data;
    }

    private function createRoaster($data)
    {
        return Roaster::create([
            'date' => Helpers::getDateFromDay($data['date']),
            'check_in' => Helpers::convertToTimeFormat($data['checkin']),
            'check_out' => Helpers::convertToTimeFormat($data['checkout']),
        ]);
    }

    private function createActivity($roasterId, $data)
    {
        return Activity::create([
            'roaster_id' => $roasterId,
            'activity' => $data['Activity'] ?? null,
            'code' => Helpers::getCode($data['Activity']) ?? null,
            'description' => Helpers::getCodeDescription(Helpers::getCode($data['Activity'])) ?? null,
            'remark' => $data['Remark'] ?? null,
            'from' => $data['From'] ?? null,
            'std' => Helpers::convertToTimeFormat($data['STD(Z)']) ?? null,
            'to' => $data['To'] ?? null,
            'sta' => Helpers::convertToTimeFormat($data['STA(Z)']) ?? null,
            'remarks' => $data['Remark'] ?? null,
            'blh' => $data['BLH'] ?? null,
            'flight_time' => $data['Flight Time'] ?? null,
            'night_time' => $data['Night Time'] ?? null,
            'duration' => $data['Dur'] ?? null,
            'ext' => $data['Ext'] ?? null,
            'pax_booked' => $data['Pax booked'] ?? null,
            'ac_reg' => $data['ACReg'] ?? null,
        ]);
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
