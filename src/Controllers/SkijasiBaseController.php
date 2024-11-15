<?php

namespace NadzorServera\Skijasi\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use NadzorServera\Skijasi\Helpers\ApiResponse;
use NadzorServera\Skijasi\Helpers\Firebase\FCMNotification;
use NadzorServera\Skijasi\Helpers\GetData;
use NadzorServera\Skijasi\Models\DataType;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;

use NadzorServera\Skijasi\Module\Commerce\Helper\UploadImage;

use NadzorServera\Skijasi\Module\Commerce\Models\Cart;
use NadzorServera\Skijasi\Module\Commerce\Models\ProductDetail;


use NadzorServera\Skijasi\Mail\KIFDiplomaNotification;
use NadzorServera\Skijasi\Mail\NewHzutsNotification;
use Illuminate\Support\Facades\Mail;

use App\Models\AdminMessage;


class SkijasiBaseController extends Controller
{
  




    public function browse(Request $request)
    {
        try {
            $slug = $this->getSlug($request);
            $data_type = $this->getDataType($slug);

            $only_data_soft_delete = $request->showSoftDelete == 'true';

            $data = $this->getDataList($slug, $request->all(), $only_data_soft_delete);

            return ApiResponse::onlyEntity($data);
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    public function all(Request $request)
    {
        try {
            $slug = $this->getSlug($request);
            $data_type = $this->getDataType($slug);

            $builder_params = [
                'order_field' => isset($request['order_field']) ? $request['order_field'] : $data_type->order_column,
                'order_direction' => isset($request['order_direction']) ? $request['order_direction'] : $data_type->order_direction,
            ];

            if ($data_type->model_name) {
                $records = GetData::clientSideWithModel($data_type, $builder_params);
            } else {
                $records = GetData::clientSideWithQueryBuilder($data_type, $builder_params);
            }

            return ApiResponse::onlyEntity($records);
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    public function read(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required',
            ]);
            $slug = $this->getSlug($request);
            $data_type = $this->getDataType($slug);
            $request->validate([
                'id' => 'exists:'.$data_type->name,
            ]);
    
            $data = $this->getDataDetail($slug, $request->id);
            // add event notification handle
            $table_name = $data_type->name;
            FCMNotification::notification(FCMNotification::$ACTIVE_EVENT_ON_READ, $table_name);
    
            return ApiResponse::onlyEntity($data);
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }


    public function generatepdffpotvrdaisia(Request $request)
{
        $request->validate([
            'id' => 'required',
        ]);
        $slug = $this->getSlug($request);
        $data_type = $this->getDataType($slug);
        $request->validate([
            'id' => 'exists:'.$data_type->name,
        ]);

   
//primanje podataka iz vue
        $statuspdf = $request->input('statuspdf');
      
        $isiapdf = $request->input('isiapdf');
        $isiayear = $request->input('isiayear');
        $todaydate = $request->input('todaydate');
    

      $options = new \Dompdf\Options();
      
      $options->set('isHtml5ParserEnabled', true);
      $options->set('isRemoteEnabled', true);
      $options->set('dpi', 220);
      $options->set('defaultFont', 'Times-Roman');
      $options->set('isFontSubsettingEnabled', true);


   
        $options->set('tempDir', '/var/www/nadzornaploca/storage/');

      $dompdf = new \Dompdf\Dompdf($options);
    //  $dompdf->setBasePath($_SERVER['DOCUMENT_ROOT']); 

    // Load your template.pdf file
    $dompdf->set_paper('A4', 'portrait');
    // Query the database to get the data
    $data = DB::table('skijasi_users')->where('id', $request->id)->first();

      // Convert the data to HTML and add it to the PDF
      $html = $this->convertDataToHtmlPOTVRDAISIA($data, $isiapdf, $isiayear, $todaydate);


 
    // Use Browsershot to convert the HTML to a high-resolution PNG
    $imagePath = 'storage/slike/baza/pdfimagetemp.png';

    Browsershot::html($html)
    ->setChromePath('/usr/bin/google-chrome') 

    ->windowSize(595, 842) 
        ->deviceScaleFactor(3)  // Increase resolution by increasing device scale factor
        ->save($imagePath);

    if (!file_exists($imagePath)) {
        die("Image not created properly, check Browsershot configuration.");
    }

    // Read the image file's contents
    $imageData = file_get_contents($imagePath);
    // Encode the image data to base64
    $base64Image = base64_encode($imageData);

    // Create HTML for the PDF embedding the image
    $pdfHtml = '
    <!DOCTYPE html>
    <html>
      <head>
        <meta charset="utf-8" />
    
      
        <style>
        @page {
            margin: 0px;
        }
        
   
          
          .bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
    
            z-index: 1;
          
          }
        </style>
      </head>
      <body>
      <div class="bg">
      <img src="data:image/png;base64,' . $base64Image . '" alt="Background Image">
  </div>
        
      </body>
    </html>
    ';


    $dompdf->loadHtml($pdfHtml, 'UTF-8');


    $dompdf->render();
    $output = $dompdf->output();

    // Optionally, delete the image file after the PDF is generated
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }

    // Return the PDF as a response
    return response($output, 200)
        ->header('Content-Type', 'application/pdf');
}





public function generatepdffpotvrdaivsi(Request $request)
{
        $request->validate([
            'id' => 'required',
        ]);
        $slug = $this->getSlug($request);
        $data_type = $this->getDataType($slug);
        $request->validate([
            'id' => 'exists:'.$data_type->name,
        ]);

   
//primanje podataka iz vue
        $statuspdf = $request->input('statuspdf');
     
        $todaydate = $request->input('todaydate');
        $year = $request->input('year');


      $options = new \Dompdf\Options();
      
      $options->set('isHtml5ParserEnabled', true);
      $options->set('isRemoteEnabled', true);
      $options->set('dpi', 220);
      $options->set('isFontSubsettingEnabled', true);

      $options->set('margin_left', 0);
      $options->set('margin_right', 0);
      $options->set('margin_top', 0);
      $options->set('margin_bottom', 0);
   
        $options->set('tempDir', '/var/www/nadzornaploca/storage/');

      $dompdf = new \Dompdf\Dompdf($options);
      $dompdf->set_paper('A4', 'portrait');
    // Query the database to get the data
    $data = DB::table('skijasi_users')->where('id', $request->id)->first();

// Convert the data to HTML and add it to the PDF
$html = $this->convertDataToHtmlPOTVRDAIVSI($data, $todaydate, $year);


 
    // Use Browsershot to convert the HTML to a high-resolution PNG
    $imagePath = 'storage/slike/baza/pdfimagetemp.png';

    Browsershot::html($html)
    

    ->setChromePath('/usr/bin/google-chrome') 

    ->windowSize(595, 842) 
        ->deviceScaleFactor(3)  
     
        ->save($imagePath);

    if (!file_exists($imagePath)) {
        die("Image not created properly, check Browsershot configuration.");
    }

    // Read the image file's contents
    $imageData = file_get_contents($imagePath);
    // Encode the image data to base64
    $base64Image = base64_encode($imageData);

    // Create HTML for the PDF embedding the image
    $pdfHtml = '
    <!DOCTYPE html>
    <html>
      <head>
        <meta charset="utf-8" />
    
      
        <style>
        @page {
            margin: 0px;
        }
        
     
          
          .bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
            background-image: url("data:image/png;base64,' . $base64Image . '");
          
            background-size: cover;
            z-index: 1;
          
          }
        </style>
      </head>
      <body>
      <div class="bg">
  </div>
        
      </body>
    </html>
    ';


    $dompdf->loadHtml($pdfHtml, 'UTF-8');


    $dompdf->render();
    $output = $dompdf->output();

    // Optionally, delete the image file after the PDF is generated
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }

    // Return the PDF as a response
    return response($output, 200)
        ->header('Content-Type', 'application/pdf');
}





    public function generatepdffid(Request $request)
{

   

        $request->validate([
            'id' => 'required',
        ]);
        $slug = $this->getSlug($request);
        $data_type = $this->getDataType($slug);
        $request->validate([
            'id' => 'exists:'.$data_type->name,
        ]);

   
//primanje podataka iz vue
        $statuspdf = $request->input('statuspdf');
        $cardseng = $request->input('cardseng');
        $cardscro = $request->input('cardscro');
  


      $options = new \Dompdf\Options();
      
      $options->set('isHtml5ParserEnabled', true);
      $options->set('isRemoteEnabled', true);
      $options->set('dpi', 300);
      $options->set('defaultFont', 'DejaVu Sans');
      $options->set('margin_left', 0);
      $options->set('margin_right', 0);
      $options->set('margin_top', 0);
      $options->set('margin_bottom', 0);
   
        $options->set('tempDir', '/var/www/nadzornaploca/storage/');

      $dompdf = new \Dompdf\Dompdf($options);
    //  $dompdf->setBasePath($_SERVER['DOCUMENT_ROOT']); 


   // $dompdf->set_paper('A4', 'portrait');
   $dompdf->set_paper(array(0, 0, 158.7, 248.6), 'landscape');

    

    // Query the database to get the data
    $data = DB::table('skijasi_users')->where('id', $request->id)->first();
    





// Convert the data to HTML and add it to the PDF
$html = $this->convertDataToHtmlID($data, $cardscro, $cardseng);

 

    $dompdf->loadHtml($html);

    // Render the PDF
    $dompdf->render();

    $dompdf->stream();
 
    // Output the generated PDF
    $output = $dompdf->output();

    // Return the PDF as a response
    return response($output, 200)
            ->header('Content-Type', 'application/pdf');
}







    public function generatepdffprint(Request $request)
    {
        

        $request->validate([
            'id' => 'required',
        ]);
        $slug = $this->getSlug($request);
        $data_type = $this->getDataType($slug);
        $request->validate([
            'id' => 'exists:'.$data_type->name,
        ]);

   
//primanje podataka iz vue
        $programNaziv = $request->input('programNaziv');
        $programRavnatelj = $request->input('programRavnatelj');
        $programVoditeljEdukacijskeGrupe = $request->input('programVoditeljEdukacijskeGrupe');
        $programDuljinaPrograma = $request->input('programDuljinaPrograma');
        $programDuljinaProgramaTekst = $request->input('programDuljinaProgramaTekst');
        $programRjKlasa = $request->input('programRjKlasa');
        $programRjurbr = $request->input('programRjurbr');
        $programRjdatum = $request->input('programRjdatum');
        $programKlasaGodina = $request->input('programKlasaGodina');
        $profesijaClana = $request->input('profesijaClana');
        $programClanklasabrojtekst = $request->input('programClanklasabrojtekst');

        $datumprijabe = $request->input('datumprijabe');
        $zavrsenaedukacija = $request->input('zavrsenaedukacija');
        $brojclanskiidurbrojtekst = $request->input('brojclanskiidurbrojtekst');
        $programMaticniBroj = $request->input('programMaticniBroj');
        $listaArray = $request->input('listaArray');
    

     
  
   

      $options = new \Dompdf\Options();
      
      $options->set('isHtml5ParserEnabled', true);
      $options->set('isRemoteEnabled', true);
      $options->set('dpi', 150);
      $options->set('defaultFont', 'DejaVu Sans');
      $options->set('margin_left', 0);
      $options->set('margin_right', 0);
      $options->set('margin_top', 0);
      $options->set('margin_bottom', 0);

      $options->set('defaultMediaType', 'visual');

      $dompdf = new \Dompdf\Dompdf($options);
      

    // Load your template.pdf file
   // $dompdf->set_paper('A4', 'portrait');
    $dompdf->set_paper(array(0,0,595.28,841.89), 'portrait');

    

    // Query the database to get the data
    $data = DB::table('su_clanovi')->where('id', $request->id)->first();
    

    $birthdateFormatted =  $this->formatDate($data->birthdate);

    setlocale(LC_TIME, 'hr_HR.UTF-8'); // Set the locale to Croatian
    $today = new \DateTime();
    $formattedTodayDate = strftime('%d. %B', $today->getTimestamp());
    $formattedTodayDateYear = strftime('%y', $today->getTimestamp());
    

    // Convert the data to HTML and add it to the PDF
    $html = $this->convertDataToHtmlPrint($data, $programNaziv, $programRavnatelj, $programVoditeljEdukacijskeGrupe, $programDuljinaPrograma, $programDuljinaProgramaTekst, $programKlasaGodina, $profesijaClana, $programRjKlasa, $programRjurbr, $programRjdatum, $birthdateFormatted, $programClanklasabrojtekst, $datumprijabe, $zavrsenaedukacija, $brojclanskiidurbrojtekst, $formattedTodayDate, $formattedTodayDateYear, $listaArray, $programMaticniBroj);

    $dompdf->loadHtml($html);

    // Render the PDF
    $dompdf->render();

    $dompdf->stream();

    // Output the generated PDF
    $output = $dompdf->output();

    // Return the PDF as a response
    return response($output, 200)
            ->header('Content-Type', 'application/pdf');
  
    }


    public function generatepdffprintcopy(Request $request)
    {
        

        $request->validate([
            'id' => 'required',
        ]);
        $slug = $this->getSlug($request);
        $data_type = $this->getDataType($slug);
        $request->validate([
            'id' => 'exists:'.$data_type->name,
        ]);

   
//primanje podataka iz vue
        $programNaziv = $request->input('programNaziv');
        $programRavnatelj = $request->input('programRavnatelj');
        $programVoditeljEdukacijskeGrupe = $request->input('programVoditeljEdukacijskeGrupe');
        $programDuljinaPrograma = $request->input('programDuljinaPrograma');
        $programDuljinaProgramaTekst = $request->input('programDuljinaProgramaTekst');
        $programRjKlasa = $request->input('programRjKlasa');
        $programRjurbr = $request->input('programRjurbr');
        $programRjdatum = $request->input('programRjdatum');
        $programKlasaGodina = $request->input('programKlasaGodina');
        $profesijaClana = $request->input('profesijaClana');
        $programClanklasabrojtekst = $request->input('programClanklasabrojtekst');

        $datumprijabe = $request->input('datumprijabe');
        $zavrsenaedukacija = $request->input('zavrsenaedukacija');
        $brojclanskiidurbrojtekst = $request->input('brojclanskiidurbrojtekst');
        $programMaticniBroj = $request->input('programMaticniBroj');
        $listaArray = $request->input('listaArray'); 
   

      $options = new \Dompdf\Options();
      
      $options->set('isHtml5ParserEnabled', true);
      $options->set('isRemoteEnabled', true);
      $options->set('dpi', 150);
      $options->set('defaultFont', 'DejaVu Sans');
      $options->set('margin_left', 0);
      $options->set('margin_right', 0);
      $options->set('margin_top', 0);
      $options->set('margin_bottom', 0);

      $options->set('defaultMediaType', 'visual');

      $dompdf = new \Dompdf\Dompdf($options);
      

    // Load your template.pdf file
   // $dompdf->set_paper('A4', 'portrait');
    $dompdf->set_paper(array(0,0,595.28,841.89), 'portrait');

    

    // Query the database to get the data
    $data = DB::table('su_clanovi')->where('id', $request->id)->first();
    

    $birthdateFormatted =  $this->formatDate($data->birthdate);

    setlocale(LC_TIME, 'hr_HR.UTF-8'); // Set the locale to Croatian
    $today = new \DateTime();
    $formattedTodayDate = strftime('%d. %B', $today->getTimestamp());
    $formattedTodayDateYear = strftime('%y', $today->getTimestamp());
    

    // Convert the data to HTML and add it to the PDF
    $html = $this->convertDataToHtmlPrintCopy($data, $programNaziv, $programRavnatelj, $programVoditeljEdukacijskeGrupe, $programDuljinaPrograma, $programDuljinaProgramaTekst, $programKlasaGodina, $profesijaClana, $programRjKlasa, $programRjurbr, $programRjdatum, $birthdateFormatted, $programClanklasabrojtekst, $datumprijabe, $zavrsenaedukacija, $brojclanskiidurbrojtekst, $formattedTodayDate, $formattedTodayDateYear, $listaArray, $programMaticniBroj);

    $dompdf->loadHtml($html);

    // Render the PDF
    $dompdf->render();

    $dompdf->stream();

    // Output the generated PDF
    $output = $dompdf->output();

    // Return the PDF as a response
    return response($output, 200)
            ->header('Content-Type', 'application/pdf');
  
    }



    public function zadnjimaticni() {
      // Fetch the highest clanbrojknjige value that is not null
      $highestClanBrojKnjige = DB::table('su_clanoviedukacijskipodaci')
          ->whereNotNull('clanbrojknjige')
          ->max('clanbrojknjige');
  
      // Calculate the next clanbrojknjige by adding 1 to the highest value
      $nextClanBrojKnjige = $highestClanBrojKnjige + 1;
  
      // Fetch all maticnibroj values that are not null
      $highestMaticniBroj = DB::table('su_clanoviedukacijskipodaci')
          ->whereNotNull('maticnibroj')
          ->max('maticnibroj');
  
      // Parse the maticnibroj to get the numeric parts
      $maticniBrojParts = explode('/', $highestMaticniBroj);
      $maticniBrojNumber = intval($maticniBrojParts[0]);
      $maticniBrojDigits = isset($maticniBrojParts[1]) ? intval($maticniBrojParts[1]) : 0;
  
      // Calculate the next maticnibroj
      $nextMaticniBroj = '';
      $nextMaticniBrojNumber = $maticniBrojNumber;
      $nextMaticniBrojDigits = $maticniBrojDigits + 1;
  
      if ($nextMaticniBrojDigits > 199) {
          $nextMaticniBrojNumber++;
          $nextMaticniBrojDigits = 1;
      }
  
      $nextMaticniBroj = $nextMaticniBrojNumber . '/' . $nextMaticniBrojDigits;
  
      // Fetch the highest idclanaedukacije value
      $highestIdClanaEdukacije = DB::table('su_clanoviedukacijskipodaci')
          ->max('idclanaedukacije');
  
      // Calculate the next idclanaedukacije by adding 1 to the highest value
      $nextIdClanaEdukacije = $highestIdClanaEdukacije + 1;
  
      // Return all values in a single JSON response
      return response()->json([
          'clanbrojknjige' => $nextClanBrojKnjige,
          'maticnibroj' => $nextMaticniBroj,
          'idclanaedukacije' => $nextIdClanaEdukacije
      ]);
  }

  public function zadnjiidbrojevi(Request $request) {
    // Assume 'klasagodine' is passed as a request parameter
    $klasagodina = $request->input('klasagodina');

    // Fetch the highest brojclanskiidurbroj value for the specified klasagodine
    $highestBrojClanskiIdUrBroj = DB::table('su_clanoviedukacijskipodaci')
                                    ->where('klasagodina', $klasagodina)
                                    ->max('brojclanskiidurbroj');
    // Calculate the next brojclanskiidurbroj by adding 1 to the highest value
    $nextBrojClanskiIdUrBroj = $highestBrojClanskiIdUrBroj + 1;

    // Return all values in a single JSON response
    return response()->json([
        'brojclanskiidurbroj' => $nextBrojClanskiIdUrBroj
    ]);
}

  
  
private function generateAndUpdateCallnumber($memberId)
{
    $paymentId = DB::getPdo()->lastInsertId();
    $callnumber = $paymentId . '-' . $memberId;

    DB::table('tbl_payments')
        ->where('id', $paymentId)
        ->update(['callnumber' => $callnumber]);

    return $paymentId;
}




public function generateisiagodinu(Request $request)
{
    $ranges = $request->input('ranges');
    $isiayear = $request->input('isiayear');

    DB::beginTransaction();

    try {
        \Log::info("Starting ISIA number generation", ['ranges' => $ranges, 'isiayear' => $isiayear]);

        // Identify users excluded due to unpaid payments
        $excludedUsers = DB::table('skijasi_users')
            ->join('tbl_licence', 'skijasi_users.id', '=', 'tbl_licence.idmember')
            ->where('tbl_licence.nazivlicence', 'like', '%ISIA%')
            ->where('tbl_licence.nazivlicence', 'not like', '%ISIA CARD%')
            ->whereNull('tbl_licence.krajlicence')
            ->where('tbl_licence.aktivna', 1)
            ->whereIn('skijasi_users.id', function($query) {
                $query->select('idmember')
                    ->from('tbl_payments')
                    ->whereIn(DB::raw('YEAR(opendate)'), [2022, 2023, 2024])
                    ->where('paidstatus', 0)
                    ->where('partialpaid', 0)
                    ->where('paymentdiscard', 0)
                    ->where('paymentforgive', 0)
                    ->groupBy('idmember')
                    ->havingRaw('COUNT(DISTINCT YEAR(opendate)) = 3');
            })
            ->select('skijasi_users.id as idmember')
            ->get();

        \Log::info("Users excluded due to unpaid payments", [
            'count' => $excludedUsers->count(),
            'idmembers' => $excludedUsers->pluck('idmember')->toArray()
        ]);

        // Final eligible users query with license count
        $eligibleUsers = DB::table('skijasi_users')
            ->join('tbl_licence', 'skijasi_users.id', '=', 'tbl_licence.idmember')
            ->where('tbl_licence.nazivlicence', 'like', '%ISIA%')
            ->where('tbl_licence.nazivlicence', 'not like', '%ISIA CARD%')
            ->whereNull('tbl_licence.krajlicence')
            ->where('tbl_licence.aktivna', 1)
            ->whereNotIn('skijasi_users.id', $excludedUsers->pluck('idmember'))
            ->groupBy('skijasi_users.id')
            ->select('skijasi_users.id', DB::raw('COUNT(*) as license_count'))
            ->get();

        \Log::info("Final eligible users", ['count' => $eligibleUsers->count()]);

        // Add this after the eligible users query
$usersWithMultipleLicenses = $eligibleUsers->where('license_count', '>', 1);
\Log::info("Users with multiple licenses", [
    'count' => $usersWithMultipleLicenses->count(),
    'details' => $usersWithMultipleLicenses->map(function ($user) {
        return ['id' => $user->id, 'license_count' => $user->license_count];
    })
]);

        $totalLicenses = $eligibleUsers->sum('license_count');
        \Log::info("Total eligible licenses", ['count' => $totalLicenses]);

        $availableNumbers = collect();
        foreach ($ranges as $range) {
            $availableNumbers = $availableNumbers->concat(range($range['start'], $range['end']));
        }

        \Log::info("Available numbers", ['count' => $availableNumbers->count()]);

        $assignedCount = 0;
        foreach ($eligibleUsers as $user) {
            $numbersToAssign = min($user->license_count, $availableNumbers->count());
            
            for ($i = 0; $i < $numbersToAssign; $i++) {
                if ($availableNumbers->isEmpty()) {
                    \Log::warning("Ran out of numbers", ['assigned' => $assignedCount, 'eligible' => $totalLicenses]);
                    break 2; // Break out of both loops
                }

                $number = $availableNumbers->shift();

                DB::table('tbl_isia_member')->insert([
                    'idmember' => $user->id,
                    'isiayear' => $isiayear,
                    'idisia' => $number
                ]);

                $assignedCount++;
            }
        }

        \Log::info("Assignment complete", ['assigned' => $assignedCount, 'eligible' => $totalLicenses]);

        // Log remaining numbers
        \Log::info("Remaining unused numbers", [
            'count' => $availableNumbers->count(),
            'numbers' => $availableNumbers->toArray()
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'ISIA numbers assigned successfully',
            'data' => [
                'assigned_count' => $assignedCount,
                'eligible_count' => $totalLicenses,
                'remaining_numbers_count' => $availableNumbers->count(),
            ]
        ]);

    } catch (Exception $e) {
        DB::rollBack();
        \Log::error("Error in ISIA number assignment", ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        return response()->json([
            'success' => false,
            'message' => 'Error assigning ISIA numbers: ' . $e->getMessage(),
            'error_details' => $e->getTraceAsString(),
        ], 500);
    }
}


// New function to handle multiple statuses
private function addToCartForAllStatuses($userId, $productId)
{
    $memberStatuses = DB::table('tbl_member_status')
        ->join('trainersts', 'tbl_member_status.trainerstsid', '=', 'trainersts.id')
        ->where('tbl_member_status.idmember', $userId)
        ->where('tbl_member_status.statusdefault', 1) 
        ->select('trainersts.cardscro as userstatus')
        ->get();

    if ($memberStatuses->isEmpty()) {
        throw new \Exception("Member status not found for user ID: $userId");
    }

    foreach ($memberStatuses as $status) {
        $this->addToCart($userId, $productId, $status->userstatus);
    }
}

// Modified helper function to add item to cart
private function addToCart($userId, $productId, $userStatus)
{
    $product_detail = ProductDetail::where('product_id', $productId)
        ->where(function ($query) use ($userStatus) {
            $query->where('name', $userStatus)
                  ->orWhere('name', 'Default');
        })
        ->first();

    if (!$product_detail) {
        \Log::warning("Product detail not found for ID: $productId and status: $userStatus");
        return; // Skip this status if no matching product detail is found
    }

    $cart = Cart::where('user_id', $userId)
        ->where('product_detail_id', $product_detail->id)
        ->first();

    if (empty($cart)) {
        Cart::create([
            'product_detail_id' => $product_detail->id,
            'user_id' => $userId,
            'quantity' => 1,
        ]);
    } else {
        $cart->update([
            'quantity' => $cart->quantity + 1,
        ]);
    }
}



private function addCartForUnpaidPayments($userId)
{
    $fiveYearsAgo = now()->subYears(5)->toDateTimeString();

    $unpaidPayments = DB::table('tbl_payments')
        ->where('idmember', $userId)
        ->where('paidstatus', 0)
        ->where('partialpaid', 0)
        ->where('paymentdiscard', 0)
        ->where('paymentforgive', 0)
        ->where('opendate', '>=', $fiveYearsAgo)
        ->get();

    foreach ($unpaidPayments as $payment) {
        $productId = $this->getProductIdForPayment($payment->idpaygroup);
        if ($productId) {
            $this->addToCart($userId, $productId, 'Nije član'); 
        }
    }
}

private function getProductIdForPayment($paygroup)
{
    // Map paygroups to product IDs
    $payGroupToProductId = [
        1 => 13,  // Godišnja članarina
        2 => 14,  // ISIA članarina
        7 => 15,  // ISIA Card članarina
        12 => 18, // Trenerska članarina
      
        13 => 14,
    ];

    return $payGroupToProductId[$paygroup] ?? null;
}





    public function generatepdff(Request $request)
{
  

        $request->validate([
            'id' => 'required',
        ]);
        $slug = $this->getSlug($request);
        $data_type = $this->getDataType($slug);
        $request->validate([
            'id' => 'exists:'.$data_type->name,
        ]);

   
//primanje podataka iz vue
        $programNaziv = $request->input('programNaziv');
        $programRavnatelj = $request->input('programRavnatelj');
        $programVoditeljEdukacijskeGrupe = $request->input('programVoditeljEdukacijskeGrupe');
        $programDuljinaPrograma = $request->input('programDuljinaPrograma');
        $programDuljinaProgramaTekst = $request->input('programDuljinaProgramaTekst');
        $programRjKlasa = $request->input('programRjKlasa');
        $programRjurbr = $request->input('programRjurbr');
        $programRjdatum = $request->input('programRjdatum');
        $programKlasaGodina = $request->input('programKlasaGodina');
        $profesijaClana = $request->input('profesijaClana');
        $programClanklasabrojtekst = $request->input('programClanklasabrojtekst');

        $datumprijabe = $request->input('datumprijabe');
        $zavrsenaedukacija = $request->input('zavrsenaedukacija');
        $brojclanskiidurbrojtekst = $request->input('brojclanskiidurbrojtekst');
        $programMaticniBroj = $request->input('programMaticniBroj');
        $listaArray = $request->input('listaArray');
    

      $options = new \Dompdf\Options();
      
      $options->set('isHtml5ParserEnabled', true);
      $options->set('isRemoteEnabled', true);
      $options->set('dpi', 150);
      $options->set('defaultFont', 'DejaVu Sans');
      $options->set('margin_left', 0);
      $options->set('margin_right', 0);
      $options->set('margin_top', 0);
      $options->set('margin_bottom', 0);

      $options->set('defaultMediaType', 'visual');

      $dompdf = new \Dompdf\Dompdf($options);
      

    // Load your template.pdf file
   // $dompdf->set_paper('A4', 'portrait');
    $dompdf->set_paper(array(0,0,595.28,841.89), 'portrait');

    

    // Query the database to get the data
    $data = DB::table('su_clanovi')->where('id', $request->id)->first();
    

    $birthdateFormatted =  $this->formatDate($data->birthdate);

    setlocale(LC_TIME, 'hr_HR.UTF-8'); // Set the locale to Croatian
    $today = new \DateTime();
    $formattedTodayDate = strftime('%d. %B', $today->getTimestamp());
    $formattedTodayDateYear = strftime('%y', $today->getTimestamp());
    

    // Convert the data to HTML and add it to the PDF
    $html = $this->convertDataToHtml($data, $programNaziv, $programRavnatelj, $programVoditeljEdukacijskeGrupe, $programDuljinaPrograma, $programDuljinaProgramaTekst, $programKlasaGodina, $profesijaClana, $programRjKlasa, $programRjurbr, $programRjdatum, $birthdateFormatted, $programClanklasabrojtekst, $datumprijabe, $zavrsenaedukacija, $brojclanskiidurbrojtekst, $formattedTodayDate, $formattedTodayDateYear, $listaArray, $programMaticniBroj);

    $dompdf->loadHtml($html);

    // Render the PDF
    $dompdf->render();

    $dompdf->stream();

    // Output the generated PDF
    $output = $dompdf->output();

    // Return the PDF as a response
    return response($output, 200)
            ->header('Content-Type', 'application/pdf');
}

private function formatDate($dateString) {
    $date = \DateTime::createFromFormat('Y-m-d', $dateString);
    return $date->format('d.m.Y.');
}




private function convertDataToHtmlPOTVRDAISIA($data, $isiapdf, $isiayear, $todaydate)
{
    $data = (array) $data; // Convert object to array

    $imagePath = 'storage/slike/baza/bg1.png';

    // Read the image content
    $imageData = file_get_contents($imagePath);
    
    // Encode the image data to base64
    $base64Image = base64_encode($imageData);

        $imagePath2 = 'storage/slike/baza/logowatermark.png';
    // Read the image content
    $imageData2 = file_get_contents($imagePath2);
    // Encode the image data to base64
    $base64Image2 = base64_encode($imageData2);



$html = '
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="initial-scale=1" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin> 

    <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Avenir Next:wght@400&display=swap"
    />
    <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap"
    />


    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Tinos:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
   
    <style>
    @page {
        margin: 0px;
    }
      body {
        margin: 0;
        line-height: normal;
      }

    
      
      .bg {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100%;
        height: 100%;
        background-image: url("data:image/png;base64,' . $base64Image2 . '");
        background-repeat: no-repeat;
        background-size: contain;
        z-index: 1;
      
    
      }
    </style>
  </head>
  <body>
  <div class="bg"></div>
    <div
      style="
        position: relative;
        width: 100%;
        overflow: hidden;
        display: flex;
        flex-direction: row;
        align-items: flex-start;
        justify-content: flex-start;
        text-align: left;
        font-size: 12.13px;
        color: #000;
        font-family: \'Tinos\';
      "
    >
      <div
        style="
          position: relative;
          width: 595px;
          height: 848px;
          overflow: hidden;
          flex-shrink: 0;
        "
      >
        <img
          style="
            position: absolute;
            top: 0px;
            left: -3px;
            width: 595px;
            height: 848px;
            overflow: hidden;
            object-fit: cover;
          "
          alt=""
          src="data:image/jpeg;base64,' . $base64Image . '"
        />

        <div
          style="
            position: absolute;
            top: 779.95px;
            left: 120.22px;
            font-size: 10px;
            font-family: \'Avenir Next\';
            color: #31bdec;
          "
        >
          www.hzuts.hr  info@hzuts.hr  OIB: 31990276348  IBAN HR7423600001101359833
        </div>
        <div
          style="
            position: absolute;
            top: 763.15px;
            left: 408.39px;
            font-size: 10px;
            font-family: \'Avenir Next\';
            color: #31bdec;
          "
        > f.+38512399955
        </div>
        <div
        style="
          position: absolute;
          top: 763.15px;
          left: 320.39px;
          font-size: 10px;
          font-family: \'Avenir Next\';
          color: #31bdec;
        "
      >   t.+38512399950
      </div>
        <div
          style="
            position: absolute;
            top: 763.15px;
            left: 120.15px;
            font-size: 10px;
            font-family: \'Avenir Next\';
            color: #31bdec;
          "
        >
          Maksimirska 51a,   10000 Zagreb, Hrvatska  
        </div>
        <div
          style="
            position: absolute;
            top: 664.59px;
            left: 410.9px;
            font-size: 12.69px;
          "
        >
        '.$todaydate.'
        </div>
        <div
          style="
            position: absolute;
            top: 663.59px;
            left: 307.7px;
            font-size: 11.44px;
          "
        >
          Zagabria li,
        </div>
        <b
          style="
            position: absolute;
            top: 602.59px;
            left: 70.9px;
            font-size: 10.88px;
          "
          >Dag Modrić</b
        >
        <div
          style="
            position: absolute;
            top: 588.59px;
            left: 70.9px;
            font-size: 10.31px;
          "
        >
          Il Presidente
        </div>
        <b
          style="
            position: absolute;
            top: 568.1px;
            left: 70.9px;
            font-size: 11.63px;
          "
          >HZUTS</b
        >
        <div
          style="
            position: absolute;
            top: 436.1px;
            left: 70.9px;
            font-size: 11.44px;
          "
        >
          territorio Croato.
        </div>
        <div style="position: absolute; top: 417.1px; left: 70.9px">
          E’ abilitato/ta a svolgere la professione di Maestro di Sci senza
          nessuna limitazione nel
        </div>
        <div
          style="
            position: absolute;
            top: 391.15px;
            left: 217.15px;
            font-size: 11.44px;
          "
        >
          per '.$isiayear.'.
        </div>
        <div
          style="
            position: absolute;
            top: 391.15px;
            left: 70.9px;
            font-size: 11.44px;
          "
        >
          <span>con certificato </span>
          <b>ISIA</b>
        
        </div>
        <div
        style="
          position: absolute;
          top: 389.15px;
          left: 175.9px;
          font-size: 11.44px;
        "
      >
        <span>'.$isiapdf.'</span>
      </div>
        <div style="position: absolute; top: 372.1px; left: 70.9px">
          In possesso del titolo Croato di Maestro di Sci
        </div>
        <div
          style="
            position: absolute;
            top: 345.1px;
            left: 70.9px;
            font-size: 11.44px;
          "
        >
          residente in: '.$data["adresa"].', '.$data["postanskibroj"].' '.$data["grad"].'
        </div>
        <div
          style="
            position: absolute;
            top: 328.1px;
            left: 414.9px;
            font-size: 12px;
          "
        >
        '.$data["idmember"].'
        </div>
        <div style="position: absolute; top: 331.1px; left: 354.1px;">
          tessera No
        </div>
        <div style="position: absolute; top: 327px; left: 230.9px; font-size: 12px;">
        '.$data["name"].' '.$data["username"].'
        </div>
        <div
          style="
            position: absolute;
            top: 330.1px;
            left: 70.9px;
            font-size: 11.44px;
          "
        >
          Si dichiara che il Sig/ra:
        </div>
        <b
          style="
            position: absolute;
            top: 240.1px;
            left: 263.85px;
            font-size: 12.4px;
          "
          >Certificato</b
        >
        <div
        style="
          position: absolute;
          top: 104.1px;
          left: 70.9px;
          font-size: 12px;
        "
      > '.$data["name"].' '.$data["username"].'
      </div>

     <div></div>
      </div>
    </div>
  </body>
</html>


';



return $html;
}


private function convertDataToHtmlPOTVRDAIVSI($data, $todaydate, $year)
{
    $data = (array) $data; // Convert object to array

    $imagePath = 'storage/slike/baza/bg2.png';

    // Read the image content
    $imageData = file_get_contents($imagePath);
    
    // Encode the image data to base64
    $base64Image = base64_encode($imageData);


    $imagePath2 = 'storage/slike/baza/logowatermark.png';
    // Read the image content
    $imageData2 = file_get_contents($imagePath2);
    // Encode the image data to base64
    $base64Image2 = base64_encode($imageData2);


$html = '
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin> 

    <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Avenir Next:wght@400&display=swap"
    />
    <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap"
    />


    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Tinos:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
   
    <style>
    @page {
        margin: 0px;
    }
      body {
        margin: 0;
        line-height: normal;
      }

    
      
      .bg {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100%;
        height: 100%;
        background-image: url("data:image/png;base64,' . $base64Image2 . '");
        background-repeat: no-repeat;
        background-size: contain;
        z-index: 1;
      
  
      }
    </style>
  </head>
  <body>
  <div class="bg"></div>
    <div
      style="
        position: relative;
        width: 100%;
        overflow: hidden;
        display: flex;
        flex-direction: row;
        align-items: flex-start;
        justify-content: flex-start;
        text-align: left;
        font-size: 12.13px;
        color: #000;
        font-family: \'Tinos\';
      "
    >
      <div
        style="
          position: relative;
          width: 595px;
          height: 848px;
          overflow: hidden;
          flex-shrink: 0;
        "
      >
        <img
          style="
            position: absolute;
            top: 0px;
            left: -3px;
            width: 595px;
            height: 848px;
            overflow: hidden;
            object-fit: cover;
          "
          alt=""
          src="data:image/png;base64,' . $base64Image . '"
        />

        <div
          style="
            position: absolute;
            top: 779.95px;
            left: 120.22px;
            font-size: 10px;
            font-family: \'Avenir Next\';
            color: #31bdec;
          "
        >
          www.hzuts.hr  info@hzuts.hr  OIB: 31990276348  IBAN HR7423600001101359833
        </div>
        <div
          style="
            position: absolute;
            top: 763.15px;
            left: 408.39px;
            font-size: 10px;
            font-family: \'Avenir Next\';
            color: #31bdec;
          "
        > f.+38512399955
        </div>
        <div
        style="
          position: absolute;
          top: 763.15px;
          left: 320.39px;
          font-size: 10px;
          font-family: \'Avenir Next\';
          color: #31bdec;
        "
      >   t.+38512399950
      </div>
        <div
          style="
            position: absolute;
            top: 763.15px;
            left: 120.15px;
            font-size: 10px;
            font-family: \'Avenir Next\';
            color: #31bdec;
          "
        >
          Maksimirska 51a,   10000 Zagreb, Hrvatska  
        </div>
        <div
          style="
            position: absolute;
            top: 664.59px;
            left: 410.9px;
            font-size: 12.69px;
          "
        >
        '.$todaydate.'
        </div>
        <div
          style="
            position: absolute;
            top: 663.59px;
            left: 307.7px;
            font-size: 11.44px;
          "
        >
          Zagabria li,
        </div>
        <b
          style="
            position: absolute;
            top: 602.59px;
            left: 70.9px;
            font-size: 10.88px;
          "
          >Dag Modrić</b
        >
        <div
          style="
            position: absolute;
            top: 588.59px;
            left: 70.9px;
            font-size: 10.31px;
          "
        >
          Il Presidente
        </div>
        <b
          style="
            position: absolute;
            top: 568.1px;
            left: 70.9px;
            font-size: 11.63px;
          "
          >HZUTS</b
        >
        <div
          style="
            position: absolute;
            top: 436.1px;
            left: 70.9px;
            font-size: 11.44px;
          "
        >
          territorio Croato.
        </div>
        <div style="position: absolute; top: 417.1px; left: 70.9px">
          E’ abilitato/ta a svolgere la professione di Maestro di Sci senza
          nessuna limitazione nel
        </div>
       <div
          style="
            position: absolute;
            top: 391.15px;
            left: 168.15px;
            font-size: 11.44px;
          "
        >
          per '.$year.'.
        </div>
        <div
          style="
            position: absolute;
            top: 391.15px;
            left: 70.9px;
            font-size: 11.44px;
          "
        >
          <span>con certificato </span>
          <b>IVSI</b>
        
        </div>
    
        <div style="position: absolute; top: 376.1px; left: 70.9px">
          In possesso del titolo Croato di Maestro di Sci
        </div>
        <div
          style="
            position: absolute;
            top: 345.1px;
            left: 70.9px;
            font-size: 11.44px;
          "
        >
          residente in: '.$data["adresa"].', '.$data["postanskibroj"].' '.$data["grad"].'
        </div>
        <div
          style="
            position: absolute;
            top: 328.1px;
            left: 414.9px;
            font-size: 12px;
          "
        >
        '.$data["idmember"].'
        </div>
        <div style="position: absolute; top: 331.1px; left: 354.1px">
          tessera No
        </div>
        <div style="position: absolute; top: 327px; left: 230.9px">
        '.$data["name"].' '.$data["username"].'
        </div>
        <div
          style="
            position: absolute;
            top: 330.1px;
            left: 70.9px;
            font-size: 11.44px;
          "
        >
          Si dichiara che il Sig/ra:
        </div>
        <b
          style="
            position: absolute;
            top: 240.1px;
            left: 263.85px;
            font-size: 12.4px;
          "
          >Certificato</b
        >
        <div
        style="
          position: absolute;
          top: 104.1px;
          left: 70.9px;
          font-size: 12px;
        "
      > '.$data["name"].' '.$data["username"].'
      </div>

     <div></div>
      </div>
    </div>
  </body>
</html>


';



return $html;
}



private function convertDataToHtmlID($data, $cardscro, $cardseng)
{
    $data = (array) $data; // Convert object to array

$imagePath = 'storage/' . $data['avatar'];

// Read the image content
$imageData = file_get_contents($imagePath);

// Encode the image data to base64
$base64Image = base64_encode($imageData);

if (empty($cardseng)) {
  $cardscroStyle = "width: 100%; left: 0%; text-align: center;";
} else {
  $cardscroStyle = "width: 54%; left: 0%; text-align: right;";
}

    // Calculate the target dimensions while maintaining a 2:3 aspect ratio
    $targetWidth = 247; // Set your desired width here (adjust as needed)
    $targetHeight = $targetWidth * (3 / 2);


 $html = '<html><head><meta charset="UTF-8"><style>
    @page {
        margin: 0px;
    }

    .bg {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100%;
        height: 100%;
        background-image: url("https://firebasestorage.googleapis.com/v0/b/hzuts-47aa0.appspot.com/o/uvjerenje-template-2.jpg?alt=media&token=e5eecf05-4c25-4c25-938d-484d53f819d2&_gl=1*qv3qut*_ga*OTY2NzAxMjczLjE2OTYyNjk2OTk.*_ga_CW55HF8NVT*MTY5NjMzODg3My4yLjEuMTY5NjMzODkzOC42MC4wLjA.");
        background-repeat: no-repeat;
        background-size: contain;
        z-index: 1;
      
        /* Hide the image when printing */
        @media print {
          visibility: hidden;
        }
      }

      .image-container {
        position: absolute;
        top: 18%;
        left: 7%;
        width: ' . $targetWidth . 'px;
        height: ' . $targetHeight . 'px;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }

    .image-container img {
        width: 100%; /* Set the image width to 100% */
        height: 100%; /* Set the image height to 100% */
        object-fit: contain; /* Ensure the image is contained within the dimensions */
        z-index: 2;
    }

    /* Define the styles for the text boxes */
    .text-box {
     position: absolute;
      font-size: 20px;
      z-index: 2;
    }
    .text-box-lista {
        font-weight: lighter;
         font-size: 22.7px;
         z-index: 2;
       }

    .text-box-small {
        position: absolute;
         font-size: 15px;
         z-index: 2;
       }
    /* Define the styles for the labels */
    .label {
      position: absolute;
      font-size: 38px;
      font-weight: bold;
      z-index: 2;
    }
    .labeltop1 {
        position: absolute;
        font-size: 44px; /* Adjust the font size as needed */
        font-weight: bold;
        z-index: 2;
        overflow: visible; /* Allow text to extend beyond boundaries */
        white-space: nowrap; /* Prevent text from wrapping */
        font-size-adjust: 0.5; /* Adjust font size to fit */
      }
      
      .labeltop2 {
        position: absolute;
        font-size: 45px; /* Adjust the font size as needed */
        font-weight: normal;
        z-index: 2;
        overflow: visible; /* Allow text to extend beyond boundaries */
        white-space: nowrap; /* Prevent text from wrapping */
        font-size-adjust: 0.5; /* Adjust font size to fit */
      }
      
      
      
    /* Define the styles for the background image */
    .bg {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100%;
        height: 100%;
        background-image: url("");
        background-repeat: no-repeat;
        background-size: contain;
        z-index: 1;
      
        /* Hide the image when printing */
        @media print {
          visibility: hidden;
        }
      }
  
  </style></head><body>

  <div class="bg"></div>

  <div class="image-container">
    <img src="data:image/jpeg;base64,' . $base64Image . '" alt="NEMA SLIKE" />
  </div>

  <div class="labeltop1" style="top: 2.0%; ' . $cardscroStyle . '">
  '.$cardscro.'
</div>
  <div class="labeltop2" style="top: 2.0%; left:56%; text-align: left; width: 40%; ">
  '.$cardseng.'
</div>


  <div class="label" style="top: 18.0%; left:35%; ">
  '.$data["name"].'
</div>

 <div class="label" style="top: 33.0%; left:35%; ">
  '.$data["username"].'
</div>

 <div class="label" style="top: 48.0%; left:35%;  ">
 '.$data["grad"].'
</div>

<div class="label" style="top: 63.0%; left: 35%; ">
'.$data["idmember"].'
</div>';




$html .= '</body></html>';

  return $html;
}



private function convertDataToHtml($data, $programNaziv, $programRavnatelj, $programVoditeljEdukacijskeGrupe, $programDuljinaPrograma, $programDuljinaProgramaTekst, $programKlasaGodina, $profesijaClana, $programRjdatum, $programRjKlasa, $programRjurbr, $birthdateFormatted, $programClanklasabrojtekst, $datumprijabe, $zavrsenaedukacija, $brojclanskiidurbrojtekst, $formattedTodayDate, $formattedTodayDateYear, $listaArray, $programMaticniBroj )
{
    $data = (array) $data; // Convert object to array
   // Convert listaArray to an array if it's not already
   $listaArray = (array) $listaArray;
   $listHtml = '';

   if (!empty($listaArray) && isset($listaArray[0])) {
       // Split the string into individual items
       $segments = explode(',', $listaArray[0]);

       // Here goes the new code for splitting the list into two columns
       $firstColumn = array_slice($segments, 0, 12); // First 12 items
       $secondColumn = array_slice($segments, 12);   // Remaining items

       $listHtml = '<div class="text-box-lista">';
       $listHtml .= '<div class="list-column" style="float: left; width: 150%;">';
       $listHtml .= '<ul style="list-style-type: none; padding-left: 0;">';

       foreach ($firstColumn as $index => $segmentName) {
           $number = str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT);
           $listHtml .= "<li>{$number}.&nbsp;&nbsp;{$segmentName}</li>";
       }

       $listHtml .= '</ul>';
       $listHtml .= '</div>'; // Close first column

       if (!empty($secondColumn)) {
           $listHtml .= '<div class="list-column" style="float: left; width: 150%;">';
           $listHtml .= '<ul style="list-style-type: none; padding-left: 0;">';

           foreach ($secondColumn as $index => $segmentName) {
               $number = str_pad((string)($index + 13), 2, '0', STR_PAD_LEFT); // Start from 13
               $listHtml .= "<li>{$number}.&nbsp;&nbsp;{$segmentName}</li>";
           }

           $listHtml .= '</ul>';
           $listHtml .= '</div>'; // Close second column
       }

       $listHtml .= '</div>'; // Close text-box-lista
   }




   $listHtml .= '</ul>';
   $listHtml .= '</div>';



    $html = '<html><head><meta charset="UTF-8"><style>
    @page {
        margin: 0px;
    }
    /* Define the styles for the text boxes */
    .text-box {
     position: absolute;
      font-size: 23px;
      z-index: 2;
    }
    .text-box-lista {
        font-weight: lighter;
         font-size: 22.7px;
         z-index: 2;
         width: 80%;
       }

    .text-box-small {
        position: absolute;
         font-size: 15px;
         z-index: 2;
       }
    /* Define the styles for the labels */
    .label {
      position: absolute;
      font-size: 32px;
      //font-weight: bold;
      z-index: 2;
    }
    /* Define the styles for the background image */
    .bg {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100%;
        height: 100%;
        background-image: url("https://firebasestorage.googleapis.com/v0/b/hzuts-47aa0.appspot.com/o/uvjerenje-template-2.jpg?alt=media&token=e5eecf05-4c25-4c25-938d-484d53f819d2&_gl=1*qv3qut*_ga*OTY2NzAxMjczLjE2OTYyNjk2OTk.*_ga_CW55HF8NVT*MTY5NjMzODg3My4yLjEuMTY5NjMzODkzOC42MC4wLjA.");
        background-repeat: no-repeat;
        background-size: contain;
        z-index: 1;
      
        /* Hide the image when printing */
        @media print {
          visibility: hidden;
        }
      }
  
  </style></head><body>

  <div class="bg"></div>

  <div class="text-box" style="top: 8.5%; left: 23%;">
    USTANOVA ZA OBRAZOVANJE ODRASLIH SKIJAŠKO UČILIŠTE
  </div>

  <div class="text-box" style="top: 11.4%; left: 39%;">
    Maksimirska 51, Zagreb
  </div>

  <div class="text-box" style="top: 14.4%; left: 13.1%;">' . $programClanklasabrojtekst . '</div>

  <div class="text-box" style="top: 14.4%; left: 44.3%;">' . $brojclanskiidurbrojtekst . '</div>

  <div class="text-box" style="top: 14.4%; left: 78.7%; text-align: center; width: 10%;">' . $programMaticniBroj . '</div>

  <div class="label" style="top: 23.7%; left:25%; text-align: center; width: 50%;">
  '.$data["firstname"].' '.$data["lastname"].'
</div>


<div class="text-box" style="top: 27.08%; left: 12.54%;">
'.implode('&nbsp;&nbsp;', str_split($data["oib"])).'
</div>


<div class="text-box" style="top: 27.37%; left: 50.33%;">'.$data["fatherfirstname"].' '.$data["fatherlastname"].' i '.$data["motherfirstname"].' '.$data["motherlastname"].'</div>

<div class="text-box" style="top: 29.90%; left: 18.38%; text-align: center; width: 15%;">'.$birthdateFormatted.'</div>

<div class="text-box" style="top: 29.90%; left: 53.00%;">'.$data["birthplace"].'</div>

<div class="text-box" style="top: 32.70%; left: 20.33%;">'.$data["birthcountry"].'</div>

<div class="text-box" style="top: 32.70%; left: 60.33%;">'.$data["citizenship"].'</div>


<div class="text-box" style="top: 35.20%; left: 25.00%;">' . $profesijaClana . '</div>

<div class="text-box" style="top: 40.70%; left: 10.00%; text-align: center; width: 80%;">' . $programNaziv . '</div>


<div class="text-box" style="top: 43.37%; left: 33.00%;">' . $programDuljinaProgramaTekst . '</div>

<div class="text-box" style="top: 43.37%; left: 77.00%;">' . $programDuljinaPrograma . '</div>

<div class="text-box" style="top: 46.00%; left: 23.00%;">' . $datumprijabe . '</div>

<div class="text-box" style="top: 46.00%; left: 43.00%;">' . $zavrsenaedukacija . '</div>

<div class="text-box" style="top: 49.60%; left: 10.20%;">' . $listHtml . '</div>


<div class="text-box" style="top: 76.00%; left: 24.33%;">' . $zavrsenaedukacija . '</div>

<div class="text-box" style="top: 79.00%; left: 10.00%; text-align: center; width: 80%;">' . $programNaziv . '</div>

<div class="text-box" style="top: 82.00%; left: 20.00%;">
 Zagrebu </div>

 <div class="text-box" style="top: 82.00%; left: 65.00%;">' . $formattedTodayDate . '</div>

 <div class="text-box" style="top: 82.00%; left: 80.54%;">' . $formattedTodayDateYear . '</div>

 <div class="text-box" style="top: 86.30%; left: 10.50%;">' . $programVoditeljEdukacijskeGrupe . '</div>

 <div class="text-box" style="top: 86.30%; left: 69.70%;">' . $programRavnatelj . '</div>

 <div class="text-box-small" style="top: 89.0%; left: 43.00%;">' . $programRjdatum . '</div>

<div class="text-box-small" style="top: 89.0%; left: 63.00%;">' . $programRjKlasa . '</div>

<div class="text-box-small" style="top: 89.00%; left: 78.60%;">' . $programRjurbr . '</div>


';

if (isset($data["gender"]) && $data["gender"] !== "") {
  if ($data["gender"] === "Žensko") {

    $html .= '<div class="text-box" style="top: 24.65%; left:85.9%;">X</div>';

      $html .= '<div class="text-box" style="top: 76.61%; left:14.60%;">x</div>';

      $html .= '<div class="text-box" style="top: 27.50%; left:39.7%;">x</div>';

      $html .= '<div class="text-box" style="top: 38.20%; left:12.50%;">x</div>';

      $html .= '<div class="text-box" style="top: 38.20%; left:20.30%;">x</div>';
  }
  else if ($data["gender"] === "Muško") {
    $html .= '<div class="text-box" style="top: 24.65%; left:89.1%;">X</div>';

    $html .= '<div class="text-box" style="top: 76.61%; left:16.1%;">x</div>';

    $html .= '<div class="text-box" style="top: 27.50%; left:41.90%;">x</div>';

    $html .= '<div class="text-box" style="top: 38.20%; left:13.80%;">x</div>';

    $html .= '<div class="text-box" style="top: 38.20%; left:21.40%;">x</div>';
}
}


$html .= '</body></html>';

  return $html;
}




private function convertDataToHtmlPrint($data, $programNaziv, $programRavnatelj, $programVoditeljEdukacijskeGrupe, $programDuljinaPrograma, $programDuljinaProgramaTekst, $programKlasaGodina, $profesijaClana, $programRjdatum, $programRjKlasa, $programRjurbr, $birthdateFormatted, $programClanklasabrojtekst, $datumprijabe, $zavrsenaedukacija, $brojclanskiidurbrojtekst, $formattedTodayDate, $formattedTodayDateYear, $listaArray, $programMaticniBroj )
{
    $data = (array) $data; // Convert object to array
    $listaArray = (array) $listaArray;


    $listHtml = '';

    // Split the string into individual items
    if (!empty($listaArray) && isset($listaArray[0])) {
     // Split the string into individual items
     $segments = explode(',', $listaArray[0]);

   $listHtml = '<div class="text-box-lista">';
   $listHtml .= '<ul style="list-style-type: none; padding-left: 0;">';

   foreach ($segments as $index => $segmentName) {
       $number = str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT); // This will ensure numbers are 01, 02, 03, etc.
       $listHtml .= "<li>{$number}.&nbsp;&nbsp;{$segmentName}</li>";
   }
 
 
 }


   // Split the string into individual items
 

   $listHtml .= '</ul>';
   $listHtml .= '</div>';



    $html = '<html><head><meta charset="UTF-8"><style>
    @page {
        margin: 0px;
    }
    /* Define the styles for the text boxes */
    .text-box {
     position: absolute;
      font-size: 23px;
      z-index: 2;
    }
    .text-box-lista {
        font-weight: lighter;
         font-size: 22.7px;
         z-index: 2;
    
       }

    .text-box-small {
        position: absolute;
         font-size: 15px;
         z-index: 2;
       }
    /* Define the styles for the labels */
    .label {
      position: absolute;
      font-size: 32px;
      //font-weight: bold;
      z-index: 2;
    }
    /* Define the styles for the background image */
    .bg {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1;
      
        /* Hide the image when printing */
        @media print {
          visibility: hidden;
        }
      }
  
  </style></head><body>

  <div class="bg"></div>

  <div class="text-box" style="top: 8.5%; left: 23%;">
    USTANOVA ZA OBRAZOVANJE ODRASLIH SKIJAŠKO UČILIŠTE
  </div>

  <div class="text-box" style="top: 11.4%; left: 39%;">
    Maksimirska 51, Zagreb
  </div>

  <div class="text-box" style="top: 14.4%; left: 13.1%;">' . $programClanklasabrojtekst . '</div>

  <div class="text-box" style="top: 14.4%; left: 44.3%;">' . $brojclanskiidurbrojtekst . '</div>

  <div class="text-box" style="top: 14.4%; left: 78.7%; text-align: center; width: 10%;">' . $programMaticniBroj . '</div>

  <div class="label" style="top: 23.7%; left:25%; text-align: center; width: 50%;">
  '.$data["firstname"].' '.$data["lastname"].'
</div>


<div class="text-box" style="top: 27.08%; left: 12.54%;">
'.implode('&nbsp;&nbsp;', str_split($data["oib"])).'
</div>


<div class="text-box" style="top: 27.37%; left: 50.33%;">'.$data["fatherfirstname"].' '.$data["fatherlastname"].' i '.$data["motherfirstname"].' '.$data["motherlastname"].'</div>

<div class="text-box" style="top: 29.90%; left: 18.38%; text-align: center; width: 15%;">'.$birthdateFormatted.'</div>

<div class="text-box" style="top: 29.90%; left: 53.00%;">'.$data["birthplace"].'</div>

<div class="text-box" style="top: 32.70%; left: 20.33%;">'.$data["birthcountry"].'</div>

<div class="text-box" style="top: 32.70%; left: 60.33%;">'.$data["citizenship"].'</div>


<div class="text-box" style="top: 35.20%; left: 25.00%;">' . $profesijaClana . '</div>

<div class="text-box" style="top: 40.70%; left: 10.00%; text-align: center; width: 80%;">' . $programNaziv . '</div>


<div class="text-box" style="top: 43.37%; left: 33.00%;">' . $programDuljinaProgramaTekst . '</div>

<div class="text-box" style="top: 43.37%; left: 77.00%;">' . $programDuljinaPrograma . '</div>

<div class="text-box" style="top: 46.00%; left: 23.00%;">' . $datumprijabe . '</div>

<div class="text-box" style="top: 46.00%; left: 43.00%;">' . $zavrsenaedukacija . '</div>

<div class="text-box" style="top: 49.60%; left: 10.20%;">' . $listHtml . '</div>


<div class="text-box" style="top: 76.00%; left: 24.33%;">' . $zavrsenaedukacija . '</div>

<div class="text-box" style="top: 79.00%; left: 10.00%; text-align: center; width: 80%;">' . $programNaziv . '</div>

<div class="text-box" style="top: 82.00%; left: 20.00%;">
 Zagrebu </div>

 <div class="text-box" style="top: 82.00%; left: 65.00%;">' . $formattedTodayDate . '</div>

 <div class="text-box" style="top: 82.00%; left: 80.54%;">' . $formattedTodayDateYear . '</div>

 <div class="text-box" style="top: 86.30%; left: 10.50%;">' . $programVoditeljEdukacijskeGrupe . '</div>

 <div class="text-box" style="top: 86.30%; left: 69.70%;">' . $programRavnatelj . '</div>

 <div class="text-box-small" style="top: 89.0%; left: 43.00%;">' . $programRjdatum . '</div>

<div class="text-box-small" style="top: 89.0%; left: 63.00%;">' . $programRjKlasa . '</div>

<div class="text-box-small" style="top: 89.00%; left: 78.60%;">' . $programRjurbr . '</div>


';

if (isset($data["gender"]) && $data["gender"] !== "") {
  if ($data["gender"] === "Žensko") {

    $html .= '<div class="text-box" style="top: 24.65%; left:85.9%;">X</div>';

      $html .= '<div class="text-box" style="top: 76.61%; left:14.60%;">x</div>';

      $html .= '<div class="text-box" style="top: 27.50%; left:39.7%;">x</div>';

      $html .= '<div class="text-box" style="top: 38.20%; left:12.50%;">x</div>';

      $html .= '<div class="text-box" style="top: 38.20%; left:20.30%;">x</div>';
  }
  else if ($data["gender"] === "Muško") {
    $html .= '<div class="text-box" style="top: 24.65%; left:89.1%;">X</div>';

    $html .= '<div class="text-box" style="top: 76.61%; left:16.1%;">x</div>';

    $html .= '<div class="text-box" style="top: 27.50%; left:41.90%;">x</div>';

    $html .= '<div class="text-box" style="top: 38.20%; left:13.80%;">x</div>';

    $html .= '<div class="text-box" style="top: 38.20%; left:21.40%;">x</div>';
}
}

$html .= '</body></html>';

  return $html;
}



private function convertDataToHtmlPrintCopy($data, $programNaziv, $programRavnatelj, $programVoditeljEdukacijskeGrupe, $programDuljinaPrograma, $programDuljinaProgramaTekst, $programKlasaGodina, $profesijaClana, $programRjdatum, $programRjKlasa, $programRjurbr, $birthdateFormatted, $programClanklasabrojtekst, $datumprijabe, $zavrsenaedukacija, $brojclanskiidurbrojtekst, $formattedTodayDate, $formattedTodayDateYear, $listaArray, $programMaticniBroj )
{
    $data = (array) $data; // Convert object to array
    $listaArray = (array) $listaArray;


    $listHtml = '';

    // Split the string into individual items
    if (!empty($listaArray) && isset($listaArray[0])) {
     // Split the string into individual items
     $segments = explode(',', $listaArray[0]);

   $listHtml = '<div class="text-box-lista">';
   $listHtml .= '<ul style="list-style-type: none; padding-left: 0;">';

   foreach ($segments as $index => $segmentName) {
       $number = str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT); // This will ensure numbers are 01, 02, 03, etc.
       $listHtml .= "<li>{$number}.&nbsp;&nbsp;{$segmentName}</li>";
   }
 
 
 }


   // Split the string into individual items
 

   $listHtml .= '</ul>';
   $listHtml .= '</div>';



    $html = '<html><head><meta charset="UTF-8"><style>
    @page {
        margin: 0px;
    }
    /* Define the styles for the text boxes */
    .text-box {
     position: absolute;
      font-size: 23px;
      z-index: 2;
    }
    .text-box-duplikat {
      position: absolute;
       font-size: 16px;
       z-index: 2;
     }
    .text-box-lista {
        font-weight: lighter;
         font-size: 22.7px;
         z-index: 2;
    
       }

    .text-box-small {
        position: absolute;
         font-size: 15px;
         z-index: 2;
       }
    /* Define the styles for the labels */
    .label {
      position: absolute;
      font-size: 32px;
      //font-weight: bold;
      z-index: 2;
    }
    /* Define the styles for the background image */
    .bg {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1;
      
        /* Hide the image when printing */
        @media print {
          visibility: hidden;
        }
      }
  
  </style></head><body>

  <div class="bg"></div>

  <div class="text-box" style="top: 8.5%; left: 23%;">
    USTANOVA ZA OBRAZOVANJE ODRASLIH SKIJAŠKO UČILIŠTE
  </div>

  <div class="text-box" style="top: 11.4%; left: 39%;">
    Maksimirska 51, Zagreb
  </div>

  <div class="text-box" style="top: 14.4%; left: 13.1%;">' . $programClanklasabrojtekst . '</div>

  <div class="text-box" style="top: 14.4%; left: 44.3%;">' . $brojclanskiidurbrojtekst . '</div>

  <div class="text-box" style="top: 14.4%; left: 78.7%; text-align: center; width: 10%;">' . $programMaticniBroj . '</div>

  <div class="text-box-duplikat" style="top: 22.4%; left:25%; text-align: center; width: 50%;">
  ( duplikat )
</div>

  <div class="label" style="top: 23.7%; left:25%; text-align: center; width: 50%;">
  '.$data["firstname"].' '.$data["lastname"].'
</div>


<div class="text-box" style="top: 27.08%; left: 12.54%;">
'.implode('&nbsp;&nbsp;', str_split($data["oib"])).'
</div>


<div class="text-box" style="top: 27.37%; left: 50.33%;">'.$data["fatherfirstname"].' '.$data["fatherlastname"].' i '.$data["motherfirstname"].' '.$data["motherlastname"].'</div>

<div class="text-box" style="top: 29.90%; left: 18.38%; text-align: center; width: 15%;">'.$birthdateFormatted.'</div>

<div class="text-box" style="top: 29.90%; left: 53.00%;">'.$data["birthplace"].'</div>

<div class="text-box" style="top: 32.70%; left: 20.33%;">'.$data["birthcountry"].'</div>

<div class="text-box" style="top: 32.70%; left: 60.33%;">'.$data["citizenship"].'</div>


<div class="text-box" style="top: 35.20%; left: 25.00%;">' . $profesijaClana . '</div>

<div class="text-box" style="top: 40.70%; left: 10.00%; text-align: center; width: 80%;">' . $programNaziv . '</div>


<div class="text-box" style="top: 43.37%; left: 33.00%;">' . $programDuljinaProgramaTekst . '</div>

<div class="text-box" style="top: 43.37%; left: 77.00%;">' . $programDuljinaPrograma . '</div>

<div class="text-box" style="top: 46.00%; left: 23.00%;">' . $datumprijabe . '</div>

<div class="text-box" style="top: 46.00%; left: 43.00%;">' . $zavrsenaedukacija . '</div>

<div class="text-box" style="top: 49.60%; left: 10.20%;">' . $listHtml . '</div>


<div class="text-box" style="top: 76.00%; left: 24.33%;">' . $zavrsenaedukacija . '</div>

<div class="text-box" style="top: 79.00%; left: 10.00%; text-align: center; width: 80%;">' . $programNaziv . '</div>

<div class="text-box" style="top: 82.00%; left: 20.00%;">
 Zagrebu </div>

 <div class="text-box" style="top: 82.00%; left: 65.00%;">' . $formattedTodayDate . '</div>

 <div class="text-box" style="top: 82.00%; left: 80.54%;">' . $formattedTodayDateYear . '</div>

 <div class="text-box" style="top: 86.30%; left: 10.50%;">' . $programVoditeljEdukacijskeGrupe . '</div>

 <div class="text-box" style="top: 86.30%; left: 69.70%;">' . $programRavnatelj . '</div>

 <div class="text-box-small" style="top: 89.0%; left: 43.00%;">' . $programRjdatum . '</div>

<div class="text-box-small" style="top: 89.0%; left: 63.00%;">' . $programRjKlasa . '</div>

<div class="text-box-small" style="top: 89.00%; left: 78.60%;">' . $programRjurbr . '</div>


';

if (isset($data["gender"]) && $data["gender"] !== "") {
  if ($data["gender"] === "Žensko") {

    $html .= '<div class="text-box" style="top: 24.65%; left:85.9%;">X</div>';

      $html .= '<div class="text-box" style="top: 76.61%; left:14.60%;">x</div>';

      $html .= '<div class="text-box" style="top: 27.50%; left:39.7%;">x</div>';

      $html .= '<div class="text-box" style="top: 38.20%; left:12.50%;">x</div>';

      $html .= '<div class="text-box" style="top: 38.20%; left:20.30%;">x</div>';
  }
  else if ($data["gender"] === "Muško") {
    $html .= '<div class="text-box" style="top: 24.65%; left:89.1%;">X</div>';

    $html .= '<div class="text-box" style="top: 76.61%; left:16.1%;">x</div>';

    $html .= '<div class="text-box" style="top: 27.50%; left:41.90%;">x</div>';

    $html .= '<div class="text-box" style="top: 38.20%; left:13.80%;">x</div>';

    $html .= '<div class="text-box" style="top: 38.20%; left:21.40%;">x</div>';
}
}

$html .= '</body></html>';

  return $html;
}


    
  public function citanjeispiti(Request $request)
  {
      try {
         
          $slug = $this->getSlug($request);
          $data_type = $this->getDataType($slug);
     
          $request->validate([
              'idedukacijskogsegmentaclana' => 'required|exists:'.$data_type->name,
          ]);

          $data = $this->getDataDetail3($slug, $request->idedukacijskogsegmentaclana);
          // add event notification handle
          $table_name = $data_type->name;
          FCMNotification::notification(FCMNotification::$ACTIVE_EVENT_ON_READ, $table_name);

          return ApiResponse::onlyEntity($data);
      } catch (Exception $e) {
          return ApiResponse::failed($e);
      }
  }

    
  public function citanje(Request $request)
  {
      try {
          $slug = $this->getSlug($request);
          $data_type = $this->getDataType($slug);
          
          // Validate the request, checking if idmember exists in the table
          $request->validate([
              'idmember' => 'required|exists:'.$data_type->name,
          ]);
  
          $data = $this->getDataDetail2($slug, $request->idmember);
          
          // Add event notification handle
          $table_name = $data_type->name;
          FCMNotification::notification(FCMNotification::$ACTIVE_EVENT_ON_READ, $table_name);
  
          return ApiResponse::onlyEntity($data);
      } catch (ValidationException $e) {
          return response()->json([
              'message' => 'Neispravni podaci',
              'errors' => $e->errors(),
          ], 422);
      } catch (Exception $e) {
          return ApiResponse::failed($e);
      }
  }
  

    public function edit(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'data' => [
                    'required',
                    function ($attribute, $value, $fail) use ($request) {
                        $slug = $this->getSlug($request);
                        $data_type = $this->getDataType($slug);
                        $table_entity = DB::table($data_type->name)->where('id', $request->data['id'])->first();
                        if (is_null($table_entity)) {
                            $fail(__('skijasi::validation.crud.id_not_exist'));
                        }
                    },
                ],
            ]);

            // get slug by route name and get data type
            $slug = $this->getSlug($request);
            $data_type = $this->getDataType($slug);

            // get data in request, validate, and update data
            $data = $request->input('data');
            $this->validateData($data, $data_type);
            $updated = $this->updateData($data, $data_type);

            DB::commit();
            activity($data_type->display_name_singular)
                ->causedBy(auth()->user() ?? null)
                ->withProperties([
                    'old' => $updated['old_data'],
                    'attributes' => $updated['updated_data'],
                ])
                ->log($data_type->display_name_singular.' je izmijenjeno');
            // add event notification handle
            $table_name = $data_type->name;
            FCMNotification::notification(FCMNotification::$ACTIVE_EVENT_ON_UPDATE, $table_name);


  // Check if the filetitle is 'KIF Diploma'
            if (isset($data['filetitle']) && $data['filetitle'] === 'KIF Diploma') {
                $this->sendDiplomaNotification($data['idmember']);
            }




            return ApiResponse::onlyEntity($updated['updated_data']);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failed($e);
        }
    }


 


    private function sendDiplomaNotification($userId)
    {
        try {
            // Use DB facade to query the 'skijasi_users' table directly
            $user = DB::table('skijasi_users')->where('id', $userId)->first();
    
            // Check if the user is found
            if (!$user) {
                throw new Exception('User not found');
            }

               // Save 'KIF' to the 'additional_info' column
        DB::table('skijasi_users')
        ->where('id', $userId)
        ->update(['additional_info' => 'KIF']);
    
            // Send email
            Mail::to($user->email)->send(new KIFDiplomaNotification($user));
    
            // Send FCM notification
        
              AdminMessage::create([
                  'message' => "{$user->name},\nObavještavamo Vas da prilikom dolaska na državni seminar dužni ste skijati u grupi.\n\nVaš HZUTS",
                  'sent_by' => 1, // Assuming 1 is the ID for system messages
                  'sent_to' => ["$user->id"],
                  'is_read' => [],
                  'is_hidden' => [],
              ]);
            


        
        } catch (Exception $e) {
            // Log the error, but don't throw it to prevent disrupting the main flow
            Log::error('Failed to send KIF Diploma notification: ' . $e->getMessage());
        }
    }
    
       
    public function sendNewHzutsNotificationAPI($slug, $userId)
    {
        try {
            Log::info("Attempting to send HZUTS notification. Slug: {$slug}, UserID: {$userId}");
            $this->sendNewHzutsNotification($userId);
            return response()->json(['message' => 'Notification sent successfully']);
        } catch (Exception $e) {
            Log::error("Failed to send HZUTS notification. Slug: {$slug}, UserID: {$userId}. Error: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    private function sendNewHzutsNotification($userId)
    {
        try {
            // Use DB facade to query the 'skijasi_users' table directly
            $user = DB::table('skijasi_users')->where('id', $userId)->first();
    
            // Check if the user is found
            if (!$user) {
                throw new Exception('User not found');
            }

    
            // Send email
            Mail::to($user->email)->send(new NewHzutsNotification($user));
    
            // Send FCM notification
        
              AdminMessage::create([
                  'message' => "{$user->name},\nčestitamo! Postali ste uspješno HZUTS član. Vaša prijava je odobrena!\n\nVaš HZUTS\n https://hzuts.hr",
                  'sent_by' => 1, // Assuming 1 is the ID for system messages
                  'sent_to' => ["$user->id"],
                  'is_read' => [],
                  'is_hidden' => [],
              ]);
            


        
        } catch (Exception $e) {
            // Log the error, but don't throw it to prevent disrupting the main flow
            Log::error('Failed to send New welcoming Hzuts member notification: ' . $e->getMessage());
        }
    }
  




    public function add(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'data' => [
                    'required',
                ],
            ]);

            // get slug by route name and get data type in table
            $slug = $this->getSlug($request);

            $data_type = $this->getDataType($slug);

            // get data from request
            $data = $request->input('data');

            // validate and store data to table
            $this->validateData($data, $data_type);
            $stored_data = $this->insertData($data, $data_type);

            activity($data_type->display_name_singular)
                ->causedBy(auth()->user() ?? null)
                ->withProperties(['attributes' => $stored_data])
                ->log($data_type->display_name_singular.' je dodano u bazu');

            DB::commit();

          

            // add event notification handle
            $table_name = $data_type->name;
            FCMNotification::notification(FCMNotification::$ACTIVE_EVENT_ON_CREATE, $table_name);

                // Check if the filetitle is 'Diploma'
                if (isset($data['filetitle']) && $data['filetitle'] === 'KIF Diploma') {
                  $this->sendDiplomaNotification($data['idmember']);
              }
  

            return ApiResponse::onlyEntity($stored_data);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failed($e);
        }
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'slug' => 'required',
                'data' => [
                    'required',
                    function ($attribute, $value, $fail) use ($request) {
                        $slug = $this->getSlug($request);
                        $data_type = $this->getDataType($slug);
                        $table_entity = DB::table($data_type->name)->where('id', $request->data[0]['value'])->first();

                        if (is_null($table_entity)) {
                            $fail(__('skijasi::validation.crud.id_not_exist'));
                        }
                    },
                ],
                'data.*.field' => ['required'],
                'data.*.value' => ['required'],
            ]);

            $is_hard_delete = $request->isHardDelete == 'true';

            $slug = $this->getSlug($request);
            $data_type = $this->getDataType($slug);

            $data = $this->createDataFromRaw($request->input('data') ?? [], $data_type);
            $this->deleteData($data, $data_type, $is_hard_delete);

            activity($data_type->display_name_singular)
                ->causedBy(auth()->user() ?? null)
                ->withProperties($data)
                ->log($data_type->display_name_singular.' je obrisano');

            DB::commit();

            // add event notification handle
            $table_name = $data_type->name;
            FCMNotification::notification(FCMNotification::$ACTIVE_EVENT_ON_DELETE, $table_name);

            return ApiResponse::onlyEntity($data);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failed($e);
        }
    }


    public function deleteByFormId(Request $request)
{
    DB::beginTransaction();
    try {
        $request->validate([
            'slug' => 'required',
            'formId' => 'required',
        ]);

        $slug = $this->getSlug($request);
        $data_type = $this->getDataType($slug);
        $form_id = $request->input('formId');

        // Find all records with the given formId
        $records = DB::table($data_type->name)->where('form_id', $form_id)->get();

        if ($records->isEmpty()) {
            throw new Exception(__('No records found for the given formId'));
        }

        // Delete all records with the given formId
        $deleted = DB::table($data_type->name)->where('form_id', $form_id)->delete();

        activity($data_type->display_name_singular)
            ->causedBy(auth()->user() ?? null)
            ->withProperties(['formId' => $form_id, 'deleted_count' => $deleted])
            ->log($data_type->display_name_singular . ' records were deleted for formId: ' . $form_id);

        DB::commit();

        // add event notification handle
        $table_name = $data_type->name;
        FCMNotification::notification(FCMNotification::$ACTIVE_EVENT_ON_DELETE, $table_name);

        return ApiResponse::success([
          'status' => 'success',  // Add status field
          'message' => 'Successfully deleted all records for formId: ' . $form_id,
          'deleted_count' => $deleted
      ]);
  } catch (Exception $e) {
      DB::rollBack();
      return ApiResponse::failed($e);
  }
}

    public function restore(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'slug' => 'required',
                'data' => [
                    'required',
                ],
                'data.*.field' => ['required'],
                'data.*.value' => ['required'],
            ]);

            $slug = $this->getSlug($request);
            $data_type = $this->getDataType($slug);

            $data = $this->createDataFromRaw($request->input('data') ?? [], $data_type);
            $this->restoreData($data, $data_type);

            activity($data_type->display_name_singular)
                ->causedBy(auth()->user() ?? null)
                ->withProperties($data)
                ->log($data_type->display_name_singular.' je vraćeno');

            DB::commit();

            return ApiResponse::onlyEntity($data);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failed($e);
        }
    }

    public function deleteMultiple(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'slug' => 'required',
                'data' => [
                    'required',
                    function ($attribute, $value, $fail) use ($request) {
                        $slug = $this->getSlug($request);
                        $data_type = $this->getDataType($slug);

                        $data = $this->createDataFromRaw($request->input('data') ?? [], $data_type);
                        $ids = $data['ids'];
                        $id_list = explode(',', $ids);
                        foreach ($id_list as $id) {
                            $table_entity = DB::table($data_type->name)->where('id', $id)->first();
                            if (is_null($table_entity)) {
                                $fail(__('skijasi::validation.crud.id_not_exist'));
                            }
                        }
                    },
                ],
                'data.*.field' => ['required'],
                'data.*.value' => ['required'],
            ]);

            $is_hard_delete = $request->isHardDelete == 'true';

            $slug = $this->getSlug($request);
            $data_type = $this->getDataType($slug);

            $data = $this->createDataFromRaw($request->input('data') ?? [], $data_type);
            $ids = $data['ids'];
            $id_list = explode(',', $ids);
            foreach ($id_list as $id) {
                $should_delete['id'] = $id;
                $this->deleteData($should_delete, $data_type, $is_hard_delete);
            }

            activity($data_type->display_name_singular)
                ->causedBy(auth()->user() ?? null)
                ->withProperties($data)
                ->log($data_type->display_name_singular.' je obrisano');

            DB::commit();

            return ApiResponse::onlyEntity($data);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failed($e);
        }
    }

    public function restoreMultiple(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'slug' => 'required',
                'data' => [
                    'required',
                ],
                'data.*.field' => ['required'],
                'data.*.value' => ['required'],
            ]);

            $slug = $this->getSlug($request);
            $data_type = $this->getDataType($slug);

            $data = $this->createDataFromRaw($request->input('data') ?? [], $data_type);
            $ids = $data['ids'];
            $id_list = explode(',', $ids);
            foreach ($id_list as $id) {
                $should_delete['id'] = $id;
                $this->restoreData($should_delete, $data_type);
            }

            activity($data_type->display_name_singular)
                ->causedBy(auth()->user() ?? null)
                ->withProperties($data)
                ->log($data_type->display_name_singular.' je vraćeno iz backupa');

            DB::commit();

            return ApiResponse::onlyEntity($data);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failed($e);
        }
    }

    public function sort(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'slug' => 'required',
                'data' => [
                    'required',
                ],
            ]);

            $slug = $this->getSlug($request);
            $data_type = $this->getDataType($slug);
            $order_column = $data_type->order_column;

            if ($data_type->model_name) {
                $model = app($data_type->model_name);
                foreach ($request->data as $index => $row) {
                    $single_data = $model::find($row['id']);
                    $single_data[$order_column] = $index + 1;
                    $single_data->save();

                    // activity($data_type->display_name_singular)
                    //     ->causedBy(auth()->user() ?? null)
                    //     ->withProperties(['attributes' => $single_data])
                    //     ->log($data_type->display_name_singular.' je sortirano');
                }
            } else {
                foreach ($request->data as $index => $row) {
                    $updated_data[$order_column] = $index + 1;
                    DB::table($data_type->name)->where('id', $row['id'])->update($updated_data);

                    // activity($data_type->display_name_singular)
                    //     ->causedBy(auth()->user() ?? null)
                    //     ->withProperties(['attributes' => $updated_data])
                    //     ->log($data_type->display_name_singular.' je sortirano');
                }
            }

            DB::commit();

            return ApiResponse::onlyEntity();
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failed($e);
        }
    }

    public function setMaintenanceState(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'slug' => 'required|exists:NadzorServera\Skijasi\Models\DataType,slug',
                'is_maintenance' => 'required',
            ]);

            $data_type = DataType::where('slug', $request->slug)->firstOrFail();
            $data_type->is_maintenance = $request->is_maintenance ? 1 : 0;
            $data_type->save();

            DB::commit();

            return ApiResponse::success();
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failed($e);
        }
    }





    public function getFormEntries(Request $request)
    {
        try {
            $formEntries = DB::table('skijasi_form_entries')
                ->select([
                    'skijasi_form_entries.id',
                    'skijasi_form_entries.userid', // Added userid
                    'skijasi_form_entries.form_id',
                    'skijasi_form_entries.data',
                    'skijasi_form_entries.hotel',
                    'skijasi_form_entries.created_at',
                    'skijasi_forms.name as formName'
                ])
                ->leftJoin('skijasi_forms', 'skijasi_form_entries.form_id', '=', 'skijasi_forms.id')
                ->whereNotNull('data')
                ->orderBy('skijasi_form_entries.created_at', 'desc')
                ->get();
    
            // Process entries and extract unique hotels and seminars
            $processedEntries = collect();
            $uniqueHotels = collect();
            $uniqueSeminars = collect();
    
            foreach ($formEntries as $entry) {
                try {
                    $data = json_decode($entry->data, true);
                    
                    // Add to processed entries
                    $processedEntry = [
                        'id' => $entry->id, // Keep original id
                        'userid' => $entry->userid, // Add userid
                        'formId' => $entry->form_id,
                        'formName' => $entry->formName,
                        'hotel' => $entry->hotel,
                        'createdAt' => $entry->created_at,
                        'data' => $data
                    ];
                    $processedEntries->push($processedEntry);
    
                    // Collect unique hotels
                    if (!empty($entry->hotel)) {
                        $uniqueHotels->push($entry->hotel);
                    }
    
                    // Collect unique seminars from formName
                    if (!empty($entry->formName)) {
                        $uniqueSeminars->push($entry->formName);
                    }
                } catch (\Exception $e) {
                    Log::error("Error processing form entry {$entry->id}: " . $e->getMessage());
                    continue;
                }
            }
    
            return ApiResponse::success([
                'entries' => $processedEntries->values(),
                'uniqueHotels' => $uniqueHotels->unique()->values(),
                'uniqueSeminars' => $uniqueSeminars->unique()->values()
            ]);
    
        } catch (Exception $e) {
            Log::error('Error in getFormEntries:', ['error' => $e->getMessage()]);
            return ApiResponse::failed($e);
        }
    }
    public function getFormEntriesByHotel(Request $request)
    {
        try {
            $hotel = $request->input('hotel');
            
            if (!$hotel) {
                throw new Exception('Hotel parameter is required');
            }
    
            $entries = DB::table('skijasi_form_entries')
                ->select([
                    'skijasi_form_entries.id',
                    'skijasi_form_entries.form_id',
                    'skijasi_form_entries.data',
                    'skijasi_form_entries.hotel',
                    'skijasi_form_entries.created_at',
                    'skijasi_forms.name as form_name'
                ])
                ->leftJoin('skijasi_forms', 'skijasi_form_entries.form_id', '=', 'skijasi_forms.id')
                ->where('hotel', $hotel)
                ->orderBy('created_at', 'desc')
                ->get();
    
            $processedEntries = $entries->map(function ($entry) {
                try {
                    return [
                        'id' => $entry->id,
                        'form_id' => $entry->form_id,
                        'form_name' => $entry->form_name,
                        'hotel' => $entry->hotel,
                        'created_at' => $entry->created_at,
                        'data' => json_decode($entry->data, true)
                    ];
                } catch (\Exception $e) {
                    Log::error("Error processing form entry {$entry->id}: " . $e->getMessage());
                    return null;
                }
            })->filter();
    
            return ApiResponse::success(['data' => $processedEntries]);
    
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }
    
    public function getFormEntriesByFormId(Request $request)
    {
        try {
            $formId = $request->input('form_id');
            
            if (!$formId) {
                throw new Exception('Form ID parameter is required');
            }
    
            $entries = DB::table('skijasi_form_entries')
                ->select([
                    'skijasi_form_entries.id',
                    'skijasi_form_entries.form_id',
                    'skijasi_form_entries.data',
                    'skijasi_form_entries.hotel',
                    'skijasi_form_entries.created_at',
                    'skijasi_forms.name as form_name'
                ])
                ->leftJoin('skijasi_forms', 'skijasi_form_entries.form_id', '=', 'skijasi_forms.id')
                ->where('form_id', $formId)
                ->orderBy('created_at', 'desc')
                ->get();
    
            $processedEntries = $entries->map(function ($entry) {
                try {
                    return [
                        'id' => $entry->id,
                        'form_id' => $entry->form_id,
                        'form_name' => $entry->form_name,
                        'hotel' => $entry->hotel,
                        'created_at' => $entry->created_at,
                        'data' => json_decode($entry->data, true)
                    ];
                } catch (\Exception $e) {
                    Log::error("Error processing form entry {$entry->id}: " . $e->getMessage());
                    return null;
                }
            })->filter();
    
            return ApiResponse::success(['data' => $processedEntries]);
    
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }


}
