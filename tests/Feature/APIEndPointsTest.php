<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\RoasterFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use App\Events\FileUploaded;
use Illuminate\Support\Str;

class APIEndPointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_roaster_file_upload_and_dispatch_event()
    {
        Event::fake();

        $filename = Str::random(10) . '.html';
        
        $file = UploadedFile::fake()->create($filename, 1000, 'text/html');

        $response = $this->post('/api/roaster/upload-file', [
            'file' => $file,
        ]);

        $this->assertTrue(Storage::disk('local')->exists('roaster_files/' . $filename));

        $roasterFile = RoasterFile::factory()->create([
            'filename' => $filename,
            'extension' => 'html',
            'path' => 'roaster_files/' . $filename,
        ]);

        Event::assertDispatched(FileUploaded::class, function ($event) {
            return $event->roasterFileId !== null;
        });

        $response->assertStatus(201);

        $response->assertJson([
            'status' => true,
            'message' => 'File uploaded successfully.',
        ]);
    }

    public function test_events_between_dates(): void
    {
        $response = $this->get('/api/roaster/events?startDate=2022-01-14&endDate=2022-01-23');

        $response->assertStatus(200)->assertJsonStructure(['status', 'data']);
    }

    public function test_flights_next_week(): void
    {
        $response = $this->get('/api/roaster/events/next-week?currentDate=2022-01-14&code=FLT');

        $response->assertStatus(200)->assertJsonStructure(['status', 'data']);
    }

    public function test_standby_next_week(): void
    {
        $response = $this->get('/api/roaster/events/next-week?currentDate=2022-01-14&code=SBY');

        $response->assertStatus(200)->assertJsonStructure(['status', 'data']);
    }

    public function test_flights_by_location(): void
    {
        $response = $this->get('/api/roaster/flights?location=krp');

        $response->assertStatus(200)->assertJsonStructure(['status', 'data']);
    }
}
