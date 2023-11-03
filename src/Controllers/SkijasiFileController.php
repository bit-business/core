<?php

namespace NadzorServera\Skijasi\Controllers;

use Illuminate\Http\Request;
use NadzorServera\Skijasi\Helpers\ApiResponse;
use UniSharp\LaravelFilemanager\Controllers\DeleteController;
use UniSharp\LaravelFilemanager\Controllers\ItemsController;
use UniSharp\LaravelFilemanager\Controllers\UploadController;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;



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
        return ApiResponse::failed(['message' => 'Failed to upload the file']);
    }
}




}
