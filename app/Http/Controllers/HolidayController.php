<?php

namespace App\Http\Controllers;

use App\Models\HolidayModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HolidayController extends Controller
{
    // Store new holiday
    public function AddHoliday(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'holiday_title' => 'required|string|max:255',
            'holiday_date' => 'required|date',
        ]);

        $holidayData = [
            'holiday_title' => $request->holiday_title,
            'holiday_date' => $request->holiday_date,
        ];

        $Holiday = HolidayModel::createHoliday($holidayData);
        if($Holiday)
        {
            return response()->json([
                'success' => true,
                'message' => 'Holiday created successfully.',
                'holiday_id'=>$Holiday
            ]);

        }else
        {
            return response()->json([
                'message' => 'Holiday not created.',
            ]);

        }
       
    }

    public function getHolidays()
    {
        // Fetch all holidays from the database
        $holidays = HolidayModel::where('statusflag', 'Y')->get();

        // Return the events in a format that FullCalendar expects
        $events = $holidays->map(function ($holiday) {
            return [
                'id' => $holiday->holiday_id, // ID of the holiday
                'holiday_title' => $holiday->holiday_title,   // Holiday title
                'holiday_date' => $holiday->holiday_date,   // Holiday start date
                'extendedProps' => ['calendar' => 'danger' ],
            ];
        });

        return response()->json($events);
    }

    public function FetchHolidays()
    {

        $holidays = HolidayModel::where('statusflag', 'Y') // Only active holidays
                                ->get(['holiday_date', 'holiday_title']) // Fetch both date and name
                                ->map(function ($holiday) {
                                    return [
                                        'date' => \Carbon\Carbon::parse($holiday->holiday_date)->format('d/m/Y'), // Format date
                                        'name' => $holiday->holiday_title, // Include holiday name
                                    ];
                                });

        return response()->json($holidays);


    }


    public function RemoveHoliday($id)
    {
        $response = HolidayModel::RemoveHoliday($id);
        
        return response()->json($response);

    }

    
}
