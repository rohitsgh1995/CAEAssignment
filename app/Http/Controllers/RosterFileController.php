<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RoasterFile;
use App\Events\FileUploaded;
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

            $roasterFile = RoasterFile::create([
                'filename' => $filename,
                'extension' => $file->extension(),
                'path' => $path,
            ]);

            event(new FileUploaded($roasterFile));

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

    public function getAllFiles()
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

    public function getFileById(int $id)
    {
        try {
            $file = RoasterFile::findOrFail($id);

            return response()->json([
                'status' => true,
                'data' => $file,
            ], 200);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status' => false,
                'message' => 'No roaster file found.',
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve roaster files.',
            ], 500);
        }
    }
}
