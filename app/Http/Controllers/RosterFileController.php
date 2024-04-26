<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RoasterFile;
use App\Http\Requests\RosterFileRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RosterFileController extends Controller
{
    public function uploadFile(RosterFileRequest $request)
    {
        try {
            if (!$request->hasFile('file')) {
                return response()->json([
                    'status' => false,
                    'message' => 'File not found.'
                ], 400);
            }

            $file = $request->file('file');

            $filename = 'roaster-file-' . time() . '.' . $file->extension();
            $path = Storage::putFileAs('roaster_files', $file, $filename);

            RoasterFile::create([
                'filename' => $filename,
                'mime' => $file->extension(),
                'path' => $path,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'File uploaded successfully.'
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function getFiles()
    {
        try {
            $files = RoasterFile::all();

            return response()->json([
                'status' => true,
                'data' => $files,
            ], 200);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status' => false,
                'message' => 'No roaster files found.',
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve roaster files.',
            ], 500);
        }
    }
}
