<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Roaster;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RoasterController extends Controller
{
    public function getEventsBetweenDates(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        }

        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        try {
            $events = Roaster::with('activities')->whereDate('date', '>=', $startDate)
                ->whereDate('date', '<=', $endDate)
                ->get();

            return response()->json([
                'status' => true,
                'data' => $events
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Events not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }

    public function getActivitiesForNextWeek(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'currentDate' => 'required|date',
            'code' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        }

        $currentDate = $request->input('currentDate', Carbon::now()->toDateString());
        $code = $request->input('code');

        Carbon::setTestNow(Carbon::createFromFormat('Y-m-d', $currentDate));

        // next week
        $startDate = Carbon::now()->next(Carbon::MONDAY)->startOfDay();
        $endDate = $startDate->copy()->addDays(7)->endOfDay();

        try {
            $events = Roaster::whereHas('activities', function ($query) use ($code) {
                if (!empty($code)) {
                    $query->where('code', $code);
                }
            })
                ->whereDate('date', '>=', $startDate)
                ->whereDate('date', '<=', $endDate)
                ->with(['activities' => function ($query) use ($code) {
                    if (!empty($code)) {
                        $query->where('code', $code);
                    }
                }])
                ->get();

            return response()->json([
                'status' => true,
                'data' => $events
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Events not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }

    public function getFlightsByLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        }

        $location = strtoupper($request->input('location'));
        
        try {
            $flights = Roaster::whereHas('flights', function ($query) use ($location) {
                if (!empty($location)) {
                    $query->where('from', $location);
                }
            })
                ->with(['flights' => function ($query) use ($location) {
                    if (!empty($location)) {
                        $query->where('from', $location);
                    }
                }])
                ->get();

            return response()->json([
                'status' => true,
                'data' => $flights
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Flights not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }
}
