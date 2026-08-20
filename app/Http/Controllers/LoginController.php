<?php

namespace App\Http\Controllers;

use App\Helpers\CryptoHelper;
use App\Models\SmsmailModel;
use App\Services\PHPMailerService;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;  // Import Hash facade
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class LoginController extends Controller
{
    public function generatePassword($length = 8)
    {
        $upper = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lower = 'abcdefghijkmnopqrstuvwxyz';
        $numbers = '23456789';
        $special = '$';

        $password = [
            $upper[random_int(0, strlen($upper) - 1)],
            $lower[random_int(0, strlen($lower) - 1)],
            $numbers[random_int(0, strlen($numbers) - 1)],
            $special[random_int(0, strlen($special) - 1)],
        ];

        $all = $upper . $lower . $numbers . $special;
        for ($i = 4; $i < $length; $i++) {
            $password[] = $all[random_int(0, strlen($all) - 1)];
        }

        shuffle($password);

        return implode('', $password);
    }

    protected $smsService;
    protected $mailService;

    // Combine both services in the constructor
    public function __construct(SmsService $smsService, PHPMailerService $mailService)
    {
        $this->smsService = $smsService;
        $this->mailService = $mailService;
    }

    // public function sendTestEmail()
    // {

    // }

        public function login(Request $request)
    {
    //     $to = 'swathinagarajann99@gmail.com';
    //     $subject = 'Test Subject';
    //     $body = '<h1>This is a test email</h1><p>Sending emails using PHPMailer in Laravel</p>';

    //    print_r($this->mailService->sendEmail($to, $subject, $body));

    //    exit;

        // return response()->json(['result' => $result]);


        //$otp    =   rand(1000,9999);

        // $mobileNumber = "8148958988";
        // $response = $this->smsService->sendSms($mobileNumber,$otp,'login');

        // print_r($response);


        // exit;


        // Validate user input
        $request->validate([
            'username' => 'required|email',  // Assuming username is an email
            'encryptedPassword' => 'required|min:6',  // Minimum password length
            'captcha' => 'required|string'
        ]);

       if ($request->captcha !== Session::get('captcha_code'))
        {
            return response()->json([
                "message" => __("validation.captcha"),
                "errors" => [
                    "captcha" => [__("validation.captcha")]
                ]
            ], 400);


        }

        $username = $request->username;

        $encryptedPassword = $request->encryptedPassword;
        // dd($encryptedPassword);
        // Manually call the helper function
        $password = CryptoHelper::decryptPassword($encryptedPassword);
        // Fetch user from the database (replace with your actual DB query)
        // $user = DB::table(table: 'audit.deptuserdetails')->where('email', $username)->first();

        // // Check if user exists and password is correct
        // if ($user && Hash::check($password, $user->pwd))

        // Fetch user from the database (replace with your actual DB query)
        $user = DB::table('audit.deptuserdetails')->where('email', $username)
        ->where('statusflag','Y')->first();

        // Check if user exists and password is correct
        if ($user && Hash::check($password, $user->pwd))  // Compare hashed password'
        // if ($user && $request->password === $user->pwd)
        {  // Compare hashed password

            $lastlogin_userdel = DB::table('audit.userlogindetails')
                ->where('userid', $user->deptuserid)
                ->orderByDesc('loginid')  // Order by ID in descending order
                ->limit(1)           // Limit to 1 record
                ->first();


            $lastlogin_time =   '';
            $deptuserid =   $user->deptuserid;

            if ($lastlogin_userdel) {
                $lastlogin_time =   $lastlogin_userdel->logintime;
                DB::table('audit.userlogindetails')
                    ->where('userid', $user->deptuserid) // Condition to identify the record
                    ->update([
                        'activestatus' => 'N',  // Field and new value
                    ]);
            }


            $insertedId = DB::table('audit.userlogindetails')->insertGetId([
                'userid' => $user->deptuserid,
                'ipadd' => $request->ip(),  // Get the server's IP address
                'browser' => $request->userAgent(),      // Get the user's browser information
                'machinename' => gethostname(),           // Get the server's hostname
                'logintime' =>  View::shared('get_nowtime'),                     // Current timestamp using Laravel's `now()` helper
                'activestatus' => 'Y'                    // Active status set to 'Y'
            ], 'loginid');  // Specify 'loginid' as the primary key column




            // echo $user->deptuserid;


            if ($lastlogin_time) {
                DB::table('audit.deptuserdetails')
                    ->where('deptuserid', $deptuserid) // Condition to identify the record
                    ->update([
                        'lastlogin' => $lastlogin_time,  // Field and new value
                    ]);
            }



            $charge = DB::table('audit.userchargedetails as uc')
                ->join('audit.deptuserdetails as du', 'uc.userid', '=', 'du.deptuserid') // Adjust the columns as needed
                ->join('audit.chargedetails as c', 'c.chargeid', '=', 'uc.chargeid')
                ->join('audit.rolemapping as rm', 'rm.rolemappingid', '=', 'c.rolemappingid')
                ->leftJoin('audit.roletypemapping as rtm', 'rtm.roletypemappingcode', '=', 'rm.roletypemappingcode')
                ->leftJoin('audit.mst_dept as de', 'de.deptcode', '=', 'c.deptcode')
                ->leftJoin('audit.mst_district as di', 'di.distcode', '=', 'c.distcode')
                ->leftJoin('audit.mst_region as r', 'r.regioncode', '=', 'c.regioncode')
                ->leftJoin('audit.auditor_instmapping as i', 'i.instmappingcode', '=', 'c.instmappingcode')
                ->leftJoin('audit.auditplanteammember as at', 'at.userid', '=', 'du.deptuserid')
                ->join('audit.mst_designation as d', 'd.desigcode', '=', 'du.desigcode')
                ->where('du.deptuserid', $deptuserid)
                ->where('uc.chargeflag', 'P')
                ->where('uc.statusflag','=','Y' )
                ->where('du.statusflag','=','Y' )
                // ->where('uc.statusflag','=','Y' )
                ->select(
                    'uc.chargeid',
                    'uc.userid',
                    'c.chargedescription',
                    'c.deptcode',
                    'c.regioncode',
                    'rtm.usertypecode',
                    'c.distcode',
                    'c.desigcode',
                    'd.desigelname',
                    'd.desigtlname',
                    'de.deptesname',
                    'd.desigesname',
                    'di.distsname',
                    'r.regionename',
                    'r.regiontname',
                    'di.distename',
                    'di.disttname',
                    'd.desigelname',
                    'du.username',
                    'du.usertamilname',
                    'du.email',
                    'du.mobilenumber',
                    'rtm.roletypecode',
		            'rm.roleactioncode',
                    'du.lastlogin',
                    'rm.rolemappingid',
                    'uc.userchargeid',
                    'at.teamhead as auditteamhead',

                ) // Select all columns from both tables
                ->first();

            // print_r($charge);

            // exit;

            if($charge)
            {
                $user = new \stdClass();  // Create a new instance of stdClass
                $user->userid =   $charge->userid;
                $user->username =   $charge->username;
                $user->usertname =   $charge->usertamilname;
                $user->desigtsname =   $charge->desigtlname;
                $user->lastlogin =   $charge->lastlogin;
                $user->email =   $charge->email;
                $user->mobilenumber =   $charge->mobilenumber;


                unset($charge->userid);
                unset($charge->username);
                unset($charge->lastlogin);
                unset($charge->email);
                unset($charge->mobilenumber);


                $menuQuery = DB::table('audit.rolemapping')
                    ->crossJoin(DB::raw("LATERAL jsonb_array_elements_text(menuid->'1') AS menu_id"))
                    ->join('audit.mst_menu', DB::raw('CAST(menu_id AS INTEGER)'), '=', 'audit.mst_menu.menuid')
                    ->select(
                        DB::raw('CAST(menu_id AS INTEGER) as value'),
                        'audit.mst_menu.menuurl as menuname'
                    )
                    ->where('audit.rolemapping.rolemappingid', $charge->rolemappingid);

                $baseResults = $menuQuery->get();

                if ($charge->roleactioncode == view::shared('AuditorRoleactioncode')) {
                    $selecttable = DB::table('audit.auditplanteammember')
                        ->where('teamhead', 'Y')
                        ->where('userid', $deptuserid)
                        ->get();

                    if ($selecttable->isNotEmpty()) {

                        $teamHeadMenus = DB::table('audit.mst_menu')
                            ->where('menuforteamhead', 'Y')
                            ->select(DB::raw('menuid as value'), 'menuurl as menuname')
                            ->get();

                        $baseResults = $baseResults->merge($teamHeadMenus);
                    }
                }

                $control_menu = $baseResults->pluck('value')->unique()->toArray();
                $menuurl = $baseResults->pluck('menuname')->unique()->filter()->values()->toArray();

                $charge->menu = $control_menu;
                $charge->menuname = $menuurl;

		 $user->loginid    =   $insertedId;

                session(['user' => $user]);
                session(['charge' => $charge]);
                // dd($charge);


                // Return success response with redirect URL
                return response()->json([
                    'success' => true,
                    'redirect_url' => url('/home'),   // Redirect to dashboard after successful login
                ]);
            }
            else {
                return response()->json([
                    'success' => false,
                    'message' => 'you have no charge. Contact administator',
                ]);
            }

        }

        $developer = DB::table('audit.dev_userdetails')
            ->where('email', $username)
            ->where('statusflag', 'Y')
            ->first();
            // dd($password);
        if ($developer && Hash::check($password, $developer->pwd)) {
// dd( $developer);
            $lastlogin_userdel = DB::table('audit.userlogindetails')
                ->where('userid', $developer->devuserid)
                ->orderByDesc('loginid')
                ->limit(1)
                ->first();

            $lastlogin_time = '';
            $devuserid = $developer->devuserid;
            // dd($lastlogin_userdel);
            if ($lastlogin_userdel) {
                $lastlogin_time = $lastlogin_userdel->logintime;
                DB::table('audit.userlogindetails')
                    ->where('userid', $developer->devuserid)
                    ->update([
                        'activestatus' => 'N',
                    ]);
            }

            $insertedId = DB::table('audit.userlogindetails')->insertGetId([
                'userid' => $developer->devuserid,
                'ipadd' => $request->ip(),
                'browser' => $request->userAgent(),
                'machinename' => gethostname(),
                'logintime' => View::shared('get_nowtime'),
                'activestatus' => 'Y'
            ], 'loginid');

            if ($lastlogin_time) {
                DB::table('audit.dev_userdetails')
                    ->where('devuserid', $devuserid)
                    ->update([
                        'lastlogin' => $lastlogin_time,
                    ]);
            }

            $charge = DB::table('audit.dev_userdetails as du')
                ->leftJoin('audit.chargedetails as c', 'c.chargeid', '=', 'du.chargeid')
                ->leftJoin('audit.rolemapping as rm', 'rm.rolemappingid', '=', 'du.rolemappingid')
                ->leftJoin('audit.roletypemapping as rtm', 'rtm.roletypemappingcode', '=', 'rm.roletypemappingcode')
                ->leftJoin('audit.mst_dept as de', 'de.deptcode', '=', 'c.deptcode')
                ->leftJoin('audit.mst_district as di', 'di.distcode', '=', 'c.distcode')
                ->leftJoin('audit.mst_region as r', 'r.regioncode', '=', 'c.regioncode')
                ->join('audit.mst_designation as d', 'd.desigcode', '=', 'du.desigcode')
                ->where('du.devuserid', $devuserid)
                ->select(
                    'du.chargeid',
                    'du.devename as username',
                    'du.devtname as usertamilname',
                    'du.email',
                    'du.mobilenumber',
                    'du.lastlogin',
                    'du.rolemappingid',
                    'c.chargedescription',
                    'c.deptcode',
                    'c.regioncode',
                    'c.distcode',
                    'de.deptesname',
                    'de.depttlname as depttsname',
                    'di.distename',
                    'di.disttname',
                    'r.regionename',
                    'r.regiontname',
                    'rtm.roletypecode',
                    'rm.roleactioncode',
                    'd.desigelname',
                    'd.desigcode',

                )
                ->first();
            if ($charge && $charge->rolemappingid) {
                $user = new \stdClass();
                $user->userid = $devuserid;
                $user->username = $charge->username;
                $user->usertname = $charge->usertamilname;
                $user->lastlogin = $charge->lastlogin;
                $user->email = $charge->email;
                $user->mobilenumber = $charge->mobilenumber;

                unset($charge->username);
                unset($charge->usertamilname);
                unset($charge->lastlogin);
                unset($charge->email);
                unset($charge->mobilenumber);

                $results = DB::table('audit.rolemapping')
                    ->crossJoin(DB::raw("LATERAL jsonb_array_elements_text(menuid->'1') AS menu_id"))
                    ->join('audit.mst_menu', DB::raw('CAST(menu_id AS INTEGER)'), '=', 'audit.mst_menu.menuid')
                    ->select(
                        DB::raw('CAST(menu_id AS INTEGER) as value'),
                        'audit.mst_menu.menuurl as menuname'
                    )
                    ->where('audit.rolemapping.rolemappingid', $charge->rolemappingid)
                    ->get();

                $charge->menu = $results->pluck('value')->unique()->toArray();
                $charge->menuname = $results->pluck('menuname')->unique()->filter()->values()->toArray();
                $charge->usertypecode = 'D';
               // $charge->userchargeid = $devuserid;

                $user->loginid = $insertedId;

                session(['user' => $user]);
                session(['charge' => $charge]);
                          // dd( $user);

                return response()->json([
                    'success' => true,
                    'redirect_url' => url('/home'),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'you have no charge. Contact administator',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Username or Password is incorrect',
            ]);
        }

        // If credentials are invalid, return error response
        // return response()->json([
        //     'success' => false,
        //     'message' => 'Invalid credentials, please try again.',
        //     // 'redirect_url' => url('/')  // Redirect back to login page
        // ], 401);
    }

    public function verifyOtp_login(Request $request)
    {
        $request->validate([
            'username' => 'required|email',
            'encryptedPassword' => 'required|min:6',
            'otp' => 'required|digits:6'
        ]);

        $username = $request->username;
        $encryptedPassword = $request->encryptedPassword;
        $password = CryptoHelper::decryptPassword($encryptedPassword);

        $user = DB::table('audit.deptuserdetails')
            ->where('email', $username)
            ->where('statusflag', 'Y')
            ->first();

        if (!$user || !Hash::check($password, $user->pwd)) {
            return response()->json([
                'success' => false,
                'message' => 'Username or Password is incorrect',
            ]);
        }

        $otpOk = SmsmailModel::verifyOTP_QT([
            'userid' => $user->deptuserid,
            'email' => $user->email,
            'otp' => $request->otp,
        ]);

        if (!$otpOk) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect OTP.',
            ]);
        }

        $lastlogin_userdel = DB::table('audit.userlogindetails')
            ->where('userid', $user->deptuserid)
            ->orderByDesc('loginid')
            ->limit(1)
            ->first();

        $lastlogin_time = '';
        $deptuserid = $user->deptuserid;

        if ($lastlogin_userdel) {
            $lastlogin_time = $lastlogin_userdel->logintime;
            DB::table('audit.userlogindetails')
                ->where('userid', $user->deptuserid)
                ->update([
                    'activestatus' => 'N',
                ]);
        }

        $nowTime = View::shared('get_nowtime');
        $loginDayCount = DB::table('audit.userlogindetails')
            ->where('userid', $deptuserid)
            ->where('userverifed_flag', 'Y')
            ->selectRaw('COUNT(DISTINCT DATE(logintime)) as cnt')
            ->value('cnt');
        $userVerifiedFlag = $loginDayCount < 10 ? 'Y' : 'N';

        $insertedId = DB::table('audit.userlogindetails')->insertGetId([
            'userid' => $user->deptuserid,
            'ipadd' => $request->ip(),
            'browser' => $request->userAgent(),
            'machinename' => gethostname(),
            'logintime' => $nowTime,
            'activestatus' => 'Y',
            'userverifed_flag' => $userVerifiedFlag
        ], 'loginid');

        DB::table('audit.userlogindetails')
            ->where('loginid', $insertedId)
            ->update([
                'userverifed_flag' => 'Y'
            ]);

        if ($lastlogin_time) {
            DB::table('audit.deptuserdetails')
                ->where('deptuserid', $deptuserid)
                ->update([
                    'lastlogin' => $lastlogin_time,
                ]);
        }

        $charge = DB::table('audit.userchargedetails as uc')
            ->join('audit.deptuserdetails as du', 'uc.userid', '=', 'du.deptuserid')
            ->join('audit.chargedetails as c', 'c.chargeid', '=', 'uc.chargeid')
            ->join('audit.rolemapping as rm', 'rm.rolemappingid', '=', 'c.rolemappingid')
            ->leftJoin('audit.roletypemapping as rtm', 'rtm.roletypemappingcode', '=', 'rm.roletypemappingcode')
            ->leftJoin('audit.mst_dept as de', 'de.deptcode', '=', 'c.deptcode')
            ->leftJoin('audit.mst_district as di', 'di.distcode', '=', 'c.distcode')
            ->leftJoin('audit.mst_region as r', 'r.regioncode', '=', 'c.regioncode')
            ->leftJoin('audit.auditor_instmapping as i', 'i.instmappingcode', '=', 'c.instmappingcode')
            ->leftJoin('audit.auditplanteammember as at', 'at.userid', '=', 'du.deptuserid')
            ->join('audit.mst_designation as d', 'd.desigcode', '=', 'du.desigcode')
            ->where('du.deptuserid', $deptuserid)
            ->where('uc.chargeflag', 'P')
            ->where('uc.statusflag', 'Y')
            ->where('du.statusflag', 'Y')
            ->select(
                'uc.chargeid',
                'uc.userid',
                'c.chargedescription',
                'c.deptcode',
                'c.regioncode',
                'rtm.usertypecode',
                'c.distcode',
                'c.desigcode',
                'd.desigelname',
                'd.desigtlname',
                'de.deptesname',
                'd.desigesname',
                'di.distsname',
                'r.regionename',
                'r.regiontname',
                'di.distename',
                'di.disttname',
                'd.desigelname',
                'du.username',
                'du.usertamilname',
                'du.email',
                'du.mobilenumber',
                'rtm.roletypecode',
                'rm.roleactioncode',
                'du.lastlogin',
                'rm.rolemappingid',
                'uc.userchargeid',
                'at.teamhead as auditteamhead'
            )
            ->first();

        if ($charge) {
            $userObj = new \stdClass();
            $userObj->userid = $charge->userid;
            $userObj->username = $charge->username;
            $userObj->usertname = $charge->usertamilname;
            $userObj->desigtsname = $charge->desigtlname;
            $userObj->lastlogin = $charge->lastlogin;
            $userObj->email = $charge->email;
            $userObj->mobilenumber = $charge->mobilenumber;

            unset($charge->userid);
            unset($charge->username);
            unset($charge->lastlogin);
            unset($charge->email);
            unset($charge->mobilenumber);

            $menuQuery = DB::table('audit.rolemapping')
                ->crossJoin(DB::raw("LATERAL jsonb_array_elements_text(menuid->'1') AS menu_id"))
                ->join('audit.mst_menu', DB::raw('CAST(menu_id AS INTEGER)'), '=', 'audit.mst_menu.menuid')
                ->select(
                    DB::raw('CAST(menu_id AS INTEGER) as value'),
                    'audit.mst_menu.menuurl as menuname'
                )
                ->where('audit.rolemapping.rolemappingid', $charge->rolemappingid);

            $baseResults = $menuQuery->get();
            $control_menu = $baseResults->pluck('value')->toArray();

            $userObj->loginid = $insertedId;
            $charge->menu = $control_menu;

            session(['user' => $userObj]);
            session(['charge' => $charge]);
            session(['auditee_otp_verified' => true]);
        }

        return response()->json([
            'success' => true,
            'redirect_url' => url('/home'),
        ]);
    }

    public function logout(Request $request)
    {
        $userData = session('user');
        $loginid = $userData->loginid;

        DB::table('audit.userlogindetails')
            ->where('loginid', $loginid)  // Condition to identify the record
            ->update([
                'activestatus' => 'N',  // Field and new value
                'logouttime' => View::shared('get_nowtime'),
            ]);

        // Check if the user is authenticated
        if (Auth::check()) {
            // // Log out the user
            Auth::logout();
        }

        // Clear all session data
        session()->flush();

        // Invalidate the session
        $request->session()->invalidate();

        // Regenerate the CSRF token to prevent session fixation
        $request->session()->regenerateToken();

        // Redirect to the login page with a logout success message
        return redirect('/')->with('message', 'You have been logged out successfully.');
    }

    // --------------forget password-------------------

    public function forgetpassword(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'email' => 'required|email',
                'user' => 'required|in:auditor,auditee,hodlogin',
                'captcha' => 'required|string'
            ]);

            if ($request->captcha !== Session::get('captcha_code')) {
                return response()->json([
                    'message' => __('validation.captcha'),
                    'errors' => [
                        'captcha' => [__('validation.captcha')]
                    ]
                ], 400);
            }

            $email = $request->email;
            $userType = $request->user;

            // Choose the correct table
            if ($userType === 'auditor') {
                $table = 'audit.deptuserdetails';
            } elseif ($userType === 'auditee') {
                $table = 'audit.audtieeuserdetails';
            } elseif ($userType === 'hodlogin') {
                $table = 'audit.auditee_dept';
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid user type'
                ], 400);
            }

            // Check if user exists
            $userExists = DB::table($table)->where('email', $email)->exists();

            if (!$userExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'email_exists'
                ], 400);
            }

            // Update password
            $newPassword = $this->generatePassword(8);
            DB::table($table)->where('email', $email)->update([
                'pwd' => bcrypt($newPassword),
                'profile_update' => 'Y',
            ]);

            $redirectUrl = match ($userType) {
                'auditor' => url('/login'),
                'auditee' => url('/auditeelogin'),
                'hodlogin' => url('/auditeeinstlogin'),
            };

            if ($userType === 'hodlogin') {
                $userRow = DB::table($table)->select('email')->where('email', $email)->first();
                $username = $userRow->email;
            } else {
                $userRow = DB::table($table)->select('username')->where('email', $email)->first();
                $username = $userRow->username;
            }

            // $username =$username->username;

            $data = ['email' => $email, 'username' => $username, 'redirect_url' => $redirectUrl, 'newpwd' => $newPassword];
            // print_r($data);exit;

            $Lang = 'en';
            $auditModel = new SmsmailModel(new SmsService(), new PHPMailerService());
            $sentsms = $auditModel->sendforgetpassword($data, $Lang);

            return response()->json([
                'success' => true,
                'message' => 'forget_success',
                'redirect' => $redirectUrl
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function auditee_validatelogin(Request $request)
    {
        // Validate user input
        $request->validate([
            'username' => 'required|email',  // Assuming username is an email
            'encryptedPassword' => 'required|min:6',  // Minimum password length
            'captcha' => 'required|string'
        ]);

        if ($request->captcha !== Session::get('captcha_code')) {
            return response()->json([
                'message' => __('validation.captcha'),
                'errors' => [
                    'captcha' => [__('validation.captcha')]
                ]
            ], 400);
        }

        $username = $request->username;

        $encryptedPassword = $request->encryptedPassword;

        // Manually call the helper function
        $password = CryptoHelper::decryptPassword($encryptedPassword);

        // Fetch user from the database (replace with your actual DB query)
        $user = DB::table('audit.audtieeuserdetails')->where('email', $username)->where('statusflag', 'Y')->first();

        // Check if user exists and password is correct
        // if ($user && Hash::check(value: $password, $user->pwd))

        // Check if user exists and password is correct
        if ($user && Hash::check($password, $user->pwd))
        // if ($user && $request->password === $user->pwd)
        {  // Compare hashed password

            $lastlogin_userdel = DB::table('audit.userlogindetails')
                ->where('userid', operator: $user->auditeeuserid)
                ->where('usertypecode', operator: 'I')
                ->orderByDesc('loginid')  // Order by ID in descending order
                ->limit(1)  // Limit to 1 record
                ->first();

            $lastlogin_time = '';
            $auditeeuserid = $user->auditeeuserid;

            if ($lastlogin_userdel) {
                $lastlogin_time = $lastlogin_userdel->logintime;
                DB::table('audit.userlogindetails')
                    ->where('userid', $auditeeuserid)  // Condition to identify the record
                    ->where('usertypecode', operator: 'I')
                    ->where('userverifed_flag', operator: 'Y')
                    ->update([
                        'activestatus' => 'N',  // Field and new value
                    ]);
            }

            $loginDayCount = DB::table('audit.userlogindetails')
                ->where('userid', $auditeeuserid)
                ->where('usertypecode', 'I')
                ->where('userverifed_flag', operator: 'Y')
                ->selectRaw('COUNT(DISTINCT DATE(logintime)) as cnt')
                ->value('cnt');

            // dd($loginDayCount);
            $userVerifiedFlag = $loginDayCount < 150 ? 'Y' : 'N';

            $nowTime = View::shared('get_nowtime');

            $insertedId = DB::table('audit.userlogindetails')->insertGetId([
                'userid' => $auditeeuserid,
                'ipadd' => $request->ip(),  // Get the server's IP address
                'browser' => $request->userAgent(),  // Get the user's browser information
                'machinename' => gethostname(),  // Get the server's hostname
                'logintime' => $nowTime,  // Current timestamp using Laravel's `now()` helper
                'activestatus' => 'Y',
                'usertypecode' => 'I',
                'userverifed_flag' => $userVerifiedFlag
            ], 'loginid');  // Specify 'loginid' as the primary key column

            // echo $user->deptuserid;

            if ($lastlogin_time) {
                DB::table('audit.audtieeuserdetails')
                    ->where('auditeeuserid', $auditeeuserid)  // Condition to identify the record
                    ->update([
                        'lastlogin' => $lastlogin_time,  // Field and new value
                    ]);
            }

            // echo $auditeeuserid;

            $charge = DB::table('audit.audtieeuserdetails as au')
                ->Join('audit.mst_institution as in', 'in.instid', '=', 'au.instid')  // Adjust the columns as needed
                ->leftJoin('audit.chargedetails as c', 'c.chargeid', '=', 'au.chargeid')
                ->leftJoin('audit.rolemapping as rm', 'rm.rolemappingid', '=', 'c.rolemappingid')
                ->leftJoin('audit.roletypemapping as rtm', 'rtm.roletypemappingcode', '=', 'rm.roletypemappingcode')
                ->leftJoin('audit.mst_dept as de', 'de.deptcode', '=', 'in.deptcode')
                ->leftJoin('audit.mst_district as di', 'di.distcode', '=', 'in.distcode')
                ->leftJoin('audit.mst_region as r', 'r.regioncode', '=', 'in.regioncode')
                // ->join('audit.mst_designation as d', 'd.desigcode', '=', 'c.desigcode')
                ->where('au.auditeeuserid', $auditeeuserid)
                // ->where('uc.statusflag','=','Y' )
                ->select('*')
                ->select(
                    'c.chargeid',
                    'au.auditeeuserid',
                    'c.chargedescription',
                    'in.deptcode',
                    'in.regioncode',
                    'in.distcode',
                    'de.deptesname',
                    'di.distsname',
                    'r.regionename',
                    'di.distename',
                    'in.instename',
                    'in.instid',
                    'au.username',
                    'au.email',
                    'au.mobilenumber',
                    'rtm.roletypecode',
                    'au.lastlogin',
                    'rm.rolemappingid',
                    'rtm.usertypecode'
                )  // Select all columns from both tables
                ->first();

            // print_r( $charge);

            $user = new \stdClass();  // Create a new instance of stdClass
            $user->userid = $charge->auditeeuserid;
            $user->username = $charge->username;
            $user->usertname = $charge->username;
            $user->lastlogin = $charge->lastlogin;
            $user->email = $charge->email;
            $user->mobilenumber = $charge->mobilenumber;
            $user->instename = $charge->instename;

            unset($charge->userid);
            unset($charge->username);
            unset($charge->lastlogin);
            unset($charge->email);
            unset($charge->mobilenumber);

            $results = DB::table('audit.rolemapping')
                ->select(DB::raw("jsonb_array_elements_text(menuid->'1') as value"))  // Extract values from the JSON array at key '1'
                ->where('rolemappingid', '=', $charge->rolemappingid)  // Add your condition here
                ->get();

            // Menu
            $control_menu = $results->pluck('value')->toArray();  // Plucks out the values as an array

            $user->loginid = $insertedId;
            $charge->menu = $control_menu;

            session(['user' => $user]);
            session(['charge' => $charge]);
            session(['auditee_otp_verified' => false]);

            // Return success response with redirect URL
            return response()->json([
                'success' => true,
                'redirect_url' => url('/home'),  // Redirect to dashboard after successful login
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Username or Password is incorrect',
            ]);
        }
    }

    public function verifyOtp_auditeelogin(Request $request)
    {
        $request->validate([
            'username' => 'required|email',
            'encryptedPassword' => 'required|min:6',
            'otp' => 'required|digits:6'
        ]);

        $username = $request->username;
        $encryptedPassword = $request->encryptedPassword;
        $password = CryptoHelper::decryptPassword($encryptedPassword);

        $user = DB::table('audit.audtieeuserdetails')
            ->where('email', $username)
            ->where('statusflag', 'Y')
            ->first();

        if (!$user || !Hash::check($password, $user->pwd)) {
            return response()->json([
                'success' => false,
                'message' => 'Username or Password is incorrect',
            ]);
        }

        $otpOk = SmsmailModel::verifyOTP_QT([
            'userid' => $user->auditeeuserid,
            'email' => $user->email,
            'otp' => $request->otp,
        ]);

        if (!$otpOk) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect OTP.',
            ]);
        }

        $lastlogin_userdel = DB::table('audit.userlogindetails')
            ->where('userid', $user->auditeeuserid)
            ->where('usertypecode', 'I')
            ->orderByDesc('loginid')
            ->limit(1)
            ->first();

        $lastlogin_time = '';
        $auditeeuserid = $user->auditeeuserid;

        if ($lastlogin_userdel) {
            $lastlogin_time = $lastlogin_userdel->logintime;
            DB::table('audit.userlogindetails')
                ->where('userid', $auditeeuserid)
                ->where('usertypecode', 'I')
                ->where('userverifed_flag', 'Y')
                ->update([
                    'activestatus' => 'N',
                ]);
        }

        $nowTime = View::shared('get_nowtime');
        $loginDayCount = DB::table('audit.userlogindetails')
            ->where('userid', $auditeeuserid)
            ->where('usertypecode', 'I')
            ->selectRaw('COUNT(DISTINCT DATE(logintime)) as cnt')
            ->value('cnt');
        $userVerifiedFlag = $loginDayCount < 10 ? 'Y' : 'N';

        $insertedId = DB::table('audit.userlogindetails')->insertGetId([
            'userid' => $auditeeuserid,
            'ipadd' => $request->ip(),
            'browser' => $request->userAgent(),
            'machinename' => gethostname(),
            'logintime' => $nowTime,
            'activestatus' => 'Y',
            'usertypecode' => 'I',
            'userverifed_flag' => $userVerifiedFlag
        ], 'loginid');

        if ($lastlogin_time) {
            DB::table('audit.audtieeuserdetails')
                ->where('auditeeuserid', $auditeeuserid)
                ->update([
                    'lastlogin' => $lastlogin_time,
                ]);
        }

        $charge = DB::table('audit.audtieeuserdetails as au')
            ->Join('audit.mst_institution as in', 'in.instid', '=', 'au.instid')
            ->leftJoin('audit.chargedetails as c', 'c.chargeid', '=', 'au.chargeid')
            ->leftJoin('audit.rolemapping as rm', 'rm.rolemappingid', '=', 'c.rolemappingid')
            ->leftJoin('audit.roletypemapping as rtm', 'rtm.roletypemappingcode', '=', 'rm.roletypemappingcode')
            ->leftJoin('audit.mst_dept as de', 'de.deptcode', '=', 'in.deptcode')
            ->leftJoin('audit.mst_district as di', 'di.distcode', '=', 'in.distcode')
            ->leftJoin('audit.mst_region as r', 'r.regioncode', '=', 'in.regioncode')
            ->where('au.auditeeuserid', $auditeeuserid)
            ->select('*')
            ->select(
                'c.chargeid',
                'au.auditeeuserid',
                'c.chargedescription',
                'in.deptcode',
                'in.regioncode',
                'in.distcode',
                'de.deptesname',
                'di.distsname',
                'r.regionename',
                'di.distename',
                'in.instename',
                'in.instid',
                'au.username',
                'au.email',
                'au.mobilenumber',
                'rtm.roletypecode',
                'au.lastlogin',
                'rm.rolemappingid',
                'rtm.usertypecode'
            )
            ->first();

        $userObj = new \stdClass();
        $userObj->userid = $charge->auditeeuserid;
        $userObj->username = $charge->username;
        $userObj->usertname = $charge->username;
        $userObj->lastlogin = $charge->lastlogin;
        $userObj->email = $charge->email;
        $userObj->mobilenumber = $charge->mobilenumber;
        $userObj->instename = $charge->instename;
        $userObj->loginid = $insertedId;

        unset($charge->userid);
        unset($charge->username);
        unset($charge->lastlogin);
        unset($charge->email);
        unset($charge->mobilenumber);

        $results = DB::table('audit.rolemapping')
            ->select(DB::raw("jsonb_array_elements_text(menuid->'1') as value"))
            ->where('rolemappingid', '=', $charge->rolemappingid)
            ->get();

        $control_menu = $results->pluck('value')->toArray();
        $charge->menu = $control_menu;

        session(['user' => $userObj]);
        session(['charge' => $charge]);
        session(['auditee_otp_verified' => true]);

        return response()->json([
            'success' => true,
            'redirect_url' => url('/home'),
        ]);
    }

    public function auditee_send_otp(Request $request)
    {
        $sessionUser = session('user');
        $sessionCharge = session('charge');

        $userType = $sessionCharge->usertypecode ?? null;
        $isAuditee = $userType === 'I';
        $isDeptOtpUser = false;
        if ($userType === 'A') {
            $otpRoleActionCodes = View::shared('otp_roleactioncodes') ?? [];
            $isDeptOtpUser = DB::table('audit.deptuserdetails as du')
                ->join('audit.userchargedetails as u', 'u.userid', '=', 'du.deptuserid')
                ->join('audit.chargedetails as c', 'c.chargeid', '=', 'u.chargeid')
                ->join('audit.rolemapping as rm', 'rm.rolemappingid', '=', 'c.rolemappingid')
                ->join('audit.mst_roleaction as ra', 'ra.roleactioncode', '=', 'rm.roleactioncode')
                ->where('du.statusflag', 'Y')
                ->where('u.statusflag', 'Y')
                ->where('u.chargeflag', 'P')
                ->where('rm.statusflag', 'Y')
                ->where('ra.statusflag', 'Y')
                ->whereIn('ra.roleactioncode', $otpRoleActionCodes)
                ->where('du.deptuserid', $sessionUser->userid)
                ->exists();
        }
        if (!$sessionUser || !$sessionCharge || (!$isAuditee && !$isDeptOtpUser)) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'mobile' => 'required|digits_between:10,15'
        ]);

        $sessionUser->mobilenumber = $request->mobile;
        session(['user' => $sessionUser]);

        // $otp = rand(100000, 999999);
        $otp = 123456;
        $data = [
            'userid' => $sessionUser->userid,
            'email' => $sessionUser->email,
            'otp' => $otp,
        ];

        $saveResult = SmsmailModel::saveOTP($data);
        if ($saveResult['status'] !== 'success') {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send OTP. Please try again later.'
            ], 500);
        }

        $mobileNumber = $request->mobile;
        $smsResponse = $this->smsService->sendSMS($mobileNumber, $otp, null, 'OTP_mobile');
        // dd($smsResponse);
        if (is_array($smsResponse) && isset($smsResponse['status']) && (string) $smsResponse['status'] === '100') {
            session(['auditee_otp_verified' => false]);
            return response()->json([
                'status' => 'success',
                'message' => 'OTP has been sent successfully.'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to send OTP. Please try again later.'
        ], 500);
    }

    public function auditee_verify_otp(Request $request)
    {
        $sessionUser = session('user');
        $sessionCharge = session('charge');

        $userType = $sessionCharge->usertypecode ?? null;
        $isAuditee = $userType === 'I';
        $isDeptOtpUser = false;
        if ($userType === 'A') {
            $otpRoleActionCodes = View::shared('otp_roleactioncodes') ?? [];
            $isDeptOtpUser = DB::table('audit.deptuserdetails as du')
                ->join('audit.userchargedetails as u', 'u.userid', '=', 'du.deptuserid')
                ->join('audit.chargedetails as c', 'c.chargeid', '=', 'u.chargeid')
                ->join('audit.rolemapping as rm', 'rm.rolemappingid', '=', 'c.rolemappingid')
                ->join('audit.mst_roleaction as ra', 'ra.roleactioncode', '=', 'rm.roleactioncode')
                ->where('du.statusflag', 'Y')
                ->where('u.statusflag', 'Y')
                ->where('u.chargeflag', 'P')
                ->where('rm.statusflag', 'Y')
                ->where('ra.statusflag', 'Y')
                ->whereIn('ra.roleactioncode', $otpRoleActionCodes)
                ->where('du.deptuserid', $sessionUser->userid)
                ->exists();
        }
        if (!$sessionUser || !$sessionCharge || (!$isAuditee && !$isDeptOtpUser)) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $data = [
            'userid' => $sessionUser->userid,
            'email' => $sessionUser->email,
            'otp' => $request->otp,
        ];

        $storedOtp = SmsmailModel::verifyOTP_QT($data);

        if ($storedOtp) {
            if (!empty($sessionUser->mobilenumber)) {
                if ($isAuditee) {
                    DB::table('audit.audtieeuserdetails')
                        ->where('auditeeuserid', $sessionUser->userid)
                        ->update([
                            'mobilenumber' => $sessionUser->mobilenumber,
                            'mob_verify' => 'Y'
                        ]);
                } else {
                    DB::table('audit.deptuserdetails')
                        ->where('deptuserid', $sessionUser->userid)
                        ->update([
                            'mobilenumber' => $sessionUser->mobilenumber,
                            'mob_verify' => 'Y'
                        ]);
                }
            }
            if (!empty($sessionUser->loginid)) {
                DB::table('audit.userlogindetails')
                    ->where('loginid', $sessionUser->loginid)
                    ->update([
                        'userverifed_flag' => 'Y'
                    ]);
            }
            session(['auditee_otp_verified' => true]);
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error', 'message' => 'Incorrect OTP']);
    }

    public function auditeeinst_validatelogin(Request $request)
    {
        // Validate user input
        $request->validate([
            'username' => 'required|email',  // Assuming username is an email
            'encryptedPassword' => 'required|min:6',  // Minimum password length
            'captcha' => 'required|string'
        ]);

        if ($request->captcha !== Session::get('captcha_code')) {
            return response()->json([
                'message' => __('validation.captcha'),
                'errors' => [
                    'captcha' => [__('validation.captcha')]
                ]
            ], 400);
        }

        $username = $request->username;

        $encryptedPassword = $request->encryptedPassword;

        // Manually call the helper function
        $password = CryptoHelper::decryptPassword($encryptedPassword);

        // Fetch user from the database (replace with your actual DB query)
        $user = DB::table('audit.auditee_dept')->where('email', $username)->first();

        // Check if user exists and password is correct
        // if ($user && Hash::check(value: $password, $user->pwd))

        // Check if user exists and password is correct
        if ($user && Hash::check($password, $user->pwd))
        // if ($user && $request->password === $user->pwd)
        {  // Compare hashed password

            $lastlogin_userdel = DB::table('audit.userlogindetails')
                ->where('userid', operator: $user->auditeedeptid)
                ->where('usertypecode', operator: 'H')
                ->orderByDesc('loginid')  // Order by ID in descending order
                ->limit(1)  // Limit to 1 record
                ->first();

            $lastlogin_time = '';
            $auditeedeptid = $user->auditeedeptid;

            // print_r($auditeedeptid);

            if ($lastlogin_userdel) {
                $lastlogin_time = $lastlogin_userdel->logintime;
                DB::table('audit.userlogindetails')
                    ->where('userid', $auditeedeptid)  // Condition to identify the record
                    ->where('usertypecode', operator: 'I')
                    ->update([
                        'activestatus' => 'N',  // Field and new value
                    ]);
            }

            $insertedId = DB::table('audit.userlogindetails')->insertGetId([
                'userid' => $auditeedeptid,
                'ipadd' => $request->ip(),  // Get the server's IP address
                'browser' => $request->userAgent(),  // Get the user's browser information
                'machinename' => gethostname(),  // Get the server's hostname
                'logintime' => View::shared('get_nowtime'),  // Current timestamp using Laravel's `now()` helper
                'activestatus' => 'Y',
                'usertypecode' => 'H'  // Active status set to 'Y'
            ], 'loginid');  // Specify 'loginid' as the primary key column

            // echo $user->deptuserid;

            if ($lastlogin_time) {
                DB::table('audit.auditee_dept')
                    ->where('auditeedeptid', $auditeedeptid)  // Condition to identify the record
                    ->update([
                        'lastlogin' => $lastlogin_time,  // Field and new value
                    ]);
            }

            // exit;

            $charge = DB::table('audit.hoduserchargedetails as uc')
                ->join('audit.auditee_dept as du', 'uc.userid', '=', 'du.auditeedeptid')  // Adjust the columns as needed
                ->join('audit.hodchargedetails as c', 'c.chargeid', '=', 'uc.chargeid')
                ->join('audit.rolemapping as rm', 'rm.rolemappingid', '=', 'c.rolemappingid')
                ->leftJoin('audit.roletypemapping as rtm', 'rtm.roletypemappingcode', '=', 'rm.roletypemappingcode')
                ->leftJoin('audit.mst_dept as de', 'de.deptcode', '=', 'c.deptcode')
                ->leftJoin('audit.mst_auditeedept as ade', 'ade.auditeedeptcode', '=', 'c.auditeedeptcode')
                ->join('audit.mst_designation as d', 'd.desigcode', '=', 'du.designation')
                ->where('du.auditeedeptid', $auditeedeptid)
                ->where('uc.chargeflag', 'P')
                ->where('uc.statusflag', '=', 'Y')
                ->where('du.statusflag', '=', 'Y')
                // ->where('uc.statusflag','=','Y' )
                ->select(
                    'ade.auditeedeptename',
                    'ade.auditeedepttname',
                    'c.auditeedeptcode',
                    'uc.chargeid',
                    'uc.userid',
                    'c.chargedescription',
                    'c.deptcode',
                    // 'c.regioncode',
                    'rtm.usertypecode',
                    // 'c.distcode',
                    'c.desigcode',
                    'd.desigelname',
                    'd.desigtlname',
                    'de.deptesname',
                    'de.depttlname',
                    'de.deptelname',
                    'de.deptesname',
                    'd.desigesname',
                    // 'di.distsname',
                    // 'r.regionename',
                    // 'r.regiontname',
                    // 'di.distename',
                    // 'di.disttname',
                    'du.instid',
                    'd.desigelname',
                    // 'du.username',
                    // 'du.usertamilname',
                    'du.email',
                    'du.auditeedeptid',
                    'du.auditeeins_subcategoryid',
                    'du.catcode',
                    'du.mobilenumber',
                    'rtm.roletypecode',
                    'rm.roleactioncode',
                    'du.lastlogin',
                    'rm.rolemappingid',
                    'uc.userchargeid',
                    //  'at.teamhead as auditteamhead',
                )  // Select all columns from both tables
                ->first();

            // print_r($charge);

            // exit;

            $user = new \stdClass();  // Create a new instance of stdClass
            $user->userid = $charge->auditeedeptid;
            // $user->username =   $charge->housername;
            $user->lastlogin = $charge->lastlogin;
            $user->email = $charge->email;
            $user->mobilenumber = $charge->mobilenumber;
            $charge->chargeid = $charge->chargeid;

            // unset($charge->chargeid);
            unset($charge->auditeedeptid);
            // unset($charge->housername);
            unset($charge->lastlogin);

            unset($charge->email);
            unset($charge->mobilenumber);

            $results = DB::table('audit.rolemapping')
                ->select(DB::raw("jsonb_array_elements_text(menuid->'1') as value"))  // Extract values from the JSON array at key '1'
                ->where('rolemappingid', '=', $charge->rolemappingid)  // Add your condition here
                ->get();

            // Menu
            $control_menu = $results->pluck('value')->toArray();  // Plucks out the values as an array

            $user->loginid = $insertedId;
            $charge->menu = $control_menu;

            session(['user' => $user]);
            session(['charge' => $charge]);

            // Return success response with redirect URL
            return response()->json([
                'success' => true,
                'redirect_url' => url('/home'),  // Redirect to dashboard after successful login
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Username or Password is incorrect',
            ]);
        }
    }
}
