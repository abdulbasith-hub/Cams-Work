<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event; // Assuming you have an Event model
//use Carbon\Carbon;
use App\Models\CalendarModel;

use App\Models\HolidayModel;
use Illuminate\Support\Facades\Validator;

class CalendarController extends Controller
{
    // Fetch events for the calendar
    public function getEvents(Request $request)
    {
        $userData = session('user');
        $session_userid = $userData->userid;
        $audit_scheduledetail = CalendarModel::fetchAuditScheduleDetailsDeptUsers($session_userid);

        $formattedEvents = $audit_scheduledetail->map(function($event) {
            //$event->todate = Carbon::parse($event->todate)->addDay()->format('Y-m-d');

            return [
                'id' => $event->auditscheduleid,
                'title' =>$event->instename,
                'start' => $event->fromdate, // Make sure start_date is in a format FullCalendar can parse, e.g., 'Y-m-d H:i:s'
                'end' => $event->todate, // Optional if your events have end times
                'extendedProps' => [
                    'calendar' =>'Primary', // You can customize this field
                ],
            ];
        });

        // Return as JSON
        return response()->json($formattedEvents);
    }

    public function getEventsDetails(Request $request)
    {
        $audit_scheduledetail = CalendarModel::GetSchedultedEventDetails($request->auditscheduleid);
        $audit_scheduledetail['fromdate_format'] = Controller::ChangeDateFormat($audit_scheduledetail->fromdate);
        $audit_scheduledetail['todate_format'] = Controller::ChangeDateFormat($audit_scheduledetail->todate);
        return $audit_scheduledetail;

    }

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

         $Holiday = CalendarModel::createHoliday($holidayData);
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


     public function RemoveHoliday($id)
     {
         $response = CalendarModel::RemoveHoliday($id);

         return response()->json($response);

     }

     public function getHolidays()
     {
         // Fetch all holidays from the database
         return CalendarModel::GetHoliday();

     }

     public function FetchHolidays()
     {

         return CalendarModel::FetchHoliday();


     }
}

?>
