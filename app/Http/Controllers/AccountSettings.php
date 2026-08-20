<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Import Hash facade
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AccountSettings extends Controller

{

    // public function dynamic_modal()
    // {
    //     $charge = session('charge');
    //     $usertypecode = $charge->usertypecode ?? null;
    //     $user = session('user');
    //     $userId = $user->userid ?? null;

    //     $profileUpdate = null;

    //     if ($usertypecode && $userId) {
    //         // Check based on usertypecode
    //         if ($usertypecode === 'A') {
    //             // For usertypecode 'A', check the 'audit.deptuserdetails' table with 'deptuserid'
    //             $userRecord = DB::table('audit.deptuserdetails')->where('deptuserid', $userId)->first();
    //         } elseif ($usertypecode === 'I') {
    //             // For usertypecode 'I', check the 'audit.audtieeuserdetails' table with 'auditeeuserid'
    //             $userRecord = DB::table('audit.audtieeuserdetails')->where('auditeeuserid', $userId)->first();
    //         }

    //         // Check if the profile_update field is 'Y'
    //         if (isset($userRecord) && $userRecord->profile_update === 'Y') {
    //             $profileUpdate = 'Y';
    //         }

    //         // Return the appropriate view based on the usertypecode
    //         if ($usertypecode === 'A') {
    //             return view('dashboard.dashboard', compact('profileUpdate'));
    //         } elseif ($usertypecode === 'I') {
    //             return view('dashboard.auditeedashboard', compact('profileUpdate'));
    //         }
    //     }

    // }

    public function dynamic_modal($viewName)
    {
        //dd(session()->all()); // Debug session data

        $charge = session('charge');
        //print_r($usertypecode);

        $usertypecode = $charge->usertypecode ?? null;

        $user = session('user');
        $userId = $user->userid ?? null;

        $profileUpdate = null;

        if ($usertypecode && $userId) {
            if ($usertypecode === 'A') {
                $userRecord = DB::table('audit.deptuserdetails')->where('deptuserid', $userId)->first();
            } elseif ($usertypecode === 'I') {
                $userRecord = DB::table('audit.audtieeuserdetails')->where('auditeeuserid', $userId)->first();
            }

            if (isset($userRecord) && $userRecord->profile_update === 'Y') {
                $profileUpdate = 'Y';
            }

            if ($usertypecode === 'A') {
                return view($viewName, compact('profileUpdate'));
            } elseif ($usertypecode === 'I') {
                return view($viewName, compact('profileUpdate'));
            }
        }
    }




    // Common method for processing password change
    // private function processChangePassword(Request $request, bool $checkProfileUpdate = true)
    // {
    //     // Validate the request
    //     $request->validate([
    //         'oldpassword' => 'required',
    //         'newpassword' => [
    //             'required',
    //             'min:8',
    //             'regex:/[a-z]/',
    //             'regex:/[A-Z]/',
    //             'regex:/[0-9]/',
    //             'regex:/[@$!%*?&#]/',
    //             'different:oldpassword',
    //         ],
    //     ]);

    //     // Retrieve usertypecode from the session
    //     $charge = session('charge');
    //     $usertypecode = $charge->usertypecode ?? null;

    //     if (!$usertypecode) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Session variable "usertypecode" is not set.',
    //         ], 400);
    //     }

    //     // Retrieve userid from the session
    //     $sessionuserdel = session('user');
    //     $userId = $sessionuserdel->userid ?? null;

    //     if (!$userId) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Session variable "userid" is not set.',
    //         ], 400);
    //     }

    //     // Determine the table and column based on usertypecode
    //     $tableName = null;
    //     $userIdColumn = null;
    //     if ($usertypecode === 'A') {
    //         $tableName = 'audit.deptuserdetails';
    //         $userIdColumn = 'deptuserid';
    //     } elseif ($usertypecode === 'I') {
    //         $tableName = 'audit.audtieeuserdetails';
    //         $userIdColumn = 'auditeeuserid';
    //     } else {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Invalid user role type.',
    //         ], 400);
    //     }

    //     // Fetch the user based on the correct column and table
    //     $user = DB::table($tableName)->where($userIdColumn, $userId)->first();

    //     if (!$user) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'User not found.',
    //         ], 404);
    //     }

    //     // Check the old password
    //     if (!Hash::check($request->oldpassword, $user->pwd)) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Old password is incorrect.',
    //         ], 400);
    //     }

    //     // Update the password and profile_update field
    //     try {
    //         // If profile_update check is required, ensure it is 'Y' before proceeding
    //         if ($checkProfileUpdate && $user->profile_update !== 'Y') {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Password update failed. Update status is not Y.',
    //             ], 400);
    //         }

    //         DB::table($tableName)
    //             ->where($userIdColumn, $userId)
    //             ->update([
    //                 'pwd' => Hash::make($request->newpassword),
    //                 'profile_update' => 'N',
    //             ]);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Password changed successfully.',
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'An error occurred while updating the password.',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    // // First function using processChangePassword
    // public function ChangePassword(Request $request)
    // {
    //     return $this->processChangePassword($request, true);
    // }

    // // Second function using processChangePassword
    // public function dashboard_ChangePassword(Request $request)
    // {
    //     return $this->processChangePassword($request, false);
    // }





    private function handlePasswordChange(Request $request, $checkProfileUpdate = false)
    {
        try{

            $rules = [
                'oldpassword' => 'required',
    
                'newpassword' => [
                    'required',
                    'string',
                    'min:8', 
                    'regex: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,20}$/', 
                ],
                'confirmpassword' => 'required', 
            ];
    
            $validator = Validator::make($request->all(), $rules);
    
            // If validation fails, throw an exception with a single message
            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }
    
            $charge = session('charge');
            $usertypecode = $charge->usertypecode ?? null;
    
            $sessionuserdel = session('user');
            $userId = $sessionuserdel->userid ?? null;
    
            if (!$usertypecode || !$userId) {
                return response()->json([
                    'success' => false,
                    'message' => !$usertypecode ? 'Session variable "usertypecode" is not set.' : 'Session variable "userid" is not set.',
                ], 400);
            }
    
            $tableDetails = [
		'H' => ['table' => 'audit.auditee_dept', 'column' => 'auditeedeptid'],
                'A' => ['table' => 'audit.deptuserdetails', 'column' => 'deptuserid'],
                'I' => ['table' => 'audit.audtieeuserdetails', 'column' => 'auditeeuserid'],
            ];
    
            if (!isset($tableDetails[$usertypecode])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid user role type.',
                ], 400);
            }
    
            $tableName = $tableDetails[$usertypecode]['table'];
            $userIdColumn = $tableDetails[$usertypecode]['column'];
    
            // Fetch the user
            $user = DB::table($tableName)->where($userIdColumn, $userId)->first();
    
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.',
                ], 404);
            }
    
            if (!Hash::check($request->oldpassword, $user->pwd)) {
                return response()->json([
                    'success' => false,
                    'old' => 'old_pass',
                ], 400);
            }

            
            if ($request->newpassword !== $request->confirmpassword) {
                return response()->json([
                    'success' => false,
                    'newandconf' => 'confmismatch',
                ], 400);
            }
    
    
            if (Hash::check($request->newpassword, $user->pwd)) {
                return response()->json([
                    'success' => false,
                    'oldandnew' => 'oldandnew',
                ], 400);
            }
        
    
            // Handle the profile_update check if required
            if ($checkProfileUpdate && $user->profile_update !== 'Y') {
                return response()->json([
                    'success' => false,
                    'message' => 'Password update failed. Update status is not Y.',
                ], 400);
            }
    
           
                // Update the password and optionally reset profile_update
                $updateData = ['pwd' => Hash::make($request->newpassword)];
                if ($checkProfileUpdate) {
                    $updateData['profile_update'] = 'N';
                }
    
                DB::table($tableName)
                    ->where($userIdColumn, $userId)
                    ->update($updateData);
    
                return response()->json([
                    'success' => true,
                    'message' => 'pass_success',
                    
                ], 200);
        

        }catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the password.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function ChangePassword(Request $request)
    {
        return $this->handlePasswordChange($request, true);
    }

    public function dashboard_ChangePassword(Request $request)
    {
        return $this->handlePasswordChange($request, false);
    }








    // public function ChangePassword(Request $request)
    // {
    //     // Validate the request
    //     $request->validate([
    //         'oldpassword' => 'required',
    //         'newpassword' => [
    //             'required',
    //             'min:8',
    //             'regex:/[a-z]/',
    //             'regex:/[A-Z]/',
    //             'regex:/[0-9]/',
    //             'regex:/[@$!%*?&#]/',
    //             'different:oldpassword',
    //         ],
    //     ]);

    //     // Retrieve usertypecode from the session
    //     $charge = session('charge');
    //     $usertypecode = $charge->usertypecode ?? null;

    //     if (!$usertypecode) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Session variable "usertypecode" is not set.',
    //         ], 400);
    //     }

    //     // Retrieve userid from the session
    //     $sessionuserdel = session('user');
    //     $userId = $sessionuserdel->userid ?? null;

    //     if (!$userId) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Session variable "userid" is not set.',
    //         ], 400);
    //     }


    //     // Determine the table based on usertypecode
    //     $tableName = null;
    //     if ($usertypecode === 'A') {
    //         $tableName = 'audit.deptuserdetails';
    //     } elseif ($usertypecode === 'I') {
    //         $tableName = 'audit.audtieeuserdetails';
    //     } else {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Invalid user role type.',
    //         ], 400);
    //     }


    //     // Fetch the user using `deptuserid`
    //     $user = DB::table($tableName)->where('deptuserid', $userId)->first();
    //     // $user_aud = DB::table($tableName)->where('auditeeuserid', $userId)->first();

    //     if (!$user) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'User not found.',
    //         ], 404);
    //     }

    //     // $profileUpdate = $user->profile_update === 'Y';
    //     // return view('dashboard.dashboard', compact('profileUpdate'));


    //     // Check the old password
    //     if (!Hash::check($request->oldpassword, $user->pwd)) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Old password is incorrect.',
    //         ], 400);
    //     }

    //     // Update the password and profile_update field
    //     if ($user->profile_update === 'Y') {
    //         DB::table($tableName)
    //             ->where('deptuserid', $userId)
    //             ->update([
    //                 'pwd' => Hash::make($request->newpassword),
    //                 'profile_update' => 'N',
    //             ]);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Password changed successfully.',
    //         ]);
    //     } else {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Password update failed. Update status is not Y.',
    //         ], 400);
    //     }
    // }
}
