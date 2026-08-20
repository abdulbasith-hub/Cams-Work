<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\UserManagementModel;


use App\Models\LeaveManagementModel;

use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\View;

class LeaveManagementController extends Controller
{






    public function storeOrUpdate(Request $request)
    {

        $userSessionData = session('user');
        if (!$userSessionData || !isset($userSessionData->userid)) {
            return redirect()->back()->withErrors(['Session expired or data missing.']);
        }
        $userid = $userSessionData->userid;

        $from_date = Carbon::createFromFormat('d/m/Y', $request->input('from_date'))->format('Y-m-d');
        $to_date = Carbon::createFromFormat('d/m/Y', $request->input('to_date'))->format('Y-m-d');

        // Manually inject the formatted date back into the request so that it gets validated properly
        $request->merge(['from_date' => $from_date]);
        $request->merge(['to_date'   => $to_date]);

        $request->validate([
            'from_date'     =>  'required|date|date_format:Y-m-d|',
            'to_date'       =>  'required|date|date_format:Y-m-d|',
            'leave_type'    =>  'required',
            'reason'        =>  'required',
        ], [
            'required' => 'The :attribute field is required.',
            'from_date.date' => 'The from date must be a date',
            'to_date.date' => 'The to date must be a date',
            'from_date.date_format:Y-m-d' => 'The from date must be in the format of Y-m-d',
            'to_date.date_format:Y-m-d' => 'The to date must be in the format of Y-m-d',
        ]);

        $data = [
            'userid'        => $userid,
            'fromdate'      => $request->input('from_date'),
            'todate'        => $request->input('to_date'),
            'leavetypecode' => $request->input('leave_type'),
            'reason'        => $request->input('reason'),
            'statusflag'    => $request->input('finaliseflag'),
            'createdon'     => now(),  // Current timestamp for created_at
            'updatedon'     => now(),  // Current timestamp for updated_at
            'createdby'     => $userid,
            'updatedby'     => $userid,
            'processcode'   => 'S'
        ];

        if ($request->action == 'update') {

            $leave_id = Crypt::decryptString($request->input('leave_id'));
        } else
            $leave_id =   null;

        try {
            // Call the model method for create or update
            $leavedetail = LeaveManagementModel::createleave_insertupdate($data, $leave_id, 'audit.ind_leavedetail', $userid);

            // If no user is returned, it means a conflict occurred and an exception was thrown
            return response()->json(['success' => 'Leave application detail was created/updated successfully', 'Leave Detail' => $leavedetail]);
        } catch (\Exception $e) {
            // Catch the exception thrown by the model and return the error message
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }




    public function fetchteamhead_data(Request $request)
    {
        $userSessionData = session('user');
        if (!$userSessionData || !isset($userSessionData->userid)) {
            return redirect()->back()->withErrors(['Session expired or data missing.']);
        }
        $userid = $userSessionData->userid;
        $leaveid = Crypt::decryptString($request->leaveid);
        $fromdate = DB::table('audit.ind_leavedetail')
            ->where('leaveid', $leaveid)
            ->value('fromdate');
        $teamheadid = LeaveManagementModel::get_teamheadid($fromdate, $userid, $leaveid);
        return response()->json(['success' => true, 'data' => $teamheadid]);
    }
}
