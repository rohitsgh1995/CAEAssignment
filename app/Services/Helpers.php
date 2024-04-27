<?php

namespace App\Services;

use Carbon\Carbon;

class Helpers
{
    public function getDateFromDay($dayNumber)
    {
        if (empty($dayNumber)) {
            return null;
        }

        $parts = explode(' ', $dayNumber);
        if (count($parts) != 2) {
            return null;
        }

        $dayAbbreviation = $parts[0];
        $dayOfMonth = intval($parts[1]);

        if ($dayOfMonth < 1 || $dayOfMonth > 31) {
            return null;
        }

        $currentDate = Carbon::create(2022, 1, 22);

        switch ($dayAbbreviation) {
            case 'Mon':
                $currentDate->startOfWeek()->addDays(1);
                break;
            case 'Tue':
                $currentDate->startOfWeek()->addDays(2);
                break;
            case 'Wed':
                $currentDate->startOfWeek()->addDays(3);
                break;
            case 'Thu':
                $currentDate->startOfWeek()->addDays(4);
                break;
            case 'Fri':
                $currentDate->startOfWeek()->addDays(5);
                break;
            case 'Sat':
                $currentDate->startOfWeek()->addDays(6);
                break;
            case 'Sun':
                $currentDate->startOfWeek()->addDays(7);
                break;
            default:
                return null;
        }

        $currentDate->day($dayOfMonth);
        
        return $currentDate->toDateString();
    }

    public function convertToTimeFormat($time)
    {
        if (empty($time) || strlen($time) !== 4) {
            return null;
        }

        $currentDate = Carbon::now();
        $carbonTime = Carbon::createFromFormat('Hi', $time, $currentDate->timezone);

        return $carbonTime->format('H:i');
    }

    public function getCode($activity)
    {
        if (empty($activity)) {
            return null;
        }

        if (preg_match('/^[A-Z]{2}\d+$/', $activity)) {
            return 'FLT';
        }

        // Available codes
        $codes = ['DO', 'OFF', 'SBY', 'CI', 'CO', 'CAR'];

        if (in_array($activity, $codes)) {
            return $activity;
        } else {
            return 'UNK';
        }
    }

    public function getCodeDescription($code)
    {
        switch ($code) {
            case 'DO':
                return 'Day Off';
            case 'OFF':
                return 'Day Off';
            case 'SBY':
                return 'Standby';
            case 'CI':
                return 'Check In';
            case 'CO':
                return 'Check Out';
            case 'CAR':
                return 'Company Car';
            case 'FLT':
                return 'Flight No';
            default:
                return 'Unknown';
        }
    }
}
