<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Http\Request;
use Carbon\Carbon;
use File;
use App\Models\AuditDiaryModel;
use App\Http\Controllers\AuditSlipController;
use App\Models\InstAuditscheduleModel;
use App\Models\TransWorkAllocationModel;
use App\Models\ReportModel;
use PhpOffice\PhpWord\Shared\Html;

use Illuminate\Support\Facades\DB;

class WordController extends Controller
{

    public function finalize_auditreport(Request $request)
    {
        try {
            $data['iframeContent'] = trim($request->input('iframeContent'));
            $data['activeStep'] = $request->input('activeStep');
            $data['activeStepNo'] = $request->input('activeStepNo');


            if (empty($data)) {
                return response()->json(['error' => 'No content provided'], 400);
            }

            // Call the model function to store content
            ReportModel::storeReport($data);

           // Return a success response
            return response()->json([
                'status' => 'success',
                'message' => 'Content saved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }


    }

    public function exitmeeting_editreport()
    {

        try {
            $fontName = 'Times New Roman';
            $defaultsize = 13;

            // Step 1: Load the HTML content from an HTML file
            $htmlFilePath2 = base_path('resources/views/pdf/entryorexitmeeting.html');
            $fileFromTemplate = true; // Flag to track if content is from file

            if (!File::exists($htmlFilePath2)) {
                return response()->json(['error' => 'HTML file not found at ' . $htmlFilePath2], 404);
            }

            // Step 2: Check if report exists
            $report = ReportModel::where('report_type', '5')->latest()->first();
            $htmlContent ='';


            if ($report && !empty($report->report_contents)) {
                $reportContent = json_decode($report->report_contents, true);
                if (isset($reportContent['content']))
                {
                     $htmlContent .= ($reportContent['content']);

                    $fileFromTemplate = false;
                }
            }else
            {
                $htmlContent .= '<h4>5. EXIT MEETING</h4>';

            }

             // If no report found, use template file content
             if ($fileFromTemplate) {
                $htmlContent .= File::get($htmlFilePath2);
            }


              // Load content from the JSON file
              $jsonFilePath = public_path('json/pdfcontent.json');
              $jsonContent = file_get_contents($jsonFilePath);
              $data = json_decode($jsonContent, true);
              $data = mb_convert_encoding($data, 'UTF-8', 'auto');


              $language = 'en'; // en or ta
              $title = $data['exitpdfword_' . $language]['title'];
              $tablecontents = $data['exitpdfword_' . $language];

              unset($tablecontents['title']);

              $tabledata = '';
              $sno = 1;
              $x = 0;

              $ValuesEcho = array('', '','','', '', '','', '', '', '', '', '', '', '');
              foreach ($tablecontents as $tablekey => $tableval)
              {
                  $tabledata .= '<tr><td class="lang">' . $sno . '</td><td class="lang">' . $tableval . '</td><td class="lang">:</td><td class="fillupfield englishcontent">' . (isset($ValuesEcho[$x]) ? $ValuesEcho[$x] : '') . '</td></tr>';
                  $sno++;
                  $x++;

              }

              $dynamicData = [
                                'heading_title' => $title,
                                'fontFamily' => $fontName,
                                'tabledata' => $tabledata
                                ];

              foreach ($dynamicData as $key => $value) {
              $htmlContent = str_replace('{{' . $key . '}}', $value, $htmlContent);
              }



             // Step 4: Make content editable & apply necessary formatting
             $htmlContent = $this->makeEditable($htmlContent);

             if ($fileFromTemplate)
             {

                 $htmlContent = $this->addBordersToHtml($htmlContent);

             }



            return response()->json([
                'res' => 'success',
                'html' => $htmlContent
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in intimationletter: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }

    }



    public function codeofethics_editreport()
    {
        try {
            $fontName = 'Times New Roman';
            $defaultsize = 13;

            // Step 1: Load the HTML content from an HTML file
            $htmlFilePath2 = base_path('resources/views/pdf/codeofethics.html');

            if (!File::exists($htmlFilePath2)) {
                return response()->json(['error' => 'HTML file not found at ' . $htmlFilePath2], 404);
            }

            // Step 2: Check if report exists
            $report = ReportModel::where('report_type', '3')->latest()->first();
            $htmlContent = '';
            $fileFromTemplate = true; // Flag to track if content is from file

            if ($report && !empty($report->report_contents))
            {
                $reportContent = json_decode($report->report_contents, true);
                if (isset($reportContent['content']))
                {
                     $htmlContent = ($reportContent['content']);

                    $fileFromTemplate = false;
                }
            }

            // If no report found, use template file content
            if ($fileFromTemplate) {
                $htmlContent = File::get($htmlFilePath2);
            }


            $dynamicData = [
                'name'=>'[Name]',
                'designation'=>'[Designation]',
                'currentdate' =>'[Current Date]'
            ];

            // Replace placeholders with dynamic values
            foreach ($dynamicData as $key => $value)
            {
                // Replace {{key}} with actual values
                $htmlContent = str_replace('{{' . $key . '}}', $value, $htmlContent);

            }

             // Step 4: Make content editable & apply necessary formatting
             $htmlContent = $this->makeEditable($htmlContent);

            if ($fileFromTemplate)
            {

                $htmlContent = $this->addBordersToHtml($htmlContent);

            }

            return response()->json([
                'res' => 'success',
                'html' => $htmlContent
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in intimationletter: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }

    }

    public function entrymeeting_editreport()
    {

        try {
            $fontName = 'Times New Roman';
            $defaultsize = 13;

            // Step 1: Load the HTML content from an HTML file
            $htmlFilePath2 = base_path('resources/views/pdf/entryorexitmeeting.html');
            $fileFromTemplate = true; // Flag to track if content is from file

            if (!File::exists($htmlFilePath2)) {
                return response()->json(['error' => 'HTML file not found at ' . $htmlFilePath2], 404);
            }

            // Step 2: Check if report exists
            $report = ReportModel::where('report_type', '2')->latest()->first();
            $htmlContent ='';


            if ($report && !empty($report->report_contents)) {
                $reportContent = json_decode($report->report_contents, true);
                if (isset($reportContent['content']))
                {
                     $htmlContent .= ($reportContent['content']);

                    $fileFromTemplate = false;
                }
            }else
            {
                $htmlContent .= '<h4>2. ENTRY MEETING</h4>';

            }

             // If no report found, use template file content
             if ($fileFromTemplate) {
                $htmlContent .= File::get($htmlFilePath2);
            }


              // Load content from the JSON file
              $jsonFilePath = public_path('json/pdfcontent.json');
              $jsonContent = file_get_contents($jsonFilePath);
              $data = json_decode($jsonContent, true);
              $data = mb_convert_encoding($data, 'UTF-8', 'auto');


              $language = 'en'; // en or ta
              $title = $data['entrypdfword_' . $language]['title'];
              $tablecontents = $data['entrypdfword_' . $language];

              unset($tablecontents['title']);

              $tabledata = '';
              $sno = 1;
              $x = 0;

              $ValuesEcho = array('', '','','', '', '','', '', '', '', '', '', '', '');
              foreach ($tablecontents as $tablekey => $tableval)
              {
                  $tabledata .= '<tr><td class="lang">' . $sno . '</td><td class="lang">' . $tableval . '</td><td class="lang">:</td><td class="fillupfield englishcontent">' . (isset($ValuesEcho[$x]) ? $ValuesEcho[$x] : '') . '</td></tr>';
                  $sno++;
                  $x++;

              }

              $dynamicData = [
                                'heading_title' => $title,
                                'fontFamily' => $fontName,
                                'tabledata' => $tabledata
                                ];

              foreach ($dynamicData as $key => $value) {
              $htmlContent = str_replace('{{' . $key . '}}', $value, $htmlContent);
              }



             // Step 4: Make content editable & apply necessary formatting
             $htmlContent = $this->makeEditable($htmlContent);

             if ($fileFromTemplate)
             {

                 $htmlContent = $this->addBordersToHtml($htmlContent);

             }



            return response()->json([
                'res' => 'success',
                'html' => $htmlContent
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in intimationletter: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }

    }

    public function intimationletter()
    {
        try {
            // Step 1: Load the HTML content from the template file
            $htmlFilePath = base_path('resources/views/pdf/intimationletter.html');

            if (!File::exists($htmlFilePath)) {
                return response()->json(['error' => 'HTML file not found at ' . $htmlFilePath], 404);
            }

            // Step 2: Check if report exists
            $report = ReportModel::where('report_type', '1')->latest()->first();
            $htmlContent = '';
            $fileFromTemplate = true; // Flag to track if content is from file

            if ($report && !empty($report->report_contents)) {
                $reportContent = json_decode($report->report_contents, true);
                if (isset($reportContent['content'])) {
                $htmlContent = ($reportContent['content']);
                    //$htmlContent = html_entity_decode($reportContent['content'], ENT_QUOTES, 'UTF-8');

                // $htmlContent = mb_convert_encoding($htmlContent, 'UTF-8', 'auto');

                    $fileFromTemplate = false;
                }
            }

            // If no report found, use template file content
            if ($fileFromTemplate) {
                $htmlContent = File::get($htmlFilePath);
            }

            // Step 3: Define placeholders & replace them with default values
            $placeholders = [
                'from_name' => '[From Name]',
                'from_desig' => '[From Designation]',
                'from_location' => '[From Location]',
                'audit_fromdate' => '[Audit Start Date]',
                'currentdate' => '[Current Date]',
                'to_name'=>'[To Name]',
                'to_desig'=>'[To Designation]',
                'to_location'=>'[To Location]'

            ];

            foreach ($placeholders as $key => $value) {
                $htmlContent = str_replace('{{' . $key . '}}', $value, $htmlContent);
            }

            // Step 4: Make content editable & apply necessary formatting
            $htmlContent = $this->makeEditable($htmlContent);

            if ($fileFromTemplate)
            {

                $htmlContent = $this->addBordersToHtml($htmlContent);

            }


            // Step 5: Return formatted HTML response
            return response()->json([
                'res' => 'success',
                'html' => $htmlContent
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in intimationletter: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }


    public function previewWordFile()
    {
        try {
            $fontName ='Times New Roman';
            $defaultsize =13;
            // Step 1: Load the HTML content from an HTML file (this can be from a file or a string)
            $htmlFilePath1 =  public_path('files/sample.html'); // Path to the first HTML file
            $htmlFilePath2 =  resource_path('views/pdf/intimationletter.html');
            $htmlFilePath3 =  resource_path('views/pdf/entryorexitmeeting.html');
            $htmlFilePath4 =  resource_path('views/pdf/codeofethics.html');
            //$htmlFilePath5 =  resource_path('views/pdf/partb_contents.html');
            //$htmlFilePath_SubHead =  resource_path('views/pdf/subheading.html'); // Path to the first HTML file
            $htmlFilePath6 =  resource_path('views/pdf/workallocation.html');

            /**First Page Content**/
            $chargeData = session('charge');
            $userData = session('user');
            $session_userid = $userData->userid;



            $userdetailsfetch=AuditDiaryModel::userdetails_fetch();
            //$scheduleId = Crypt::decryptString($_GET['scheduleid']);
            $scheduleId = $_GET['scheduleid'];
            $auditscheduleid = $scheduleId;

            $workAllocation = TransWorkAllocationModel::fetch_allocatedwork($auditscheduleid);

            $WorkingOfficeGet=InstAuditscheduleModel::GetSchedultedEventDetails($auditscheduleid);
            $auditeamid=$WorkingOfficeGet->auditteamid;
            if($auditscheduleid)
            {
                    $TeammemberGet = DB::table('audit.auditplanteammember as apt')
                                        ->join('audit.deptuserdetails as dud', 'apt.userid', '=', 'dud.deptuserid')  // Join with deptuserdetails on userid
                                        ->join('audit.mst_designation as msd', 'dud.desigcode', '=', 'msd.desigcode')  // Join with mst_designation on designation
                                        ->where('apt.auditplanteamid', $auditeamid)  // Filter by auditplanteamid
                                        //->where('apt.teamhead', '!=', 'Y')  // Filter by teamhead not equal to 'Y'
                                        ->select('apt.userid', 'dud.username', 'msd.desigelname')  // Select userid, username, and designationname from mst_designation
                                        ->orderBy('dud.desigcode', 'asc')
                                        ->orderBy('dud.deptuserid', 'asc')
                                        ->get();  // Retrieve the results as a collection

                    $InstituteName =$WorkingOfficeGet->instename;
                    $TypeofAudit =$WorkingOfficeGet->typeofauditename;
                    $FinancialYear =$WorkingOfficeGet->yearname;

                    // Create a new PhpWord object
                    $phpWord = new PhpWord();

                    // Define custom width for the section
                    $customWidth = 10000;  // Custom width for the section (adjust as needed)
                    $yearWidth = 6000;     // Width for the year text (narrower than the main text)

                    // Create a section
                    $section = $phpWord->addSection([
                    'align' => 'center',  // Center-align the entire section
                    'borderSize' => 26,   // Border size around the content
                    'borderColor' => '000000', // Border color (black)
                    'width' => $customWidth,      // Set custom width for the section
                    'height' => 8000
                    ]);


                    // Step 1: Add the department name (big and bold)
                    $section->addText(
                    $WorkingOfficeGet->deptelname,
                    ['name' => $fontName, 'size' => 28, 'bold' => true], // Large, bold text
                    ['align' => 'center', 'lineHeight' => 2, 'spaceBefore' => 1040] // Center-align the text
                    );

                    // Step 2: Add the image (below the department name)
                    $imagePath = public_path('site/image/tn__logo.png');  // Adjust path if needed

                    if (file_exists($imagePath)) {
                    $section->addImage($imagePath, [
                    'width' => 200,  // Set image width
                    'height' => 200, // Set image height
                    'align' => 'center', // Center-align the image
                    'marginTop' => 20

                    ]);
                    } else {
                        // Handle case where the image does not exist
                        \Log::error('Image not found: ' . $imagePath);
                    }

                    // Step 3: Add the Audit Office and year (big text)
                    $section->addText(
                    $WorkingOfficeGet->distename .' District',
                    ['name' => $fontName, 'size' => 20, 'bold' => true], // Large and bold text for Audit Office
                    ['align' => 'center', 'lineHeight' => 2, 'spaceBefore' => 640] // Center-align and add some space before the text
                    );

                    $section->addText(
                    $InstituteName,
                    ['name' => $fontName, 'size' => 28, 'bold' => true], // Large and bold text for Audit Office
                    ['align' => 'center', 'lineHeight' => 2] // Center-align and add some space before the text
                    );



                    // Step 4: Add the year (below the Audit Office)
                    $section->addText($FinancialYear .' Year of',
                    ['name' => $fontName, 'size' => 24, 'bold' => true], // Slightly smaller, italic text for year
                    ['align' => 'center', 'lineHeight' => 1.5, 'spaceBefore' => 70, 'width' => $yearWidth] // Center-align the text
                    );

                    $section->addText('Audit Report',
                    ['name' => $fontName, 'size' => 24, 'bold' => true], // Slightly smaller, italic text for year
                    ['align' => 'center', 'lineHeight' => 2, 'spaceBefore' => 70, 'width' => $yearWidth] // Center-align the text
                    );

                    $section->addPageBreak();

                    $htmlContent='';
                    if (File::exists($htmlFilePath1)) {
                    $htmlContent .= File::get($htmlFilePath1);
                    // Add a new section with border for this content
                    $section1 = $phpWord->addSection([
                    'borderSize' => 16,   // Border size around the content
                    'borderColor' => '000000', // Border color (black)
                    'width' => 12000,     // Optional: Control section width
                    'height' => 8000      // Optional: Control section height
                    ]);

                    $this->parseHtmlToWord($section1, $htmlContent); // Parse HTML to Word for the first file content

                    // Add a page break after this section
                    // $section1->addPageBreak();

                    } else
                    {
                        return response()->json(['error' => 'HTML file not found at ' . $htmlFilePath1], 404);
                    }

                    // Check and load the intimationletter file
                    if (File::exists($htmlFilePath2))
                    {
                        $report = ReportModel::where('report_type', '1')->latest()->first();
                        $htmlContent = '';
                        $fileFromTemplate = true; // Flag to track if content is from file

                        if ($report && !empty($report->report_contents))
                        {
                            $reportContent = json_decode($report->report_contents, true);
                            if (isset($reportContent['content'])) {
                            $htmlContent = ($reportContent['content']);
                                //$htmlContent = html_entity_decode($reportContent['content'], ENT_QUOTES, 'UTF-8');

                            // $htmlContent = mb_convert_encoding($htmlContent, 'UTF-8', 'auto');

                                $fileFromTemplate = false;
                            }
                        }

                        if ($fileFromTemplate)
                        {
                            $htmlContent .= File::get($htmlFilePath2);
                        }

                        $dynamicData = [
                        'From Name'=>$WorkingOfficeGet->username,
                        'From Designation'=>$WorkingOfficeGet->desigelname,
                        'From Location'=>$WorkingOfficeGet->distename,
                        'Audit Start Date'=>date('d-m-Y',strtotime($WorkingOfficeGet->fromdate)),
                        'Current Date' =>date('d-m-Y')
                        ];

                        // Replace placeholders with dynamic values
                        foreach ($dynamicData as $key => $value)
                        {
                            // Replace {{key}} with actual values
                            $htmlContent = str_replace('[' . $key . ']', $value, $htmlContent);
                        }

                        $htmlContent = preg_replace('/<style.*?<\/style>/is', '', $htmlContent);

                        // Add a new section with border for this content
                        $section2 = $phpWord->addSection([
                        'borderSize' => 16,   // Border size around the content
                        'borderColor' => '000000', // Border color (black)
                        'width' => 12000,     // Optional: Control section width
                        'height' => 8000      // Optional: Control section height
                        ]);

                        $this->parseHtmlToWord($section2, $htmlContent); // Parse HTML to Word for the second file content

                        // Add a page break after this section
                        $section2->addPageBreak();

                    } else
                    {
                       return response()->json(['error' => 'HTML file not found at ' . $htmlFilePath2], 404);
                    }

                    // Check and load the entry meeting file
                    if (File::exists($htmlFilePath3))
                    {
                        $htmlContent = '<h4>2. ENTRY MEETING</h4>';
                        $htmlContent .= File::get($htmlFilePath3);

                        // Load content from the JSON file
                        $jsonFilePath = public_path('json/pdfcontent.json');
                        $jsonContent = file_get_contents($jsonFilePath);
                        $data = json_decode($jsonContent, true);
                        $data = mb_convert_encoding($data, 'UTF-8', 'auto');


                        $language = 'en'; // en or ta
                        $title = $data['entrypdfword_' . $language]['title'];
                        $tablecontents = $data['entrypdfword_' . $language];

                        unset($tablecontents['title']);

                        $tabledata = '';
                        $sno = 1;
                        $x = 0;

                        $ValuesEcho = array($InstituteName, $FinancialYear,'', date('d-m-Y',strtotime($WorkingOfficeGet->fromdate)), $WorkingOfficeGet->teamname, 'Test',$WorkingOfficeGet->mandays, '', '', '', 'Test', '', 'Test', 'Test');
                        foreach ($tablecontents as $tablekey => $tableval)
                        {
                            $tabledata .= '<tr><td class="lang">' . $sno . '</td><td class="lang">' . $tableval . '</td><td class="lang">:</td><td class="fillupfield englishcontent">' . (isset($ValuesEcho[$x]) ? $ValuesEcho[$x] : '') . '</td></tr>';
                            $sno++;
                            $x++;

                        }

                        $dynamicData = [
                        'heading_title' => $title,
                        'fontFamily' => $fontName,
                        'tabledata' => $tabledata
                        ];

                        foreach ($dynamicData as $key => $value) {
                        $htmlContent = str_replace('{{' . $key . '}}', $value, $htmlContent);
                        }

                        // Add a new section with border for this content
                        $section3 = $phpWord->addSection([
                        'borderSize' => 16,   // Border size around the content
                        'borderColor' => '000000', // Border color (black)
                        'width' => 12000,     // Optional: Control section width
                        'height' => 8000      // Optional: Control section height
                        ]);

                        $this->parseHtmlToWord($section3, $htmlContent); // Parse HTML to Word for the second file content

                        // Add a page break after this section
                        $section3->addPageBreak();

                    } else
                    {
                        return response()->json(['error' => 'HTML file not found at ' . $htmlFilePath3], 404);
                    }


                    // Check and load the codeofethics file
                    if (File::exists($htmlFilePath4))
                    {
                        foreach($TeammemberGet as $Teammembers )
                        {
                            $htmlContent = '';

                            $htmlContent .= File::get($htmlFilePath4);

                            // Step 2: Check if report exists
                            $report = ReportModel::where('report_type', '3')->latest()->first();
                            $htmlContent = '';
                            $fileFromTemplate = true; // Flag to track if content is from file

                            if ($report && !empty($report->report_contents))
                            {
                                $reportContent = json_decode($report->report_contents, true);
                                if (isset($reportContent['content']))
                                {
                                    $htmlContent = ($reportContent['content']);

                                    $fileFromTemplate = false;
                                }
                            }

                            if ($fileFromTemplate) {
                                $htmlContent .= File::get($htmlFilePath4);
                            }

                            $dynamicData = [
                                'Name'=>$Teammembers->username,
                                'Designation'=>$Teammembers->desigelname,
                                'Current Date' =>date('d-m-Y')
                            ];

                            // Replace placeholders with dynamic values
                            foreach ($dynamicData as $key => $value) {
                                // Replace {{key}} with actual values
                                $htmlContent = str_replace('[' . $key . ']', $value, $htmlContent);
                            }
                            $htmlContent = preg_replace('/<style.*?<\/style>/is', '', $htmlContent);
                            //echo $htmlContent; // Output should show <span></span> where body{...} was

                                // Add a new section with border for this content
                                $section4 = $phpWord->addSection([
                                'borderSize' => 16,   // Border size around the content
                                'borderColor' => '000000', // Border color (black)
                                'width' => 12000,     // Optional: Control section width
                                'height' => 8000      // Optional: Control section height
                            ]);

                            $this->parseHtmlToWord($section4, $htmlContent); // Parse HTML to Word for the second file content

                            // Add a page break after this section
                            $section4->addPageBreak();
                        }

                    } else
                    {
                        return response()->json(['error' => 'HTML file not found at ' . $htmlFilePath4], 404);
                    }


                    if (!$workAllocation->isEmpty())
                    {
                            //WORKALLOCATION
                            if (File::exists($htmlFilePath6)) {

                            $WorkAllocation =TransWorkAllocationModel::fetch_allocatedwork($auditscheduleid);
                            $results =array();
                            foreach ($WorkAllocation->all() as $item)
                            {
                                $results[] = [
                                    'username' => $item->username,
                                    'worktypes' => $item->worktypes,
                                ];
                            }


                            $sizeofresults=sizeof($results);

                            if($sizeofresults > 0)
                            {

                                 // Create a new PhpWord object
                            $sectionStyle = [
                                'borderSize' => 16,
                                'borderColor' => '000000',
                                'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(10),
                                ];
                                $section = $phpWord->addSection($sectionStyle);

                                $section->addText('5. WORK ALLOCATION',
                                        ['name' => $fontName, 'size' => 14, 'bold' => true], // Slightly smaller, italic text for year
                                        ['align' => 'center', 'lineHeight' => 1.5, 'spaceBefore' => 70] // Center-align the text
                                    );

                                $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80]);

                                // Add table headers with bold text
                                $table->addRow();
                                $table->addCell(1000)->addText("S.No.", ['bold' => true, 'name' => $fontName, 'size' => $defaultsize]);
                                $table->addCell(5000)->addText("Team Member Name", ['bold' => true, 'name' => $fontName, 'size' => $defaultsize]);
                                $table->addCell(5000)->addText("Work Allocation", ['bold' => true, 'name' => $fontName, 'size' => $defaultsize]);

                                $serialNumber = 1; // Initialize serial number
                                $currentUsername = null; // Track the current username

                                foreach ($results as $entry)
                                {

                                    $table->addRow();
                                    $table->addCell(1000)->addText($serialNumber++, ['name' => $fontName, 'size' => $defaultsize]); // S.No.
                                    $table->addCell(5000)->addText($entry['username'], ['name' => $fontName, 'size' => $defaultsize]); //
                                    $worktypes = htmlspecialchars($entry['worktypes'], ENT_QUOTES, 'UTF-8');
                                    $table->addCell(5000)->addText($worktypes, ['name' => $fontName, 'size' => $defaultsize]); // Username

                                }



                                // Add a page break after this section
                                $section->addPageBreak();

                            }




                            } else {
                            return response()->json(['error' => 'HTML file not found at ' . $htmlFilePath2], 404);
                            }

                    }


                    // Check and load the exit meeting file
                    if (File::exists($htmlFilePath3)) {
                    $htmlContent = '<h4>6. EXIT MEETING</h4>';
                    $htmlContent .= File::get($htmlFilePath3);

                    // Load content from the JSON file
                    $jsonFilePath = public_path('json/pdfcontent.json');
                    $jsonContent = file_get_contents($jsonFilePath);
                    $data = json_decode($jsonContent, true);

                    $language = 'en'; // en or ta
                    $title = $data['exitpdfword_' . $language]['title'];
                    $tablecontents = $data['exitpdfword_' . $language];

                    unset($tablecontents['title']);

                    $tabledata = '';
                    $sno = 1;
                    $x = 0;

                    $ValuesEcho = array($InstituteName, $FinancialYear, date('d-m-Y',strtotime($WorkingOfficeGet->fromdate)), date('d-m-Y',strtotime($WorkingOfficeGet->todate)), $WorkingOfficeGet->mandays, 'Test',$WorkingOfficeGet->teamname, '', '', '', 'Test', '', 'Test', 'Test');
                    foreach ($tablecontents as $tablekey => $tableval)
                    {
                        $tabledata .= '<tr><td class="lang">' . $sno . '</td><td class="lang">' . $tableval . '</td><td class="lang">:</td><td class="fillupfield englishcontent">' . (isset($ValuesEcho[$x]) ? $ValuesEcho[$x] : '') . '</td></tr>';
                        $sno++;
                        $x++;

                    }

                    $dynamicData = [
                    'heading_title' => $title,
                    'fontFamily' => $fontName,
                    'tabledata' => $tabledata
                    ];

                    foreach ($dynamicData as $key => $value) {
                    $htmlContent = str_replace('{{' . $key . '}}', $value, $htmlContent);
                    }

                    // Add a new section with border for this content
                    $section5 = $phpWord->addSection([
                    'borderSize' => 16,   // Border size around the content
                    'borderColor' => '000000', // Border color (black)
                    'width' => 12000,     // Optional: Control section width
                    'height' => 8000      // Optional: Control section height
                    ]);

                    $this->parseHtmlToWord($section5, $htmlContent); // Parse HTML to Word for the second file content

                    // Add a page break after this section
                    //$section5->addPageBreak();
                    } else {
                    return response()->json(['error' => 'HTML file not found at ' . $htmlFilePath3], 404);
                    }

                    //Check and load the first fil



                    $chargeData = session('charge');
                    $userData = session('user');
                    $session_userid = $userData->userid;
                    //$workAllocation = AuditDiaryModel::DiaryFetchData();
                    //$auditscheduleid = $workAllocation->first()->auditscheduleid;
                    //$userdetailsfetch=AuditDiaryModel::userdetails_fetch();
                    //$auditscheduleid = $userdetailsfetch->auditscheduleid;
                    $auditscheduleid = $scheduleId;
                    $auditSlipController = new AuditSlipController();
                    $GetauditSlips=$auditSlipController::FetchAuditSlips($auditscheduleid);
                    // Initialize a variable to track the "Pending Para" section
                    $X = 1;
                    $tableContent = '';

                    $SeverityArr=['L' => 'Low','M' => 'Medium','H' => 'High'];

                    $liabilityarr=['Y'=>'Yes','N'=>'No'];
                    // Loop through the audit slips

                    // Add the "Pending Para" header
                    // Add a new section with page border
                    $sectionpartb = $phpWord->addSection(['borderSize' => 16,             // Border thickness
                                                          'borderColor' => '000000',      // Border color (black)
                                                          'borderRightColor' => '000000', // Right border color (if needed)
                                                          'borderTopColor' => '000000',   // Top border color
                                                          'borderBottomColor' => '000000',// Bottom border color
                                                          'borderLeftColor' => '000000',  // Left border color
                                                          'cellMargin' => 50,             // Margin inside the border
                                                        ]);

                    // Add a heading or content inside the bordered section
                    $sectionpartb->addText('PART B', ['name' => $fontName, 'bold' => true, 'size' => 18],['alignment' => 'center']);

                    foreach ($GetauditSlips as $auditSlip) {
                    $textRun = $sectionpartb->addTextRun(['align' => 'center']);

                    // Add the content with the required formatting
                    //$textRun->addText($X . ' ) ', ['name' => $fontName,'bold' => true, 'size' => 14]);

                    $textRun->addText(''.$X.' ) SLIP DETAILS OF #'.$auditSlip->mainslipnumber.'', ['name' => $fontName,'bold' => true, 'size' => 14]);

                    $textRun = $sectionpartb->addTextRun(['align' => 'center']);
                    $textRun->addText('[' . $auditSlip->objectionename . ' => ', ['name' => $fontName, 'bold' => false, 'size' => 14]);
                    $textRun->addText($auditSlip->subobjectionename . ']', ['name' => $fontName, 'bold' => false, 'size' => $defaultsize]);

                    // Add remaining content
                    $textRun = $sectionpartb->addTextRun(['align' => 'left']);
                    $textRun->addText('Amount Involved         : ', ['name' => $fontName, 'size' => $defaultsize, 'bold' => true]);
                    $textRun->addText($auditSlip->amtinvolved, ['name' => $fontName, 'size' => $defaultsize]);

                    $textRun = $sectionpartb->addTextRun(['align' => 'left']);
                    $textRun->addText('Severity                : ', ['name' => $fontName, 'size' => $defaultsize, 'bold' => true]);
                    $textRun->addText($SeverityArr[$auditSlip->severity], ['name' => $fontName, 'size' => $defaultsize]);

                    $textRun = $sectionpartb->addTextRun(['align' => 'left']);
                    $textRun->addText('Liability               : ', ['name' => $fontName, 'size' => $defaultsize, 'bold' => true]);
                    $textRun->addText($liabilityarr[$auditSlip->liability], ['name' => $fontName, 'size' => $defaultsize]);
                    if($auditSlip->liability == 'Y')
                    {
                        $textRun = $sectionpartb->addTextRun(['align' => 'left']);
                        $textRun->addText('Liability Name: ', ['name' => $fontName, 'size' => $defaultsize, 'bold' => true]);
                        $textRun->addText($auditSlip->liabilityname, ['name' => $fontName, 'size' => $defaultsize]);
                    }

                    $textRun = $sectionpartb->addTextRun();
                    $textRun->addText('Slip Details: ', ['name' => $fontName,'size' => $defaultsize,'bold' => true]);
                    $textRun->addText($auditSlip->slipdetails, ['name' => $fontName,'size' => $defaultsize]);

                    $textRun = $sectionpartb->addTextRun(['align' => 'left']);

                    $textRun->addText(''.$X.'.1.   Auditor Details', ['name' => $fontName,'bold' => true, 'size' => $defaultsize]);

                    $textRun = $sectionpartb->addTextRun(['align' => 'left']);

                    $textRun->addText('Auditor Name               : ', ['name' => $fontName, 'size' => $defaultsize, 'bold' => true]);
                    $textRun->addText($auditSlip->auditorname, ['name' => $fontName, 'size' => $defaultsize]);

                    $sectionpartb->addText('Auditor Remarks ', ['name' => $fontName,'size' => $defaultsize,'bold' => true]);

                    // Check if there are auditor remarks
                    if (!empty($auditSlip->auditorremarks))
                    {

                        $auditorRemarks = json_decode($auditSlip->auditorremarks);
                        $auditorContent = isset($auditorRemarks->content) ? $auditorRemarks->content : 'No Remarks Available';

                        // Decode HTML entities (so <p> and &nbsp; are properly interpreted)
                        $auditorContent = html_entity_decode($auditorContent, ENT_QUOTES, 'UTF-8');

                        // Add HTML content to PHPWord
                        Html::addHtml($sectionpartb, $auditorContent, false, false);


                       // $textRun->addText($auditorContent, ['name' => $fontName,'bold' => false, 'size' => $defaultsize]);

                        //$this->parseHtmlToWord($sectionpartb, $auditorContent);
                    } else
                    {
                        $sectionpartb->addText('No Remarks Available', ['size' => $defaultsize,'name' => $fontName]);
                    }

                    $textRun = $sectionpartb->addTextRun(['align' => 'left']);

                    $textRun->addText(''.$X.'.2.  Auditee Details', ['name' => $fontName,'bold' => true, 'size' => $defaultsize]);

                    $textRun = $sectionpartb->addTextRun(['align' => 'left']);

                    $textRun->addText('Auditee Name               : ', ['name' => $fontName, 'size' => $defaultsize, 'bold' => true]);
                    $textRun->addText($auditSlip->username, ['name' => $fontName, 'size' => $defaultsize]);

                    // Add "Auditee Remarks" heading
                    $sectionpartb->addText('Auditee Remarks', ['name' => $fontName,'bold' => true, 'size' => $defaultsize]);

                    // Check if there are auditee remarks
                    if (!empty($auditSlip->auditeeremarks))
                    {
                        $auditeeRemarks = json_decode($auditSlip->auditeeremarks);
                        $auditeeContent = isset($auditeeRemarks->content) ? $auditeeRemarks->content : 'No Remarks Available';

                        // Decode HTML entities (so <p> and &nbsp; are properly interpreted)
                        $auditorContent = html_entity_decode($auditeeContent, ENT_QUOTES, 'UTF-8');

                        // Add HTML content to PHPWord
                        Html::addHtml($sectionpartb, $auditeeContent, false, false);

                        //$this->parseHtmlToWord($sectionpartb, $auditeeContent);

                    } else
                    {
                        $sectionpartb->addText('No Remarks Available', ['name' => $fontName,'size' => $defaultsize]);
                    }

                    $textRun = $sectionpartb->addTextRun(['align' => 'left']);

                    $textRun->addText(''.$X.'.3.   Auditor Reply', ['name' => $fontName,'bold' => true, 'size' => $defaultsize]);

                    $textRun = $sectionpartb->addTextRun();
                    $textRun->addText('Auditor Reply : ', ['name' => $fontName,'size' => $defaultsize,'bold' => true]);

                    // Check if there are auditor remarks
                    if (!empty($auditSlip->memberrejoinderremarks))
                    {

                        $memberrejoinderremarks = $auditSlip->memberrejoinderremarks;

                        $textRun->addText($memberrejoinderremarks, ['name' => $fontName,'size' => $defaultsize]);

                    } else
                    {
                        $sectionpartb->addText('No Remarks Available', ['size' => $defaultsize,'name' => $fontName]);
                    }

                    $textRun = $sectionpartb->addTextRun(['align' => 'left']);

                    $textRun->addText(''.$X.'.4.  Team Head Details', ['name' => $fontName,'bold' => true, 'size' => $defaultsize]);

                    $textRun = $sectionpartb->addTextRun(['align' => 'left']);

                   // $textRun->addText('Team Head Name               : ', ['name' => $fontName, 'size' => $defaultsize, 'bold' => true]);

                    $textRun->addText('Team Head Reply : ', ['name' => $fontName,'size' => $defaultsize,'bold' => true]);

                    // Check if there are auditor remarks
                    if (!empty($auditSlip->finalremarks))
                    {

                        $finalremarks = $auditSlip->finalremarks;

                        $textRun->addText($finalremarks, ['name' => $fontName,'size' => $defaultsize]);

                    } else
                    {
                        $sectionpartb->addText('No Remarks Available', ['size' => $defaultsize,'name' => $fontName]);
                    }

                    //$sectionpartb->addLine(['weight' => 1, 'width' => 430, 'height' => 0, 'color' => '000000']);
                    // Add a line break at the end of each loop iteration
                    $sectionpartb->addText('', ['name' => $fontName, 'size' => $defaultsize]); // Blank line after each loop iteration


                    $X++;
                    }



                    // Step 3: Parse HTML content and add to Word file
                    //$this->parseHtmlToWord($section, $htmlContent);

                    // Define the file path (save it in the 'public/files' folder)
                    $fileName = 'AuditReport_' . Carbon::now()->format('Y_m_d_H_i_s') . '.docx';
                    $filePath = public_path('files/' . $fileName);

                    // Save the Word file dynamically
                    $phpWord->save($filePath, 'Word2007');

                    // Step 4: Convert the Word file to HTML for preview
                    $phpWord = IOFactory::load($filePath);
                    $htmlWriter = IOFactory::createWriter($phpWord, 'HTML');
                    $htmlContent = '';

                    ob_start();
                    $htmlWriter->save('php://output');
                    $htmlContent = ob_get_clean();

                    // Add contenteditable="true" to make content editable
                    //$htmlContent = $this->makeEditable($htmlContent);

                    // Step 5: Add dynamic empty space if content doesn't fully fill the page
                    $htmlContent = $this->addEmptySpaceForPreview($htmlContent);

                    // Step 6: Add borders to the HTML content
                    $htmlContent = $this->addBordersToHtml($htmlContent);

                    //print_r($htmlContent);

                    // Step 7: Return the HTML content and filename for frontend preview
                    return response()->json([
                                                'res'=>'success',
                                                'html' => $htmlContent,   // Send the HTML content
                                                'filename' => $fileName   // Send the generated filename for download
                                            ]);

            }else
            {
                return response()->json([
                                            'res'=>'nodata'
                                        ]);

            }




        } catch (\Exception $e) {
            // Log the error and return a response
            \Log::error('Error in previewWordFile: ' . $e->getMessage());
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

        /**
     * Make the HTML content editable by adding contenteditable="true" to all text elements,
     * but exclude images from being editable.
     */
    private function makeEditablenew($htmlContent)
    {
        // Use DOMDocument to manipulate the HTML and make text fields editable
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true); // Disable warnings for invalid HTML structure

        // Load the HTML content
        $dom->loadHTML($htmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        // Create an XPath object to search for elements
        $xpath = new \DOMXPath($dom);

        // Find all text nodes (excluding images) and set them to editable
        foreach ($xpath->query('//p|//div|//span|//h1|//h2|//h3|//h4|//h5|//h6|//li') as $element) {
            // Check if the element is not an image
            if ($element->tagName !== 'img') {
                $element->setAttribute('contenteditable', 'true');
            }
        }

        // Return the modified HTML content
        return $dom->saveHTML();
    }


      /**
     * Make the HTML content editable by adding contenteditable="true" to all elements.
     */
    private function makeEditable($htmlContent)
    {
        // Use DOMDocument to manipulate the HTML and make text fields editable
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true); // Disable warnings for invalid HTML structure

        $dom->loadHTML($htmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        // Find all text nodes and set them to editable
        $xpath = new \DOMXPath($dom);
       // Make only the .content-wrapper div editable
        foreach ($xpath->query('//div[contains(@class, "content-wrapper")]|//body') as $contentWrapper) {
            $contentWrapper->setAttribute('contenteditable', 'true');
        }

        // Ensure individual child elements inside .content-wrapper are NOT editable
        foreach ($xpath->query('//div[contains(@class, "content-wrapper")]//*') as $childElement) {
            $childElement->removeAttribute('contenteditable');
        }


        // Return the modified HTML content
        return $dom->saveHTML();
    }

    public function containsEnglish($string)
    {
        return preg_match('/[a-zA-Z]/', $string);  // Check for English letters
    }


    private function addEmptySpaceForPreview($htmlContent)
    {
        // Calculate the total height of content (you can use JS to dynamically get the height in the front-end, but for simplicity, let's assume a fixed height here)
        $contentHeight = 5000;  // For example, assume content height is 5000px (adjust dynamically as needed)
        $pageHeight = 8000;     // Assume page height is 8000px

        // Calculate the empty space to be added
        $emptySpaceHeight = $pageHeight - $contentHeight;

        // If content is smaller than the page, add empty space
        if ($emptySpaceHeight > 0) {
            $htmlContent .= '<div style="height:' . $emptySpaceHeight . 'px;"></div>';
        }

        return $htmlContent;
    }


    private function addBordersToHtml($htmlContent)
    {
        // Add a style for the border of the content and page breaks
        $htmlContent = '
        <style>
            body {
                margin: 0;
                padding: 0;
                font-family:Times New Roman;
                line-height: 1.8;
                letter-spacing: 0.2;
                font-size:12px;
            }
            .content-wrapper {
                border: 2px solid #000;
                padding: 20px;
                margin: 20px auto;
                width: 90%;
                box-sizing: border-box;
            }
            .page-content {
                padding: 10px;
            }
            .highlight {
                background-color: yellow;
            }
            table {
                border-collapse: collapse;  /* Ensures single line borders for the table */
                width: 100%;
            }
            th, td {
                border: 1px solid black;  /* Single line border for table cells */
                padding: 5px;
                text-align: left;
            }
            th {
                background-color: #f2f2f2;
            }




            img
            {
               width:200px !important;
               height:200px !important;
            }




        </style>
        <div class="content-wrapper">
            <div class="page-content">
                ' . $htmlContent . '
            </div>
        </div>';

        return $htmlContent;
    }



    // Function to parse HTML and convert it into Word elements
    private function parseHtmlToWord($section, $htmlContent)
    {
        $fontName ='Times New Roman';
        $defaultsize =13;

        // Strip unwanted tags but preserve the structure of required tags
        $htmlContent = strip_tags($htmlContent, '<b><h1><h2><h4><h5><h6><p><ul><ol><li><table><thead><tbody><tr><th><td><pre>');

        // Load the HTML into DOMDocument
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true); // Suppress errors
        $dom->loadHTML($htmlContent); // Suppress warnings for malformed HTML
    // Optional: Check if the loading was successful

        // Iterate through the DOM to process elements
        foreach ($dom->getElementsByTagName('body')->item(0)->childNodes as $node) {
            if ($node->nodeName === 'h1') {
                // Add Heading 1
                $section->addText($node->textContent, ['name' => $fontName,'bold' => true, 'size' => 16], ['alignment' => 'center']);
            } elseif ($node->nodeName === 'h2') {
                // Add Heading 2
                $section->addText($node->textContent, ['name' => $fontName,'bold' => true, 'size' => 14], ['alignment' => 'center']);
            } elseif ($node->nodeName === 'h4') {
                // Add Heading 2
                $section->addText($node->textContent, ['name' => $fontName,'bold' =>true, 'size' => 13], ['alignment' => 'center'], ['alignment' => 'center']);
            }elseif ($node->nodeName === 'h5') {
                // Add Heading 2
                $section->addText($node->textContent, ['name' => $fontName,'bold' =>true, 'size' => 13]);
            }elseif ($node->nodeName === 'h6') {
                // Add Heading 2
                $section->addText($node->textContent, ['name' => $fontName,'bold' =>true, 'size' => $defaultsize]);
            } elseif ($node->nodeName === 'p') {
                // Add Paragraph
                $section->addText($node->textContent, ['name' => $fontName,'size' => $defaultsize],['lineHeight' => 2]);
            } elseif ($node->nodeName === 'pre') {
                // Handle <pre> tag (preserve whitespace)
                $section->addText($node->textContent, ['name' => $fontName,'size' => $defaultsize, 'underline' => true]);
            }else if ($node->nodeName === 'b') {
                // Add Heading 1
                $section->addText($node->textContent, ['name' => $fontName,'bold' => true, 'size' => $defaultsize]);
            } elseif ($node->nodeName === 'ul' || $node->nodeName === 'ol') {
                $isOrderedList = ($node->nodeName === 'ol');

                // Process list items
                foreach ($node->getElementsByTagName('li') as $listItem) {
                    // Add the list item
                    $section->addListItem(
                        $listItem->textContent, // List item content
                        0, // Indentation level (0 = no indentation)
                        ['name' => $fontName, 'size' => $defaultsize], // Text style
                        $isOrderedList ? \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER : \PhpOffice\PhpWord\Style\ListItem::TYPE_BULLET // Numbered for <ol>, bullets for <ul>
                    );
                }
            } elseif ($node->nodeName === 'table') {
                   // Process Tables
                    $table = $section->addTable([
                        'borderSize' => 6,   // Set border size for the table
                        'borderColor' => '000000', // Border color (black)
                        'cellMarginTop' => 100,   // Padding at the top of the cells
                        'cellMarginLeft' => 100,  // Padding on the left of the cells
                        'cellMarginBottom' => 100,// Padding at the bottom of the cells
                        'cellMarginRight' => 100, // Padding on the right of the cells
                    ]);

                    // Get the columns dynamically
                    $columns = [];
                    $columnCount = 0;
                    foreach ($node->getElementsByTagName('tr') as $row) {
                        $rowColumns = $row->getElementsByTagName('td');
                        $columnCount = max($columnCount, $rowColumns->length);
                        foreach ($rowColumns as $cell) {
                            if (!in_array($cell->nodeName, $columns)) {
                                $columns[] = $cell->nodeName;  // Collect column names dynamically
                            }
                        }
                    }

                    $totalColumns = count($columns);

                    // Total available width for all columns (e.g., 10000)
                    $totalWidth = 10000;

                    // Calculate the default cell width for each column
                    $defaultCellWidth = $totalWidth / $totalColumns;

                    foreach ($node->getElementsByTagName('tr') as $row) {
                        $tableRow = $table->addRow();

                        // Loop through each <td> (table cell)
                        foreach ($row->getElementsByTagName('td') as $index => $cell) {
                            // Define unique width for each cell (in twips)
                            // Example: Set different widths for cells based on index (first cell 2000, second 3000, etc.)

                            $tableType = $node->getAttribute('data-type'); // Example of custom attribute
                            $cellWidths = [];
                             // Set widths based on the table type or column count
                            if ($tableType === 'type1') {
                                $cellWidths = [1000, 5200, 250, 3500];  // Adjust this array for as many columns as needed

                            } elseif ($tableType === 'type2') {
                                $cellWidths = [4000, 6000]; // Custom widths for table type 2
                            }

                            $borderSize = ($tableType === 'type3') ? 0 : 6;  // Set border size to 0 for type3 (no border), 6 for others

                            $cellWidth = isset($cellWidths[$index]) ? $cellWidths[$index] :$defaultCellWidth; // Default width if no specific width is defined

                            // Add the cell with a specific width
                            $tableRow->addCell($cellWidth, [
                                'borderSize' => $borderSize,     // Border size for cells
                                'borderColor' => '000000', // Border color (black)
                                'cellMargin' => 80,    // Padding inside each cell (in twips)
                            ])->addText(trim($cell->textContent), ['name' => $fontName,'size' => $defaultsize]);


                        }
                    }

            }
        }
    }


    // Function to download the generated Word file
    public function downloadWordFile($fileName)
    {
        try {
            // Define the file path for download
            $filePath = public_path('files/' . $fileName);

            // Check if the file exists
            if (!file_exists($filePath)) {
                return response()->json(['error' => 'File not found'], 404);
            }

            // Ensure the content type is correctly set for Word files
            return response()->download($filePath, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
            ]);
        } catch (\Exception $e) {
            // Log the error and return a response
            \Log::error('Error in downloadWordFile: ' . $e->getMessage());
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function deleteFile(Request $request)
    {
        $fileName = $request->input('fileName'); // Get the file name from the request

        // Define the file path for deletion
        $filePath = public_path('files/' . $fileName);

        // Check if the file exists and delete it
        if (file_exists($filePath)) {
            unlink($filePath); // Delete the file
            return response()->json(['success' => 'File deleted successfully']);
        }

        return response()->json(['error' => 'File not found'], 404);
    }
}
