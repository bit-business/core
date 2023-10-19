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
        $gradovipdf = $request->input('gradovipdf');
        $isiapdf = $request->input('isiapdf');
        $isiayear = $request->input('isiayear');
        $todaydate = $request->input('todaydate');
        $postanskibrojpdf = $request->input('postanskibrojpdf');

      $options = new \Dompdf\Options();
      
      $options->set('isHtml5ParserEnabled', true);
      $options->set('isRemoteEnabled', true);
      $options->set('dpi', 75);
      $options->set('defaultFont', 'Times-Roman');
      $options->set('isFontSubsettingEnabled', true);

      $options->set('margin_left', 0);
      $options->set('margin_right', 0);
      $options->set('margin_top', 0);
      $options->set('margin_bottom', 0);
   
        $options->set('tempDir', '/var/www/nadzornaploca/storage/');

      $dompdf = new \Dompdf\Dompdf($options);
    //  $dompdf->setBasePath($_SERVER['DOCUMENT_ROOT']); 

    // Load your template.pdf file
    $dompdf->set_paper('A4', 'portrait');
    // Query the database to get the data
    $data = DB::table('skijasi_users')->where('id', $request->id)->first();

// Convert the data to HTML and add it to the PDF
$html = $this->convertDataToHtmlPOTVRDAISIA($data, $isiapdf, $isiayear, $todaydate, $gradovipdf, $postanskibrojpdf);


   // Use Browsershot to convert the HTML to an image
   $imagePath = public_path('storage/slike/baza/pdfimagetemp.png');
   Browsershot::html($html)
       ->noSandbox() // Depending on your server, you might need to use the noSandbox option
       ->save($imagePath);
          // Create HTML for the PDF embedding the image
    $pdfHtml = '<!DOCTYPE html>
    <html>
    <body>
        <img src="' . $imagePath . '" style="width: 100%; height: auto;">
    </body>
    </html>';


    $dompdf->loadHtml($pdfHtml, 'UTF-8');

    // Render the PDF
    $dompdf->render();

    $dompdf->stream();
 
    // Output the generated PDF
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
        $gradovipdf = $request->input('gradovipdf');
        $todaydate = $request->input('todaydate');
        $year = $request->input('year');
        $postanskibrojpdf = $request->input('postanskibrojpdf');

      $options = new \Dompdf\Options();
      
      $options->set('isHtml5ParserEnabled', true);
      $options->set('isRemoteEnabled', true);
      $options->set('dpi', 75);
      $options->set('defaultFont', 'Times-Roman');
      $options->set('isFontSubsettingEnabled', true);

      $options->set('margin_left', 0);
      $options->set('margin_right', 0);
      $options->set('margin_top', 0);
      $options->set('margin_bottom', 0);
   
        $options->set('tempDir', '/var/www/nadzornaploca/storage/');

      $dompdf = new \Dompdf\Dompdf($options);
    //  $dompdf->setBasePath($_SERVER['DOCUMENT_ROOT']); 

    // Load your template.pdf file
    $dompdf->set_paper('A4', 'portrait');
    // Query the database to get the data
    $data = DB::table('skijasi_users')->where('id', $request->id)->first();

// Convert the data to HTML and add it to the PDF
$html = $this->convertDataToHtmlPOTVRDAIVSI($data, $todaydate, $year, $gradovipdf, $postanskibrojpdf);

$dompdf->loadHtml($html, 'UTF-8');

    // Render the PDF
    $dompdf->render();

    $dompdf->stream();
 
    // Output the generated PDF
    $output = $dompdf->output();

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
        $gradovipdf = $request->input('gradovipdf');


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

    // Load your template.pdf file
   // $dompdf->set_paper('A4', 'portrait');
   $dompdf->set_paper(array(0, 0, 158.7, 248.6), 'landscape');

    

    // Query the database to get the data
    $data = DB::table('skijasi_users')->where('id', $request->id)->first();
    





// Convert the data to HTML and add it to the PDF
$html = $this->convertDataToHtmlID($data, $cardscro, $cardseng, $gradovipdf);

 

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

public function zadnjimaticni() {
    // Retrieve the latest non-null maticni number from your database
    $lastMaticni = DB::table('su_clanoviedukacijskipodaci')
                     ->whereNotNull('maticnibroj')
                     ->orderBy('id', 'desc')
                     ->first()
                     ->maticnibroj;

                 
       $lastMaticni = $lastRecord ? $lastRecord->maticnibroj : "";

    return response()->json(['maticnibroj' => $lastMaticni]);
}



public function generateisiagodinu(Request $request)
{
    // Receive numbers and isiayear from the request
    $numbers = $request->input('numbers'); // This should be an array
    $isiayear = $request->input('isiayear');

    $yearToCheck = $isiayear - 1; // Define the year you want to check against

    $opendate = new \DateTime('2024-10-01 00:00:00');

    // Start a database transaction
    DB::beginTransaction();

    try {
        // Retrieve 'idmember' values where 'dateendmember' is NULL and 'isiayear' is the specified year
        $validMembers = DB::table('skijasi_users')
            ->join('tbl_isia_member', 'skijasi_users.idmember', '=', 'tbl_isia_member.idmember')
            ->whereNull('skijasi_users.dateendmember')
            ->where('tbl_isia_member.isiayear', $yearToCheck)
            ->where('skijasi_users.user_type', 'Hzuts član') 
            ->select('skijasi_users.idmember')
            ->distinct() // Ensure you're only getting distinct 'idmember's
            ->get();

        // Fetch members who didn't have the specified isiayear
        $otherMembers = DB::table('skijasi_users')
            ->join('tbl_isia_member', 'skijasi_users.idmember', '=', 'tbl_isia_member.idmember')
            ->whereNull('skijasi_users.dateendmember')
            ->where('tbl_isia_member.isiayear', '!=', $yearToCheck)
            ->where('skijasi_users.user_type', 'Hzuts član')
            ->select('skijasi_users.idmember')
            ->distinct()
            ->get();

        // Check if there are any valid members
        if ($validMembers->isEmpty() && $otherMembers->isEmpty()) {
            // No valid members found
            DB::rollback();
            return; // Return or handle this case as per your requirement
        }

        // Check if the counts match
        if (count($numbers) !== count($validMembers)) {
            // Handle error: The number of members doesn't match the number of provided numbers
            DB::rollback();
            return; // Return or handle this case as per your requirement
        }

        // Process members with specified isiayear
        for ($i = 0; $i < count($validMembers); $i++) {

            
            // Insert into tbl_isia_member
            DB::table('tbl_isia_member')->insert([
                'idisia' => $numbers[$i], // Assigns a unique number to a member
                'idmember' => $validMembers[$i]->idmember,
                'isiayear' => $isiayear,
            ]);

            // Insert into tbl_payments
            DB::table('tbl_payments')->insert([
                'idmember' => $validMembers[$i]->idmember, // the same idmember
                'paymenttitle' => 'ISIA članarina - ' . $isiayear, // e.g., "ISIA članarina - 2024"
                'idpaygroup' => 2,
                'idpaysubgroup' => 135,
                'price' => 27,
                'opendate' => $opendate->format('Y-m-d H:i:s'), // '2024-10-01 00:00:00'
                // ... any other necessary fields
            ]);


  // Construct callnumber with the inserted ID and idmember
  $paymentId = DB::getPdo()->lastInsertId();
  $callnumber = $paymentId . '-' . $validMembers[$i]->idmember;

  // Update the payment entry with the callnumber
  DB::table('tbl_payments')
      ->where('id', $paymentId) // assuming 'id' is the primary key of tbl_payments
      ->update(['callnumber' => $callnumber]);


        }


// godisnja clanarina
        // Process members without specified isiayear
        foreach ($otherMembers as $member) {
            // Perform necessary actions for these members, for example:
            DB::table('tbl_payments')->insert([
                'idmember' => $member->idmember, // the same idmember
                'paymenttitle' => 'Godišnja članarina - ' . $isiayear, // e.g., "ISIA članarina - 2024"
                'idpaygroup' => 1, // or any other appropriate value
                'idpaysubgroup' => 133, // or any other appropriate value
                'price' => 27,
                'opendate' => $opendate->format('Y-m-d H:i:s'), // '2024-10-01 00:00:00'
                // ... any other necessary fields
            ]);

    $paymentId = DB::getPdo()->lastInsertId();
   // Construct callnumber with the inserted ID and idmember
   $callnumber = $paymentId . '-' . $member->idmember;

   // Update the payment entry with the callnumber
   DB::table('tbl_payments')
       ->where('id', $paymentId) // assuming 'id' is the primary key of tbl_payments
       ->update(['callnumber' => $callnumber]);

        }

        // Commit the transaction
        DB::commit();
    } catch (\Exception $e) {
        // Log the error message for debugging
        \Log::error($e->getMessage());
        DB::rollback();
        return; // Replace with your error handling
    }
    

    // Return a response or carry out any subsequent logic
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




private function convertDataToHtmlPOTVRDAISIA($data, $isiapdf, $isiayear, $todaydate, $gradovipdf, $postanskibrojpdf)
{
    $data = (array) $data; // Convert object to array

    $imagePath = 'storage/slike/baza/bg1.png';

    // Read the image content
    $imageData = file_get_contents($imagePath);
    
    // Encode the image data to base64
    $base64Image = base64_encode($imageData);



$html = '
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="initial-scale=1, width=device-width" />

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
        height: 98%;
        background-image: url("https://firebasestorage.googleapis.com/v0/b/hzuts-47aa0.appspot.com/o/logowatermark.png?alt=media&token=9595ae3f-fd0e-4384-b007-b567401190ea&_gl=1*52xatq*_ga*MTIwOTgzOTkyNy4xNjk2NTUzMTA5*_ga_CW55HF8NVT*MTY5NzYzNDQ5MS42LjEuMTY5NzYzNDUwNC40Ny4wLjA.");
        background-repeat: no-repeat;
        background-size: contain;
        z-index: 1;
      
        /* Hide the image when printing */
        @media print {
          visibility: hidden;
        }
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
            left: 0px;
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
            left: 383.39px;
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
          left: 305.39px;
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
            top: 662.59px;
            left: 417.9px;
            font-size: 12.69px;
          "
        >
        '.$todaydate.'
        </div>
        <div
          style="
            position: absolute;
            top: 661.59px;
            left: 311.7px;
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
            top: 432.1px;
            left: 70.9px;
            font-size: 10.31px;
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
            top: 387.15px;
            left: 227.15px;
            font-size: 11.44px;
          "
        >
          per '.$isiayear.'.
        </div>
        <div
          style="
            position: absolute;
            top: 387.15px;
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
          top: 387.15px;
          left: 176.9px;
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
            top: 341.1px;
            left: 70.9px;
            font-size: 10.69px;
          "
        >
          residente in Croazia: '.$data["adresa"].', '.$postanskibrojpdf.' '.$gradovipdf.'
        </div>
        <div
          style="
            position: absolute;
            top: 324.1px;
            left: 424.9px;
            font-size: 12px;
          "
        >
        '.$data["idmember"].'
        </div>
        <div style="position: absolute; top: 327.1px; left: 354.1px">
          tessera No
        </div>
        <div style="position: absolute; top: 325px; left: 247.9px">
        '.$data["name"].' '.$data["username"].'
        </div>
        <div
          style="
            position: absolute;
            top: 326.1px;
            left: 70.9px;
            font-size: 10.88px;
          "
        >
          Si dichiara che il Sig/ra:
        </div>
        <b
          style="
            position: absolute;
            top: 236.1px;
            left: 267.85px;
            font-size: 11.5px;
          "
          >Certificato</b
        >
        <div
        style="
          position: absolute;
          top: 100.1px;
          left: 70.9px;
          font-size: 10.69px;
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


private function convertDataToHtmlPOTVRDAIVSI($data, $todaydate, $year, $gradovipdf, $postanskibrojpdf)
{
    $data = (array) $data; // Convert object to array

    $imagePath = 'storage/slike/baza/bg2.png';

    // Read the image content
    $imageData = file_get_contents($imagePath);
    
    // Encode the image data to base64
    $base64Image = base64_encode($imageData);



$html = '
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="initial-scale=1, width=device-width" />

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
        height: 98%;
        background-image: url("https://firebasestorage.googleapis.com/v0/b/hzuts-47aa0.appspot.com/o/logowatermark.png?alt=media&token=9595ae3f-fd0e-4384-b007-b567401190ea&_gl=1*52xatq*_ga*MTIwOTgzOTkyNy4xNjk2NTUzMTA5*_ga_CW55HF8NVT*MTY5NzYzNDQ5MS42LjEuMTY5NzYzNDUwNC40Ny4wLjA.");
        background-repeat: no-repeat;
        background-size: contain;
        z-index: 1;
      
        /* Hide the image when printing */
        @media print {
          visibility: hidden;
        }
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
            left: 0px;
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
            left: 383.39px;
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
          left: 305.39px;
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
            top: 662.59px;
            left: 417.9px;
            font-size: 12.69px;
          "
        >
        '.$todaydate.'
        </div>
        <div
          style="
            position: absolute;
            top: 661.59px;
            left: 311.7px;
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
            top: 432.1px;
            left: 70.9px;
            font-size: 10.31px;
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
            top: 387.15px;
            left: 170.15px;
            font-size: 11.44px;
          "
        >
          per '.$year.'.
        </div>
        <div
          style="
            position: absolute;
            top: 387.15px;
            left: 70.9px;
            font-size: 11.44px;
          "
        >
          <span>con certificato </span>
          <b>IVSI</b>
        
        </div>
    
        <div style="position: absolute; top: 372.1px; left: 70.9px">
          In possesso del titolo Croato di Maestro di Sci
        </div>
        <div
          style="
            position: absolute;
            top: 341.1px;
            left: 70.9px;
            font-size: 10.69px;
          "
        >
          residente in Croazia: '.$data["adresa"].', '.$postanskibrojpdf.' '.$gradovipdf.'
        </div>
        <div
          style="
            position: absolute;
            top: 324.1px;
            left: 424.9px;
            font-size: 12px;
          "
        >
        '.$data["idmember"].'
        </div>
        <div style="position: absolute; top: 327.1px; left: 354.1px">
          tessera No
        </div>
        <div style="position: absolute; top: 325px; left: 247.9px">
        '.$data["name"].' '.$data["username"].'
        </div>
        <div
          style="
            position: absolute;
            top: 326.1px;
            left: 70.9px;
            font-size: 10.88px;
          "
        >
          Si dichiara che il Sig/ra:
        </div>
        <b
          style="
            position: absolute;
            top: 236.1px;
            left: 267.85px;
            font-size: 11.5px;
          "
          >Certificato</b
        >
        <div
        style="
          position: absolute;
          top: 100.1px;
          left: 70.9px;
          font-size: 10.69px;
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



private function convertDataToHtmlID($data, $cardscro, $cardseng, $gradovipdf)
{
    $data = (array) $data; // Convert object to array

$imagePath = 'storage/' . $data['avatar'];

// Read the image content
$imageData = file_get_contents($imagePath);

// Encode the image data to base64
$base64Image = base64_encode($imageData);



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
        width: 32%;
        height: 56%; /* Adjust based on desired height */
        display: flex;
        justify-content: center; /* Center image horizontally */
        align-items: center; /* Center image vertically */
        overflow: hidden; /* Ensure no part of the image spills out */
    }
    
    .image-container img {
        max-width: 100%;
        max-height: 100%;
        width: auto;
        height: auto;
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

  <div class="labeltop1" style="top: 2.0%; left:0%; text-align: right; width: 54%;">
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
  '.$gradovipdf.'
</div>

<div class="label" style="top: 63.0%; left: 35%; ">
'.$data["id"].'
</div>';




$html .= '</body></html>';

  return $html;
}



private function convertDataToHtml($data, $programNaziv, $programRavnatelj, $programVoditeljEdukacijskeGrupe, $programDuljinaPrograma, $programDuljinaProgramaTekst, $programKlasaGodina, $profesijaClana, $programRjdatum, $programRjKlasa, $programRjurbr, $birthdateFormatted, $programClanklasabrojtekst, $datumprijabe, $zavrsenaedukacija, $brojclanskiidurbrojtekst, $formattedTodayDate, $formattedTodayDateYear, $listaArray, $programMaticniBroj )
{
    $data = (array) $data; // Convert object to array
    $listaArray = (array) $listaArray;

   // Split the string into individual items
   $segments = explode(',', $listaArray[0]);

   $listHtml = '<div class="text-box-lista">';
   $listHtml .= '<ul style="list-style-type: none; padding-left: 0;">';

   foreach ($segments as $index => $segmentName) {
       $number = str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT); // This will ensure numbers are 01, 02, 03, etc.
       $listHtml .= "<li>{$number}.&nbsp;&nbsp;{$segmentName}</li>";
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

 <div class="text-box" style="top: 82.00%; left: 66.00%;">' . $formattedTodayDate . '</div>

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

   // Split the string into individual items
   $segments = explode(',', $listaArray[0]);

   $listHtml = '<div class="text-box-lista">';
   $listHtml .= '<ul style="list-style-type: none; padding-left: 0;">';

   foreach ($segments as $index => $segmentName) {
       $number = str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT); // This will ensure numbers are 01, 02, 03, etc.
       $listHtml .= "<li>{$number}.&nbsp;&nbsp;{$segmentName}</li>";
   }

   $listHtml .= '</ul>';
   $listHtml .= '</div>';

\Log::info('Generated listHtml:', (array)$listHtml);

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

 <div class="text-box" style="top: 82.00%; left: 66.00%;">' . $formattedTodayDate . '</div>

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
          $request->validate([
              'idedukacijskogsegmentaclana' => 'required',
          ]);
          $slug = $this->getSlug($request);
          $data_type = $this->getDataType($slug);
          $request->validate([
              'idedukacijskogsegmentaclana' => 'exists:'.$data_type->name,
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
            $request->validate([
                'idmember' => 'required',
            ]);
            $slug = $this->getSlug($request);
            $data_type = $this->getDataType($slug);
            $request->validate([
                'idmember' => 'exists:'.$data_type->name,
            ]);

            $data = $this->getDataDetail2($slug, $request->idmember);
            
            // add event notification handle
            $table_name = $data_type->name;
            FCMNotification::notification(FCMNotification::$ACTIVE_EVENT_ON_READ, $table_name);

            return ApiResponse::onlyEntity($data);
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
                ->log($data_type->display_name_singular.' je izmijenjen');
            // add event notification handle
            $table_name = $data_type->name;
            FCMNotification::notification(FCMNotification::$ACTIVE_EVENT_ON_UPDATE, $table_name);

            return ApiResponse::onlyEntity($updated['updated_data']);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failed($e);
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
                ->log($data_type->display_name_singular.' has been created');

            DB::commit();

            // add event notification handle
            $table_name = $data_type->name;
            FCMNotification::notification(FCMNotification::$ACTIVE_EVENT_ON_CREATE, $table_name);

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
                ->log($data_type->display_name_singular.' has been deleted');

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
                ->log($data_type->display_name_singular.' has been restore');

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
                ->log($data_type->display_name_singular.' has been bulk deleted');

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
                ->log($data_type->display_name_singular.' has been bulk deleted');

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

                    activity($data_type->display_name_singular)
                        ->causedBy(auth()->user() ?? null)
                        ->withProperties(['attributes' => $single_data])
                        ->log($data_type->display_name_singular.' has been sorted');
                }
            } else {
                foreach ($request->data as $index => $row) {
                    $updated_data[$order_column] = $index + 1;
                    DB::table($data_type->name)->where('id', $row['id'])->update($updated_data);

                    activity($data_type->display_name_singular)
                        ->causedBy(auth()->user() ?? null)
                        ->withProperties(['attributes' => $updated_data])
                        ->log($data_type->display_name_singular.' has been sorted');
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
}
