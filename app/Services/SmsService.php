<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;  // If you're using a service like Twilio, Nexmo, etc.

class SmsService
{
    protected $smsApiUrl = 'https://tmegov.onex-aura.com/api/sms'; // Replace with the actual API endpoint
    protected $apiKey = '8uWr4sBj';  // Replace with your API key

    /**
     * Send SMS to the provided number.
     *
     * @param string $to
     * @param string $message
     * @return mixed
     */
    // public function sendSms($to, $message)
    // {
    //     // Example: Using an HTTP request to a third-party SMS API
    //     $response = Http::post($this->smsApiUrl, [
    //         'api_key' => $this->apiKey,
    //         'to' => $to,
    //         'message' => $message,
    //     ]);

        

    //     return $response->json();  // Return response as JSON or whatever format your API gives
    // }

    public function sendSMS($mobileNumber,$otp,$data=null,$page,$Lang=null) 
    {
        // $url = "https://tmegov.onex-aura.com/api/sms"; 
        // $apiKey = "8uWr4sBj";

	        if($page == 'login')
	        {
	            $content = "Dear User, your password reset request has been received. Use OTP ".$otp." to reset your password. This OTP is valid for 5 minutes. - CAMS";
	            $templateID = "1007758064057327120";
	        }else if($page == 'OTP_mobile')
	        {
	            $content = "Dear User, your password reset request has been received. Use OTP ".$otp." to reset your password. This OTP is valid for 5 minutes. - CAMS";
	            $templateID = "1007758064057327120";
	        }else if($page == 'sent_intimation')
	        {
            if($Lang == 'ta')
            {
                /*$content = "{#var#} ???????????? {#var#} ????? ????? ????????????????????. ??????? ??????? ???????? ??????? ?????????? ??????????? ?????? ???????? ?????????? ?????????????????.  - CAMS";
                $templateID = "1007095212377841526";*/

            }else
            {
                $content = "Audit of ".$data['inst_name']." is scheduled on ".$data['fromdate'].". Kindly ensure all necessary documents and records are updated and kept ready for audit. - CAMS";
                $templateID = "1007095212377841526";

            }
           

        }else if($page == 'sent_exitmeeting')
        {
            if($Lang == 'ta')
            {
                $content = "??????? ???????? ??????? ??????????? ".$data['exitmeetdate']." ????? ??????????? ?????? ?????????? ???????. ????????????? ".$data['pendingslip']." ??????? ????????????? 2 ?????????????? ???? ???????? ?????????. - CAMS";
                $templateID = "1007912290047513846";

            }else
            {
                $content = "Your Institution audit has been completed, and the audit exit meeting was held on ".$data['exitmeetdate'].". Please provide appropriate reply to the pending -".$data['pendingslip']." audit slips within 5 days. - CAMS";
                $templateID = "1007047424708209720";

            }

        }

        
        $senderID = "DGCAMS";
        $entityID = "1001227948943862859";

        $postData = [
            "key" => $this->apiKey,
            "to" => $mobileNumber,
            "from" => $senderID,
            "body" => $content,
            "entityid" => $entityID,
            "templateid" => $templateID
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->smsApiUrl . '?' . http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        // Execute and get response
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);


        //return Array ( "status" => 100,"description" => "Message submitted with tracking id ( UID )","messageid" => "2utajM2RMEkiiWRuF3TWwbgd74M" ) ;

        // // Debugging the response
        if ($http_code == 200) {
         $decoded_response = json_decode($response, true);
        if ($decoded_response) {
           return $decoded_response; // Return the decoded response
        } else {
            return ["status" => "error", "message" => "Invalid JSON response", "raw" => $response];
       }
       } else {
         return ["status" => "error", "message" => "HTTP Code: $http_code", "error" => $error];
        }
    }
       


}



?>
