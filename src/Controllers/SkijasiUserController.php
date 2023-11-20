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
                if (!empty($zborovi)) {
                    $zboroviArray = explode(',', $zborovi);
                    $query->whereIn('department', $zboroviArray);
                }
        


                // Apply sorting
                $query->orderBy($sort, $order);
    
                // Paginate results
                $users = $query->paginate($perPage, ['*'], 'page', $page);


                
    
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

    public function edit(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'id'        => 'required|exists:NadzorServera\Skijasi\Models\User,id',
                'email'     => "required|email|unique:NadzorServera\Skijasi\Models\User,email,{$request->id}",
               // 'username'  => "required|string|max:255|alpha_num|unique:NadzorServera\Skijasi\Models\User,username,{$request->id}",
               'username'  => "required|string|max:255|alpha_num",
               'name'      => 'required',
                'avatar'    => 'nullable',

       
            ]);

            $user = User::find($request->id);
            $old_user = $user->toArray();

            $user->name = $request->name;
            $user->email = $request->email;

            $user->additional_info = $request->additional_info;

            $user->datumrodjenja = $request->datumrodjenja;
            $user->brojmobitela = $request->brojmobitela;
            $user->drzava = $request->drzava;
            $user->grad = $request->grad;
            $user->postanskibroj = $request->postanskibroj;
            $user->adresa = $request->adresa;
            $user->oib = $request->oib;
            $user->spol = $request->spol;
            $user->urlfacebook = $request->urlfacebook;
            $user->urltwitter = $request->urltwitter;
            $user->urlinstagram = $request->urlinstagram;
            $user->urllinkedin = $request->urllinkedin;

            $user->prikazi_fb = $request->prikazi_fb;
            $user->prikazi_ig = $request->prikazi_ig;
            $user->prikazi_tw = $request->prikazi_tw;
            $user->prikazi_lnk = $request->prikazi_lnk;

           
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


    public function approveAvatar(Request $request)
{
    $user = User::find($request->id);

    if (!$user) {
        return ApiResponse::failed('User not found');
    }

    if (!$user->new_avatar) {
        return ApiResponse::failed('No new avatar to approve');
    }

    DB::beginTransaction();

    try {
        // Replace old avatar with new_avatar
        $user->avatar = $user->new_avatar;
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

        return ApiResponse::success('Avatar approved successfully');
    } catch (Exception $e) {
        DB::rollBack();

        return ApiResponse::failed($e);
    }
}

public function declineAvatar(Request $request)
{
    $user = User::find($request->id);

    if (!$user) {
        return ApiResponse::failed('User not found');
    }

    if (!$user->new_avatar) {
        return ApiResponse::failed('No new avatar to decline');
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

        return ApiResponse::success('Avatar declined successfully');
    } catch (Exception $e) {
        DB::rollBack();

        return ApiResponse::failed($e);
    }
}


public function zadnjiidmember() {
    // Retrieve the latest non-null maticni number from your database
    $lastRecord = DB::table('skijasi_users')
                     ->whereNotNull('idmember')
                     ->orderBy('id', 'desc')
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
