<?php

namespace NadzorServera\Skijasi\Controllers;

use Illuminate\Http\Request;
use NadzorServera\Skijasi\Helpers\ApiResponse;
use UniSharp\LaravelFilemanager\Controllers\DeleteController;
use UniSharp\LaravelFilemanager\Controllers\ItemsController;
use UniSharp\LaravelFilemanager\Controllers\UploadController;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;



class SkijasiFileController extends Controller
{
    public function uploadFile(Request $request)
    {
        $files = $request->input('files', []);

        return $this->handleUploadFiles($files);
    }

    public function downloadFile(Request $request)
    {
        $file = $request->input('file', []);

        return $this->handleDownloadFile($file);
    }

    public function deleteFile(Request $request)
    {
        $file = $request->input('file', []);

        $this->handleDeleteFile($file);
        
    }

    public function viewFile(Request $request)
    {
        $file = $request->input('file', []);

        return $this->handleViewFile($file);
    }

    /**
     * @param  int  $page  => current page number
     * @param  string  $working_dir  => working directory
     * @param  string  $sort_type  => sorting type: name | date
     * @return mixed
     */
    public function browseFileUsingLfm(Request $request)
    {
        $item = new ItemsController();
        $file = $item->getItems();

        return ApiResponse::success(json_decode(json_encode($file)));
    }

    public function uploadFileUsingLfm(Request $request)
    {
        $upload = new UploadController();
        $file = $upload->upload();

        if (key_exists('error', $file->original)) {
            return ApiResponse::failed($file);
        }

        return ApiResponse::success(json_decode(json_encode($file)));
    }

    public function deleteFileUsingLfm(Request $request)
    {
        $delete = new DeleteController();
        $file = $delete->getDelete();

        return ApiResponse::success(json_decode(json_encode($file)));
    }

    public function availableMimetype()
    {
        return ApiResponse::success(config('lfm.folder_categories'));
    }




    public function getFolders(Request $request) {
        // Default to the storage path if no directory is provided
        $defaultPath = storage_path('app/public/galerija');
        $directory = $request->input('directory', $defaultPath);
        
        // Check if directory exists
        if (!File::exists($directory)) {
            return response()->json(['error' => 'Directory not found', 'directory' => $directory], 404);
        }
        
        $folders = Storage::disk('public')->directories(str_replace(storage_path('app/public/'), '', $directory));
        
        if (empty($folders)) {
           return response()->json(['error' => 'No folders found'], 404);
        }
        
        return response()->json($folders);
    }
    
    
    /**
 * Get images from storage/slike directory za Galerija
 */
public function getImagesFromSlike()
{
    // Initialize ItemsController from Laravel File Manager
    $itemController = new ItemsController();

    // Set the working directory to 'slike'
    request()->merge(['working_dir' => '/photos/galerija']);
    
    // Fetch the items from 'slike' directory
    $files = $itemController->getItems();

    // Assuming the result contains images, you may want to filter it if it contains non-image files
    $images = array_map(function($file) {
        return $file->url;
    }, $files);

    // Return the response
    return ApiResponse::success($images);
}



public function customUploadFile(Request $request)
{


    $file = $request->file('file');
    $nameusr = $request->input('nameusr');
    $prezimeusr = $request->input('prezimeusr');
    $idmember = $request->input('idmember');

    // Create the filename
    $filename = $nameusr . '_' . $prezimeusr . '_' . $idmember . '.' . $file->getClientOriginalExtension();

    // Define the path where the file will be stored
    $path = 'profilephoto';

    // Store the file
    $storagePath = Storage::putFileAs($path, $file, $filename);

    // Check if the file has been saved successfully
    if ($storagePath) {
        return ApiResponse::success(['message' => 'File uploaded successfully', 'path' => $storagePath]);
    } else {
        return ApiResponse::failed(['message' => 'Failed to upload the file ...']);
    }
}



public function customUploadFileVijesti(Request $request)
{
    $file = $request->file('file');

    // Get the number of existing files in the directory
    $existingFilesCount = count(Storage::files('vijestislike'));

    // Increment the count to get the next number for the new file
    $nextFileNumber = $existingFilesCount + 1;

    // Create the filename
    $filename = 'slika' . $nextFileNumber . '.' . $file->getClientOriginalExtension();

    // Define the path where the file will be stored
    $path = 'vijestislike';

    // Store the file
    $storagePath = Storage::putFileAs($path, $file, $filename);

    // Check if the file has been saved successfully
    if ($storagePath) {
        return ApiResponse::success(['message' => 'File uploaded successfully', 'path' => $storagePath]);
    } else {
        return ApiResponse::failed(['message' => 'Failed to upload the file..']);
    }
}

public function customUploadFileDogadaji(Request $request)
{
    $file = $request->file('file');

    // Get the number of existing files in the directory
    $existingFilesCount = count(Storage::files('dogadajislike'));

    // Increment the count to get the next number for the new file
    $nextFileNumber = $existingFilesCount + 1;

    // Create the filename
    $filename = 'slika' . $nextFileNumber . '.' . $file->getClientOriginalExtension();

    // Define the path where the file will be stored
    $path = 'dogadajislike';

    // Store the file
    $storagePath = Storage::putFileAs($path, $file, $filename);

    // Check if the file has been saved successfully
    if ($storagePath) {
        return ApiResponse::success(['message' => 'File uploaded successfully', 'path' => $storagePath]);
    } else {
        return ApiResponse::failed(['message' => 'Failed to upload the file..']);
    }
}



public function customUploadFileDokumenti(Request $request)
{
    $chunk = $request->file('file');
    if (!$chunk) {
        // Return an error response if the file chunk is not found
        return ApiResponse::failed(['message' => 'No file chunk received']);
    }

    $originalFilename = $request->input('filename');
    $chunkIndex = $request->input('chunkIndex');
    $totalChunks = $request->input('totalChunks');

    // Generate temporary path for storing chunks
    $tempDir = storage_path('app/temp');
    $tempPath = $tempDir . '/' . $originalFilename;


    // Store the current chunk
    file_put_contents($tempPath . '_' . $chunkIndex, file_get_contents($chunk->getPathname()));

    // Check if all chunks are uploaded
    if ($this->areAllChunksUploaded($tempPath, $totalChunks)) {
        // Extract the file extension from the original filename
        $extension = pathinfo($originalFilename, PATHINFO_EXTENSION);
        $finalFilename = $this->generateFinalFilename($extension);
        $finalPath = 'dokumenti-clanova/' . $finalFilename;

        // Reassemble the file from chunks
        $this->reassembleFile($tempPath, $finalPath, $totalChunks);

        // Clean up temporary chunks
        $this->cleanupChunks($tempPath, $totalChunks);

        // Get the full path of the uploaded file
        $fullPath = Storage::disk('public')->url($finalPath);

        return ApiResponse::success(['message' => 'File uploaded successfully', 'path' => $fullPath]);
    }

    return ApiResponse::success(['message' => 'Chunk ' . $chunkIndex . ' uploaded successfully']);
}

private function generateFinalFilename($extension)
{
    $existingFiles = Storage::files('dokumenti-clanova');
    $highestNumber = 0;

    foreach ($existingFiles as $file) {
        if (preg_match('/dokument(\d+)/', $file, $matches)) {
            $number = intval($matches[1]);
            if ($number > $highestNumber) {
                $highestNumber = $number;
            }
        }
    }

    // Use the original file extension
    return 'dokument' . ($highestNumber + 1) . '.' . $extension;
}

private function areAllChunksUploaded($tempPath, $totalChunks)
{
    for ($i = 0; $i < $totalChunks; $i++) {
        if (!file_exists($tempPath . '_' . $i)) {
            return false;
        }
    }
    return true;
}

private function reassembleFile($tempPath, $finalPath, $totalChunks)
{
    $finalFile = fopen(storage_path('app/public/') . $finalPath, 'wb');

    for ($i = 0; $i < $totalChunks; $i++) {
        fwrite($finalFile, file_get_contents($tempPath . '_' . $i));
    }

    fclose($finalFile);
}

private function cleanupChunks($tempPath, $totalChunks)
{
    for ($i = 0; $i < $totalChunks; $i++) {
        unlink($tempPath . '_' . $i);
    }
}


/*
public function customUploadFileDokumenti(Request $request)
{
    $file = $request->file('file');

    // Get the number of existing files in the directory
    $existingFilesCount = count(Storage::files('dokumenti-clanova'));

    // Increment the count to get the next number for the new file
    $nextFileNumber = $existingFilesCount + 1;

    // Create the filename
    $filename = 'dokument' . $nextFileNumber . '.' . $file->getClientOriginalExtension();

    // Define the path where the file will be stored
    $path = 'dokumenti-clanova';

    // Store the file
    $storagePath = Storage::putFileAs($path, $file, $filename);

    // Check if the file has been saved successfully
    if ($storagePath) {
        return ApiResponse::success(['message' => 'Uspješno spremljen dokument', 'path' => $storagePath]);
    } else {
        return ApiResponse::failed(['message' => 'Failed to upload the file.']);
    }
}
*/

public function customUploadFileDokumentiCtt(Request $request)
{
    $file = $request->file('file');

    // Get the number of existing files in the directory
    $existingFilesCount = count(Storage::files('ctt-dokumenti-clanova'));

    // Increment the count to get the next number for the new file
    $nextFileNumber = $existingFilesCount + 1;

    // Create the filename
    $filename = 'ctt_dokument_' . $nextFileNumber . '.' . $file->getClientOriginalExtension();

    // Define the path where the file will be stored
    $path = 'ctt-dokumenti-clanova';

    // Store the file
    $storagePath = Storage::putFileAs($path, $file, $filename);

    // Check if the file has been saved successfully
    if ($storagePath) {
        return ApiResponse::success(['message' => 'Uspješno spremljen dokument', 'path' => $storagePath]);
    } else {
        return ApiResponse::failed(['message' => 'Failed to upload the file....']);
    }
}





public function customUploadDokumentiUciliste(Request $request)
{

   
    $chunk = $request->file('file');
    if (!$chunk) {
        // Return an error response if the file chunk is not found
        return ApiResponse::failed(['message' => 'No file chunk received']);
    }

    $originalFilename = $request->input('filename');
    $chunkIndex = $request->input('chunkIndex');
    $totalChunks = $request->input('totalChunks');

    $idbroj = $request->input('idbroj');

    // Generate temporary path for storing chunks
    $tempDir = storage_path('app/temp');
    $tempPath = $tempDir . '/' . $originalFilename;


    // Store the current chunk
    file_put_contents($tempPath . '_' . $chunkIndex, file_get_contents($chunk->getPathname()));

    // Check if all chunks are uploaded
    if ($this->areAllChunksUploaded2($tempPath, $totalChunks)) {
        // Extract the file extension from the original filename
        $extension = pathinfo($originalFilename, PATHINFO_EXTENSION);
        $finalFilename2 = $this->generateFinalFilename2($extension, $idbroj);
        $finalPath = 'dokumenti-uciliste/' . $finalFilename2;

        // Reassemble the file from chunks
        $this->reassembleFile2($tempPath, $finalPath, $totalChunks);

        // Clean up temporary chunks
        $this->cleanupChunks2($tempPath, $totalChunks);

        // Get the full path of the uploaded file
        $fullPath = Storage::disk('public')->url($finalPath);

        return ApiResponse::success(['message' => 'File uploaded successfully', 'path' => $fullPath]);
    }

    return ApiResponse::success(['message' => 'Chunk ' . $chunkIndex . ' uploaded successfully']);
}

private function generateFinalFilename2($extension, $idbroj)
{
    $existingFiles = Storage::files('dokumenti-uciliste');
    $highestNumber = 0;
    $pattern = '/SUdokument_id' . preg_quote($idbroj, '/') . '_\d+\.' . preg_quote($extension, '/') . '$/';

    foreach ($existingFiles as $file) {
        // Check if file matches the specific pattern for the given id and extension
        if (preg_match($pattern, $file, $matches)) {
            // Extract the numeric part of the filename
            $fileNameParts = explode('_', basename($file));
            $numberPart = preg_replace('/\D/', '', end($fileNameParts));
            $number = intval($numberPart);

            if ($number > $highestNumber) {
                $highestNumber = $number;
            }
        }
    }

    // Increment the highest number found for files with the same idbroj and extension
    $finalNumber = $highestNumber + 1;

    // Return the new filename using the original file extension
    return "SUdokument_id{$idbroj}_{$finalNumber}.{$extension}";
}

private function areAllChunksUploaded2($tempPath, $totalChunks)
{
    for ($i = 0; $i < $totalChunks; $i++) {
        if (!file_exists($tempPath . '_' . $i)) {
            return false;
        }
    }
    return true;
}

private function reassembleFile2($tempPath, $finalPath, $totalChunks)
{
    $finalFile = fopen(storage_path('app/public/') . $finalPath, 'wb');

    for ($i = 0; $i < $totalChunks; $i++) {
        fwrite($finalFile, file_get_contents($tempPath . '_' . $i));
    }

    fclose($finalFile);
}

private function cleanupChunks2($tempPath, $totalChunks)
{
    for ($i = 0; $i < $totalChunks; $i++) {
        unlink($tempPath . '_' . $i);
    }
}



public function getPdfDokumenti($folderPath = 'dokumenti-web-hzuts')
{
    // Get the absolute path of the folder
    $folderPath = Storage::disk('public')->path($folderPath);

    // Get all files in the folder
    $files = File::files($folderPath);

    // Filter out non-PDF files
    $pdfFiles = array_filter($files, function ($file) {
        return File::extension($file) === 'pdf';
    });

    // Map the PDF files to an array with their names and URLs
    $pdfFileData = array_map(function ($file) use ($folderPath) {
        $filename = File::name($file);
        $relativePath = str_replace(public_path(), '', $file);
        $url = Storage::disk('public')->url($relativePath);
        $lastModified = Carbon::createFromTimestamp(File::lastModified($file));

        return [
            'name' => $filename,
            'url' => $url,
            'date' => $lastModified->toDateTimeString()
        ];
    }, $pdfFiles);

    return ApiResponse::success($pdfFileData);
}



}
