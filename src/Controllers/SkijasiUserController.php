<?php

namespace NadzorServera\Skijasi\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use NadzorServera\Skijasi\Helpers\ApiResponse;
use NadzorServera\Skijasi\Models\User;
use NadzorServera\Skijasi\Traits\FileHandler;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;


use NadzorServera\Skijasi\Helpers\GetData;

class SkijasiUserController extends Controller
{
    use FileHandler;

    public function browse(Request $request)
    {
        try {
            $users = User::all();

            $data['users'] = $users;

            return ApiResponse::success(collect($data)->toArray());
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    public function browsenasiclanovi(Request $request)
    {
            try {
                // Retrieve query parameters
                $search = $request->query('search');
                $sort = $request->query('sort', 'id'); // Default sorting field
                $order = $request->query('order', 'asc'); // Default sorting order
                $page = $request->query('page', 1); // Default page
                $perPage = $request->query('perPage', 32); // Default items per page
                
            

                // Build the query
                $query = User::query();

                $query->where('skijasi_users.user_type', 'Hzuts član');
                $query->whereNull('dateendmember');
    
                // Apply search if it's provided
                if (!empty($search)) {
                    $searchTerms = explode(' ', $search);
                    $query->where(function (Builder $q) use ($searchTerms) {
                        foreach ($searchTerms as $term) {
                            $q->where(function (Builder $q) use ($term) {
                                $q->where('name', 'like', "%{$term}%")
                                  ->orWhere('username', 'like', "%{$term}%");
                            });
                        }
                    });
                }
                
                $zborovi = $request->query('zborovi');
                $status = $request->query('status');
                $payments = $request->query('payments');
                $licence = $request->query('licence');

                if (!empty($zborovi)) {
                    $zboroviArray = explode(',', $zborovi);
                    $query->whereIn('department', $zboroviArray);
                }
 
              
             


                // Apply sorting
                $query->orderBy($sort, $order);


                $users = $query->orderBy($sort, $order)->paginate($perPage, ['*'], 'page', $page);



   // Filter out users with payments that are unpaid for 3 or more years
$filteredUsers = collect($users->items())->filter(function ($user) {
    $paymentData = GetData::fetchPaymentDataForMember($user->id);
    $statusPlacanja = GetData::calculatePaymentStatus($paymentData);

    // Log statusPlacanja for debugging
    Log::info("User ID: {$user->id}, Status Placanja: $statusPlacanja");

    // Check if the statusPlacanja is unpaid and if any unpaid payment is 3 or more years old
    $foundUserOver3Years = false;

    foreach ($paymentData as $payment) {
        if (!$payment->paidstatus && !$payment->partialpaid && !$payment->paymentdiscard && !$payment->paymentforgive) {
            $paymentDate = Carbon::parse($payment->opendate);
            $yearsDifference = $paymentDate->diffInYears(Carbon::now());
            Log::info("Payment Date: {$payment->opendate}, Years Difference: $yearsDifference");
            if ($yearsDifference >= 3) {
                $foundUserOver3Years = true;
                break; // Exit the loop if any unpaid payment is over 3 years old
            }
        }
    }

    if ($statusPlacanja === 'Nije plaćeno' && $foundUserOver3Years) {
        Log::info("User found with unpaid payment older than 3 years.");
        return false; // Exclude the user if any unpaid payment is over 3 years old
    }

    return true; // Include the user if all payments are paid or not over 3 years old
});

// Log the number of filtered users for debugging
Log::info("Filtered Users Count: " . $filteredUsers->count());

$users->setCollection($filteredUsers);


                if (!empty($licence)) {
                    $licenceArray = explode(',', $licence);
            
                    // Convert the paginated items to a collection and then filter
                    $filteredUsers = collect($users->items())->filter(function ($user) use ($licenceArray) {
                        $statusData = GetData::fetchStatusDataForMember($user->id);
                        $statusAktivanData = GetData::calculateStatusAktivan($statusData);
            
                        return in_array($statusAktivanData['status'], $licenceArray);
                    });
            
                    // Update the items in the paginator
                    $users->setCollection($filteredUsers);
                }




                foreach ($users->items() as $user) {
                    // Use methods from UserDataService for each user
                    $paymentData = GetData::fetchPaymentDataForMember($user->id);
                    $user->statusPlacanja = GetData::calculatePaymentStatus($paymentData);
                    
                    // ... other code ...

            // Fetch and calculate status data
            $statusData = GetData::fetchStatusDataForMember($user->id);
            $trainerStatusLabels = GetData::getTrainerStatusLabels();
            $user->statusString = GetData::calculateStatusString($statusData, $trainerStatusLabels);
            

            $licenceData = GetData::fetchLicenceDataForMember($user->id);
            $user->licenceData = $licenceData;


            $statusAktivanData = GetData::calculateStatusAktivan($statusData);
            $user->statusAktivan = $statusAktivanData['status'];
            $user->endstatusdate = $statusAktivanData['endstatusdate'];
            $user->idevent = $statusAktivanData['idevent'];

            $isiaData = GetData::fetchISIAbroj($user->id);
            $user->isiaBroj = $isiaData;
      
                }
        
   
                $payments = $request->query('payments');
                if (!empty($payments)) {
                    $paymentsArray = explode(',', $payments);
                    
                    // Apply the filter after fetching data
                    $filteredUsers = collect($users->items())->filter(function ($user) use ($paymentsArray) {
                        return in_array($user->statusPlacanja, $paymentsArray);
                    });
            
                    // Update the items in the paginator
                    $users->setCollection($filteredUsers);
                }

          // Filter users based on statusString if a filter is provided
   $status = $request->query('status');
   if (!empty($status)) {
       $statusArray = explode(',', $status);
       $filteredUsers = collect($users->items())->filter(function ($user) use ($statusArray) {
           return in_array($user->statusString, $statusArray);
       });

       // Update the items in the paginator
       $users->setCollection($filteredUsers);
   }


            
      
    
                // Properly format the response for the frontend
                return ApiResponse::success([
                    'users' => $users->items(),
                    'total' => $users->total(),
                    'currentPage' => $users->currentPage(),
                    'lastPage' => $users->lastPage(),
                    'perPage' => $users->perPage(),
                    'from' => $users->firstItem(),
                    'to' => $users->lastItem(),
                ]);
            } catch (Exception $e) {
                return ApiResponse::failed($e->getMessage());
            }
        }
    


        public function browseuserporuke(Request $request)
        {
            try {
                $users = User::all();
    
                $data['users'] = $users;
    
                return ApiResponse::success(collect($data)->toArray());
            } catch (Exception $e) {
                return ApiResponse::failed($e);
            }
            }
        
    
    
        public function read(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:NadzorServera\Skijasi\Models\User,id',
            ]);

            $user = User::find($request->id);

            $user->email_verified = ! is_null($user->email_verified_at);


      

 


            $data['user'] = $user;

            return ApiResponse::success($data);
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }





    

    public function readmojstatus(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:NadzorServera\Skijasi\Models\User,id',
            ]);

            $user = User::find($request->id);

            $user->email_verified = ! is_null($user->email_verified_at);


            $paymentData = GetData::fetchPaymentDataForMember($user->id);
            $user->statusPlacanja = GetData::calculatePaymentStatus($paymentData);
            
            // ... other code ...

    // Fetch and calculate status data
    $statusData = GetData::fetchStatusDataForMember($user->id);
    $trainerStatusLabels = GetData::getTrainerStatusLabels();
    $user->statusString = GetData::calculateStatusString($statusData, $trainerStatusLabels);
    

    $licenceData = GetData::fetchLicenceDataForMember($user->id);
    $user->licenceData = $licenceData;


    $statusAktivanData = GetData::calculateStatusAktivan($statusData);
    $user->statusAktivan = $statusAktivanData['status'];
    $user->endstatusdate = $statusAktivanData['endstatusdate'];
    $user->idevent = $statusAktivanData['idevent'];

    $isiaData = GetData::fetchISIAbroj($user->id);
    $user->isiaBroj = $isiaData;


            $data['user'] = $user;

            return ApiResponse::success($data);
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    public function edit(Request $request)
    {
        DB::beginTransaction();
     
        try {
            $request->validate([
                'id'        => 'required|exists:NadzorServera\Skijasi\Models\User,id',
                'email'     => 'required|email|unique:NadzorServera\Skijasi\Models\User',
               // 'username'  => "required|string|max:255|alpha_num|unique:NadzorServera\Skijasi\Models\User,username,{$request->id}",
               'name'      => 'required',
              

       
            ]);

            $user = User::find($request->id);
            $old_user = $user->toArray();

            $user->name = $request->name;
            $user->email = $request->email;


            if ($request->has('additional_info')) {
                $user->additional_info = $request->additional_info;
            }
    
            if ($request->has('datumrodjenja')) {
                $user->datumrodjenja = $request->datumrodjenja;
            }
            if ($request->has('brojmobitela')) {
                $user->brojmobitela = $request->brojmobitela;
            }
            if ($request->has('drzava')) {
                $user->drzava = $request->drzava;
            }
            if ($request->has('grad')) {
                $user->grad = $request->grad;
            }
            if ($request->has('postanskibroj')) {
                $user->postanskibroj = $request->postanskibroj;
            }
            if ($request->has('adresa')) {
                $user->adresa = $request->adresa;
            }
            if ($request->has('oib')) {
                $user->oib = $request->oib;
            }
         
            if ($request->has('spol')) {
                $user->spol = $request->spol;
            }
         
          
     
          //  $user->urlfacebook = $request->urlfacebook;
           // $user->urltwitter = $request->urltwitter;
           // $user->urlinstagram = $request->urlinstagram;
          //  $user->urllinkedin = $request->urllinkedin;

           // $user->prikazi_fb = $request->prikazi_fb;
           // $user->prikazi_ig = $request->prikazi_ig;
          //  $user->prikazi_tw = $request->prikazi_tw;
          //  $user->prikazi_lnk = $request->prikazi_lnk;

           
           // $user->avatar = $request->avatar;
           if ($request->has('avatar')) {
            $user->new_avatar = $request->avatar;
            $user->avatar_approved = true;
        }
        


            if ($request->password && $request->password != '') {
                $user->password = Hash::make($request->password);
            }
            if ($request->email_verified) {
                $user->email_verified_at = date('Y-m-d H:i:s');
            }

            $user->save();

            DB::commit();
            activity('User')
                ->causedBy(auth()->user() ?? null)
                ->withProperties(['attributes' => [
                    'old' => $old_user,
                    'new' => $user,
                ]])
                ->performedOn($user)
                ->event('updated')
                ->log('Korisniku '.$user->name.' je ažuriran profil');

            return ApiResponse::success($user);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failed($e);
        }
    }

    public function unapprovedAvatars()
{
    $users = User::where('avatar_approved', 1)->get();
    return response()->json($users);
}

public function novizahtjevclanstvo()
{
    $users = User::where('zahtjev_approved', 1)->get();
    return response()->json($users);
}
public function obrisizahtjev(Request $request)
{
    $user = User::find($request->id);
    DB::beginTransaction();

    try {
    $user->zahtjev_approved = false;
        
        $user->save();

        DB::commit();
        return ApiResponse::success('Zahtjev za članstvo je obrisan!');
    } catch (Exception $e) {
        DB::rollBack();

        return ApiResponse::failed($e);
    }
}



    public function approveAvatar(Request $request)
{
    $user = User::find($request->id);

    if (!$user) {
        return ApiResponse::failed('Korisnik nije nađen');
    }

    if (!$user->new_avatar) {
        return ApiResponse::failed('Nema novih slika za odobrenje');
    }

    DB::beginTransaction();

    try {
        // Assuming new_avatar holds the path of the avatar in storage
        $currentAvatarPath = $user->new_avatar;
        // Define the new path (excluding 'odobrenja/' from the path)
        $newAvatarPath = str_replace('odobrenja/', '', $currentAvatarPath);

        // Check if the file exists in the current location
        if (Storage::exists($currentAvatarPath)) {
            // Move the file to the new location
            Storage::move($currentAvatarPath, $newAvatarPath);
        } else {
            // Handle the case where the file doesn't exist
            DB::rollBack();
            return ApiResponse::failed('Slika ne postoji na destinaciji gdje bi trebala biti. Javite se administratoru!');
        }

        // Update the user's avatar path to the new location
        $user->avatar = $newAvatarPath;
    $user->new_avatar = null;
    $user->avatar_approved = false;
        
        $user->save();

        DB::commit();

        // Log the approval
        activity('User')
            ->causedBy(auth()->user() ?? null)
            ->performedOn($user)
            ->event('avatar_approved')
            ->log('Odobrena je nova profilna slika za: '.$user->name.'');

        return ApiResponse::success('Novi avatar je odobren!');
    } catch (Exception $e) {
        DB::rollBack();

        return ApiResponse::failed($e);
    }
}

public function declineAvatar(Request $request)
{
    $user = User::find($request->id);

    if (!$user) {
        return ApiResponse::failed('Korisnik nije nađen');
    }

    if (!$user->new_avatar) {
        return ApiResponse::failed('Nema novih slika za odobrenje');
    }

    DB::beginTransaction();

    try {
        // Remove new_avatar without replacing the main avatar
        Storage::delete($user->new_avatar); // Make sure to delete the unapproved image from storage
        $user->new_avatar = null;
        $user->avatar_approved = false;
        
        $user->save();

        DB::commit();

        // Log the decline
        activity('User')
            ->causedBy(auth()->user() ?? null)
            ->performedOn($user)
            ->event('avatar_declined')
            ->log('Odbijena je nova profilna slika '.$user->name.'');

        return ApiResponse::success('Slike je odbijena uspješno');
    } catch (Exception $e) {
        DB::rollBack();

        return ApiResponse::failed($e);
    }
}


public function zadnjiidmember() {
    // Retrieve the latest non-null id number from your database
 $lastRecord = DB::table('skijasi_users')
                 ->whereNotNull('idmember')
                 ->orderBy('idmember', 'desc')
                 ->first();


    $lastIdmember = $lastRecord ? $lastRecord->idmember : "";

    return response()->json(['idmember' => $lastIdmember]);
}






    public function add(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'email'     => 'required|email|unique:NadzorServera\Skijasi\Models\User',
                'name'      => 'required|string|max:255',
               // 'username'  => 'required|string|max:255|alpha_num|unique:NadzorServera\Skijasi\Models\User,username',
                'username'  => 'required|string|max:255|alpha_num',
                'avatar'    => 'nullable',
            ]);

            $user = new User();
            $user->name = $request->name;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->avatar = $request->avatar;
            $user->additional_info = $request->additional_info;
            $user->password = Hash::make($request->password);
            if ($request->email_verified) {
                $user->email_verified_at = date('Y-m-d H:i:s');
            }
            $user->save();

            DB::commit();
            activity('User')
                ->causedBy(auth()->user() ?? null)
                ->withProperties(['attributes' => $user])
                ->performedOn($user)
                ->event('created')
                ->log('Novi korisnik '.$user->name.' je dodan u bazu.');

            return ApiResponse::success($user);
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
                'id' => [
                    'required',
                    'exists:NadzorServera\Skijasi\Models\User',
                ],
            ]);

            $user = User::find($request->id);
            $this->handleDeleteFile($user->avatar);
            $user->delete();

            DB::commit();
            activity('User')
                ->causedBy(auth()->user() ?? null)
                ->performedOn($user)
                ->event('deleted')
                ->log('Korisnik '.$user->name.' je obrisan');

            return ApiResponse::success();
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
                'ids' => [
                    'required',
                ],
            ]);

            $id_list = explode(',', $request->ids);

            $user_name = [];
            foreach ($id_list as $key => $id) {
                $user = User::find($id);
                $this->handleDeleteFile($user->avatar);
                $user_name[] = $user->name;
                $user->delete();
            }
            $user_name = join(',', $user_name);
            DB::commit();
            activity('User')
                ->causedBy(auth()->user() ?? null)
                ->performedOn($user)
                ->event('deleted')
                ->log('Korisnik '.$user_name.' je obrisan');

            return ApiResponse::success();
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failed($e);
        }
    }
}
