<?php

namespace NadzorServera\Skijasi\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use stdClass;
use Tymon\JWTAuth\Exceptions\JWTException;
use NadzorServera\Skijasi\Exceptions\SingleException;
use NadzorServera\Skijasi\Facades\Skijasi;
use NadzorServera\Skijasi\Helpers\ApiResponse;
use NadzorServera\Skijasi\Helpers\AuthenticatedUser;
use NadzorServera\Skijasi\Helpers\Config;
use NadzorServera\Skijasi\Mail\ForgotPassword;
use NadzorServera\Skijasi\Mail\SendUserVerification;
use NadzorServera\Skijasi\Models\Configuration;
use NadzorServera\Skijasi\Models\EmailReset;
use NadzorServera\Skijasi\Models\PasswordReset;
use NadzorServera\Skijasi\Models\Role;
use NadzorServera\Skijasi\Models\User;
use NadzorServera\Skijasi\Models\UserRole;
use NadzorServera\Skijasi\Models\UserVerification;
use NadzorServera\Skijasi\Traits\FileHandler;
use NadzorServera\Skijasi\Module\Commerce\Helper\UploadImage;
use Illuminate\Support\Facades\Log;

class SkijasiAuthController extends Controller
{
    use FileHandler;

    public function __construct()
    {
        $this->middleware(config('skijasi.middleware.authenticate'), ['except' => ['secretLogin', 'login', 'loginweb',  'register', 'forgetPassword', 'resetPassword', 'verify', 'reRequestVerification', 'validateTokenForgetPassword']]);
    }

    public function secretLogin(Request $request)
    {
        try {
            $remember = $request->get('remember', false);
            $credentials = [
                'email'    => $request->email,
                'password' => $request->password,
            ];
            $request->validate([
                'email' => [
                    'required',
                    function ($attribute, $value, $fail) use ($credentials) {
                        if (! $token = auth()->attempt($credentials)) {
                            $fail(__('skijasi::validation.auth.invalid_credentials'));
                        }
                    },
                ],
                'password' => ['required'],
            ]);

            $should_verify_email = Config::get('adminPanelVerifyEmail') == '1' ? true : false;
            if ($should_verify_email) {
                $user = auth()->user();
                if (is_null($user->email_verified_at)) {
                    $token = rand(111111, 999999);
                    $token_lifetime = env('VERIFICATION_TOKEN_LIFETIME', 5);
                    $expired_token = date('Y-m-d H:i:s', strtotime("+$token_lifetime minutes", strtotime(date('Y-m-d H:i:s'))));
                    $data = [
                        'user_id'            => $user->id,
                        'verification_token' => $token,
                        'expired_at'         => $expired_token,
                        'count_incorrect'    => 0,
                    ];

                    UserVerification::firstOrCreate($data);

                    $this->sendVerificationToken(['user' => $user, 'token' => $token]);

                    return ApiResponse::success();
                }
            }

            $ttl = $this->getTTL($remember);
            $token = auth()->setTTL($ttl)->attempt($credentials);

            activity('Authentication')
            ->causedBy(auth()->user() ?? null)
                ->withProperties(['attributes' => auth()->user()])
                ->log('Korisnik se prijavio');

            return $this->createNewToken($token, auth()->user(), $remember);
        } catch (JWTException $e) {
            return ApiResponse::failed($e);
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    public function login(Request $request)
    {
        try {
            $remember = $request->get('remember', false);
            $credentials = [
                'email'    => $request->email,
                'password' => $request->password,
            ];
            
            $request->validate([
                'email' => [
                    'required',
                    function ($attribute, $value, $fail) use ($credentials) {
                        if (! $token = auth()->attempt($credentials)) {
                            $fail(__('skijasi::validation.auth.invalid_credentials'));
                        }

                         // Get authenticated user
                $user = auth()->user();

                // Retrieve roles (assuming a roles() relationship exists)
                $roles = $user->roles()->pluck('name'); // Get names of the roles

                // Check if user has 'customer' or 'administrator' role
                if (!$roles->contains('customer') && !$roles->contains('administrator')) {
                    return $fail(__('Nemate korisnička prava za prijavu. Javite se Administratoru!'));
                }
            }],
                'password' => ['required'],
            ]);

            $should_verify_email = Config::get('adminPanelVerifyEmail') == '1' ? true : false;
            if ($should_verify_email) {
                $user = auth()->user();
                if (is_null($user->email_verified_at)) {
                    $token = rand(111111, 999999);
                    $token_lifetime = env('VERIFICATION_TOKEN_LIFETIME', 5);
                    $expired_token = date('Y-m-d H:i:s', strtotime("+$token_lifetime minutes", strtotime(date('Y-m-d H:i:s'))));
                    $data = [
                        'user_id'            => $user->id,
                        'verification_token' => $token,
                        'expired_at'         => $expired_token,
                        'count_incorrect'    => 0,
                    ];

                    UserVerification::firstOrCreate($data);

                    $this->sendVerificationToken(['user' => $user, 'token' => $token]);

                    return ApiResponse::success();
                }
            }

            $ttl = $this->getTTL($remember);
            $token = auth()->setTTL($ttl)->attempt($credentials);

            activity('Authentication')
                ->causedBy(auth()->user() ?? null)
                ->withProperties(['attributes' => auth()->user()])
                ->log('Korisnik se prijavio');

            return $this->createNewToken($token, auth()->user(), $remember);
        } catch (JWTException $e) {
            return ApiResponse::failed($e);
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }


    public function loginweb(Request $request)
    {
        try {
            $remember = $request->get('remember', false);
            $credentials = [
                'email'    => $request->email,
                'password' => $request->password,
            ];
            
            $request->validate([
                'email' => [
                    'required',
                    function ($attribute, $value, $fail) use ($credentials) {
                        if (! $token = auth()->attempt($credentials)) {
                            $fail(__('skijasi::validation.auth.invalid_credentials'));
                        }

                         // Get authenticated user
                $user = auth()->user();

                // Retrieve roles (assuming a roles() relationship exists)
                $roles = $user->roles()->pluck('name'); // Get names of the roles

                // Check if user has 'customer' or 'administrator' role
              //  if (!$roles->contains('customer') && !$roles->contains('administrator')) {
              //      return $fail(__('Nemate korisnička prava za prijavu. Javite se Administratoru!'));
              //  }
            }],
                'password' => ['required'],
            ]);

            $should_verify_email = Config::get('adminPanelVerifyEmail') == '1' ? true : false;
            if ($should_verify_email) {
                $user = auth()->user();
                if (is_null($user->email_verified_at)) {
                    $token = rand(111111, 999999);
                    $token_lifetime = env('VERIFICATION_TOKEN_LIFETIME', 5);
                    $expired_token = date('Y-m-d H:i:s', strtotime("+$token_lifetime minutes", strtotime(date('Y-m-d H:i:s'))));
                    $data = [
                        'user_id'            => $user->id,
                        'verification_token' => $token,
                        'expired_at'         => $expired_token,
                        'count_incorrect'    => 0,
                    ];

                    UserVerification::firstOrCreate($data);

                    $this->sendVerificationToken(['user' => $user, 'token' => $token]);

                    return ApiResponse::success();
                }
            }

            $ttl = $this->getTTL($remember);
            $token = auth()->setTTL($ttl)->attempt($credentials);

            activity('Authentication')
                ->causedBy(auth()->user() ?? null)
                ->withProperties(['attributes' => auth()->user()])
                ->log('Korisnik se prijavio');

            return $this->createNewToken($token, auth()->user(), $remember);
        } catch (JWTException $e) {
            return ApiResponse::failed($e);
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }


    public function logout(Request $request)
    {
        try {
            auth()->logout();
            // auth()->invalidate();
            activity('Authentication')
                ->causedBy(auth()->user() ?? null)
                ->withProperties(['attributes' => auth()->user()])
                ->log('Korisnik se odjavio');

            return ApiResponse::success();
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    public function register(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'name' => 'required|string|regex:/^[a-zA-ZčćžšđČĆŽŠĐàèéìíîòóùúÀÈÉÌÍÎÒÓÙÚäöüÄÖÜß]+$/u|max:55',
                'username' => 'required|string|regex:/^[a-zA-ZčćžšđČĆŽŠĐàèéìíîòóùúÀÈÉÌÍÎÒÓÙÚäöüÄÖÜß]+$/u|max:55',
                'brojmobitela' => 'required|regex:/^[\+]?[0-9\s\-]+$/|min:5',
                'email'    => 'required|string|email|max:55|unique:NadzorServera\Skijasi\Models\User',
                'password' => 'required|string|min:5|max:55|confirmed',
            ]);

            $existingUser = User::where('name', $request->get('name'))
            ->where('username', $request->get('username'))
            ->where('user_type', 'Hzuts član')
            ->whereNull('email_verified_at')
            ->first();

      

            if ($existingUser) {
                // Update existing user's information postojeci korisnik
                $existingUser->update([
                   // 'name'     => $request->get('name'),
                   // 'username' => $request->get('username'),
                    'brojmobitela' => $request->get('brojmobitela'),
                    'email'    => $request->get('email'),
                    'password' => Hash::make($request->get('password')),
                    'drzava'    => $request->get('drzava'),
                    'grad'    => $request->get('grad'),
                    'postanskibroj'    => $request->get('postanskibroj'),
                    'adresa'    => $request->get('adresa'),
                    'oib'    => $request->get('oib'),
                    'spol'    => $request->get('spol'),
                    'datumrodjenja'    => $request->get('datumrodjenja'),
                   // 'avatar'    => $request->get('avatar'),

                  //  'urlinstagram'    => $request->get('urlinstagram'),
                  //  'urlfacebook'    => $request->get('urlfacebook'),
                  //  'urltwitter'    => $request->get('urltwitter'),
                  //  'urllinkedin'    => $request->get('urllinkedin'),
            
                ]);

                if ($request->hasFile('avatar')) {
                    $avatarPath = UploadImage::createImageEdit($request->get('avatar'));
                    $existingUser->new_avatar = $avatarPath; 
                    $existingUser->avatar_approved = true; 
                } 
                
                
                
                $existingUser->save();

                $role = $this->getCustomerRole();

                $user_role = new UserRole();
                $user_role->user_id = $existingUser->id; // Use $existingUser->id instead of $user->id
                $user_role->role_id = $role->id;
                $user_role->save();
    
                $should_verify_email = Config::get('adminPanelVerifyEmail') == '1' ? true : false;
                if (! $should_verify_email) {
                    $ttl = $this->getTTL();
                    $token = auth()->setTTL($ttl)->login($existingUser);
    
                    DB::commit();
    
                    activity('Authentication')
                        ->causedBy(auth()->user() ?? null)
                        ->withProperties(['attributes' => [
                            'user' => $existingUser,
                            'role' => $user_role,
                        ]])
                        ->performedOn($existingUser)
                        ->event('created')
                        ->log('Stvorena je izmjena za postojećeg člana');
    
                    return $this->createNewToken($token, auth()->user());
                } else {
                    User::where('email', $request->get('email'))->update([
                        'last_sent_token_at' => date('Y-m-d H:i:s'),
                    ]);
                    $token = rand(111111, 999999);
                    $token_lifetime = env('VERIFICATION_TOKEN_LIFETIME', 5);
                    $expired_token = date('Y-m-d H:i:s', strtotime("+$token_lifetime minutes", strtotime(date('Y-m-d H:i:s'))));
                    $data = [
                        'user_id'            => $existingUser->id,
                        'verification_token' => $token,
                        'expired_at'         => $expired_token,
                        'count_incorrect'    => 0,
                    ];
    
                    UserVerification::firstOrCreate($data);
    
                    $this->sendVerificationToken(['user' => $existingUser, 'token' => $token]);
    
                    DB::commit();
    
                    return ApiResponse::success([
                        'message' => __('skijasi::validation.verification.email_sended'),
                    ]);


                } 
              } else {
                // Create a new user obican korisnik


             
                   
              
                

                $user = User::create([
                'name'     => $request->get('name'),
                'username' => $request->get('username'),
                'brojmobitela' => $request->get('brojmobitela'),
                'email'    => $request->get('email'),
                'password' => Hash::make($request->get('password')),

                'drzava'    => $request->get('drzava'),
                'grad'    => $request->get('grad'),
                'postanskibroj'    => $request->get('postanskibroj'),
                'adresa'    => $request->get('adresa'),
                'oib'    => $request->get('oib'),
                'spol'    => $request->get('spol'),
                'datumrodjenja'    => $request->get('datumrodjenja'),
               
               // 'avatar' =>  $request->get('avatar'), 
               // 'avatar' => $filename,

             //   'urlinstagram'    => $request->get('urlinstagram'),
             //   'urlfacebook'    => $request->get('urlfacebook'),
             //   'urltwitter'    => $request->get('urltwitter'),
             //   'urllinkedin'    => $request->get('urllinkedin'),   


                'user_type'    => 'Običan Korisnik',
              
                ]);


             
                    // Save the uploaded avatar to a storage directory
                    $filename = UploadImage::createImage($request->get('avatar'));
            
                    // Update the user's avatar field with the file path
                    $user->avatar = $filename;
                  
                


             
                $user->save();
                \Log::info("User Avatar: {$user->avatar}");




            $role = $this->getCustomerRole();

            $user_role = new UserRole();
            $user_role->user_id = $user->id;
            $user_role->role_id = $role->id;
            $user_role->save();

            $should_verify_email = Config::get('adminPanelVerifyEmail') == '1' ? true : false;
            if (! $should_verify_email) {
                $ttl = $this->getTTL();
                $token = auth()->setTTL($ttl)->login($user);

                DB::commit();

                activity('Authentication')
                    ->causedBy(auth()->user() ?? null)
                    ->withProperties(['attributes' => [
                        'user' => $user,
                        'role' => $user_role,
                    ]])
                    ->performedOn($user)
                    ->event('created')
                    ->log('Stvorena je izmjena za običnog korisnika');

                return $this->createNewToken($token, auth()->user());
              } else {
                User::where('email', $request->get('email'))->update([
                    'last_sent_token_at' => date('Y-m-d H:i:s'),
                ]);
                $token = rand(111111, 999999);
                $token_lifetime = env('VERIFICATION_TOKEN_LIFETIME', 5);
                $expired_token = date('Y-m-d H:i:s', strtotime("+$token_lifetime minutes", strtotime(date('Y-m-d H:i:s'))));
                $data = [
                    'user_id'            => $user->id,
                    'verification_token' => $token,
                    'expired_at'         => $expired_token,
                    'count_incorrect'    => 0,
                ];

                UserVerification::firstOrCreate($data);

                $this->sendVerificationToken(['user' => $user, 'token' => $token]);

                DB::commit();

                return ApiResponse::success([
                    'message' => __('skijasi::validation.verification.email_sended'),
                ]);
            }}
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failed($e);
        }
    }

    public function refreshToken(Request $request)
    {
        try {
            $ttl = $this->getTTL();
            $token = auth()->setTTL($ttl)->refresh();

            return $this->createNewToken($token, auth()->user());

            return ApiResponse::success();
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    public function getAuthenticatedUser()
    {
        try {
            if (! $user = AuthenticatedUser::getUser()) {
                throw new SingleException(__('skijasi::validation.auth.user_not_found'));
            }

            $data['user'] = json_decode(json_encode($user));

            return ApiResponse::success($data);
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    protected function createNewToken($token, $user, $remember = false)
    {
        $obj = new stdClass();
        $obj->access_token = $token;
        $obj->token_type = 'bearer';
        $obj->user = $user;
        $obj->expires_in = auth()->factory()->getTTL();

        return ApiResponse::success($obj);
    }

    public function sendVerificationToken($data)
    {
        return Mail::to($data['user']['email'])->queue(new SendUserVerification($data));
    }

    public function verify(Request $request)
    {
        try {
            $request->validate([
                'email' => ['required', 'exists:NadzorServera\Skijasi\Models\User'],
                'token' => ['required'],
            ]);

            $user = User::where('email', $request->email)->first();
            $user_verification = UserVerification::where('verification_token', $request->token)
                ->where('user_id', $user->id)
                ->first();

            if ($user_verification) {
                if (strtotime(date('Y-m-d H:i:s')) > strtotime($user_verification->expired_at)) {
                    // $user_verification->delete();
                    throw new SingleException('EXPIRED');
                }
                $user->email_verified_at = date('Y-m-d H:i:s');
                $user->save();

                $user_verification->delete();
            } else {
                throw new SingleException(__('skijasi::validation.verification.invalid_verification_token'));
            }

            $ttl = $this->getTTL();
            $token = auth()->setTTL($ttl)->login($user);

            return $this->createNewToken($token, auth()->user());
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    public function changePassword(Request $request)
    {
        try {
            if (! $user = auth()->user()) {
                throw new SingleException(__('skijasi::validation.auth.user_not_found'));
            }

            $request->validate([
                'old_password' => [
                    'required',
                    function ($attribute, $value, $fail) use ($user) {
                        if (! Hash::check($value, $user->password)) {
                            $fail(__('skijasi::validation.auth.wrong_old_password'));
                        }
                    },
                ],
                'new_password' => [
                    'required',
                    'confirmed',
                    'string',
                    'min:6',
                    function ($attribute, $value, $fail) use ($user) {
                        if (Hash::check($value, $user->password)) {
                            $fail(__('skijasi::validation.auth.password_not_changes'));
                        }
                    },
                ],
            ]);

            $user = User::find($user->id);
            $user->password = Hash::make($request->new_password);
            $user->save();

            activity('Authentication')
                ->causedBy(auth()->user() ?? null)
                ->withProperties(['attributes' => $request->all()])
                ->performedOn($user)
                ->event('updated')
                ->log('Promijenjena je šifra korisnika');

            return ApiResponse::success($user);
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    public function forgetPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => ['required', 'email', 'exists:NadzorServera\Skijasi\Models\User,email'],
            ]);

            $token = rand(111111, 999999);

            PasswordReset::insert([
                'email'      => $request->email,
                'token'      => $token,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $user = User::where('email', $request->email)->first();
            Mail::to($request->email)->send(new ForgotPassword($user, $token));

            return ApiResponse::success();
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }




    public function sendContactForm(Request $request)
    {
        try {
            $request->validate([
                'email'   => ['required', 'email'],
                'subject' => ['required', 'string', 'max:255'],
                'message' => ['required', 'string'],
            ]);
    
            $data = [
                'email'   => $request->email,
                'subject' => $request->subject,
                'contact_message' => $request->message,
            ];
            
    
            Mail::send('emails.contact', $data, function ($message) use ($data) {
                $message->to('info@hzuts.hr'); // promijenit prije live todo Set your email where you want to receive the contact form data
                $message->subject($data['subject']);
                $message->from($data['email']);
                $message->replyTo($data['email']);
            });
    
            return ApiResponse::success();
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }
    



    public function validateTokenForgetPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => ['required', 'email', 'exists:NadzorServera\Skijasi\Models\User,email'],
                'token' => [
                    'required',
                    'exists:NadzorServera\Skijasi\Models\PasswordReset,token',
                    function ($attribute, $value, $fail) use ($request) {
                        $password_resets = PasswordReset::where('token', $request->token)->where('email', $request->email)->get();
                        $password_reset = collect($password_resets)->first();
                        if (is_null($password_reset)) {
                            $fail('Token or Email invalid');
                        }
                    },
                ],
            ]);

            return ApiResponse::success();
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => ['required', 'email', 'exists:NadzorServera\Skijasi\Models\User,email'],
                'token' => [
                    'required',
                    'exists:NadzorServera\Skijasi\Models\PasswordReset,token',
                    function ($attribute, $value, $fail) use ($request) {
                        $password_resets = PasswordReset::where('token', $request->token)->where('email', $request->email)->get();
                        $password_reset = collect($password_resets)->first();
                        if (is_null($password_reset)) {
                            $fail('Token or Email invalid');
                        }
                    },
                ],
            ]);

            $password_resets = PasswordReset::where('token', $request->token)->where('email', $request->email)->get();

            $password_reset = collect($password_resets)->first();

            $request->validate([
                'token' => [
                    function ($attribute, $value, $fail) use ($password_reset) {
                        if (is_null($password_reset)) {
                            $fail('Token Invalid');
                        }
                    },
                ],
            ]);

            $user = User::where('email', $password_reset->email)->first();
            $user->password = Hash::make($request->password);
            $saved = $user->save();

            if ($saved) {
                PasswordReset::where('token', $request->token)->delete();
            }

            return ApiResponse::success();
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    public function reRequestVerification(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'email' => 'required|string|email|max:255',
            ]);

            $user = User::where('email', $request->email)->first();
            $time_wait_to_resend_token = Configuration::where('key', 'timeWaitResendToken')->first();
            $date_now = date('Y-m-d H:i:s');
            $time_out_token = date('Y-m-d H:i:s', strtotime($user->last_sent_token_at.' +  '.$time_wait_to_resend_token->value.' second'));
            $user_verification = UserVerification::where('user_id', $user->id)
                ->first();

            if ($date_now < $time_out_token) {
                throw new SingleException(__('skijasi::validation.verification.time_wait_loading'));
            }

            User::where('email', $request->get('email'))->update([
                'last_sent_token_at' => date('Y-m-d H:i:s'),
            ]);

            if (! $user_verification) {
                throw new SingleException(__('skijasi::validation.verification.verification_not_found'));
            }

            $token = rand(111111, 999999);
            $token_lifetime = env('VERIFICATION_TOKEN_LIFETIME', 5);
            $expired_token = date('Y-m-d H:i:s', strtotime("+$token_lifetime minutes", strtotime(date('Y-m-d H:i:s'))));

            $user_verification->verification_token = $token;
            $user_verification->expired_at = $expired_token;
            $user_verification->save();

            $this->sendVerificationToken(['user' => $user, 'token' => $token]);

            DB::commit();

            return ApiResponse::success([
                'message' => __('skijasi::validation.verification.email_sended'),
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failed($e);
        }
    }

    protected function getCustomerRole()
    {
        $name_role = Configuration::where('key', 'defaultRoleRegistration')->select('value')->first();
        $role = Role::where('name', $name_role->value)->first();

        if (is_null($role)) {
            $role = new Role();
            $role->name = 'customer';
            $role->display_name = 'Customer';
            $role->save();
        }

        return $role;
    }

    public function updateProfile(Request $request)
    {
        DB::beginTransaction();

        try {
            if (! $user = auth()->user()) {
                throw new SingleException(__('skijasi::validation.auth.user_not_found'));
            }

            $user_id = auth()->user()->id;

            $request->validate([
                'name'      => 'required|string|max:255',
                'username'  => "required|string|max:255|alpha_num|unique:NadzorServera\Skijasi\Models\User,username,{$user_id}",
                'avatar'    => 'nullable',
            ]);

            $user = User::find($user->id);
    
            $user->name = $request->name;
            $user->username = $request->username;
            $user->avatar = $request->avatar;
            $user->additional_info = $request->additional_info;
            $user->save();

            DB::commit();
            activity('Authentication')
                ->causedBy(auth()->user() ?? null)
                ->withProperties(['attributes' => [
                    'old' => auth()->user(),
                    'new' => $user,
                ]])
                ->performedOn($user)
                ->event('updated')
                ->log('Korisnička slika je promijenjena');

            return ApiResponse::success($user);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failed($e);
        }
    }

    public function updateEmail(Request $request)
    {
        DB::beginTransaction();

        try {
            if (! $user = auth()->user()) {
                throw new SingleException(__('skijasi::validation.auth.user_not_found'));
            }

            $request->validate([
                'email' => 'required|email|unique:NadzorServera\Skijasi\Models\User,email',
            ]);

            $user = User::find($user->id);

            $should_verify_email = Config::get('adminPanelVerifyEmail') == '1' ? true : false;
            if ($should_verify_email) {
                $token = rand(111111, 999999);
                $token_lifetime = env('VERIFICATION_TOKEN_LIFETIME', 5);
                $expired_token = date('Y-m-d H:i:s', strtotime("+$token_lifetime minutes", strtotime(date('Y-m-d H:i:s'))));
                $data = [
                    'user_id'            => $user->id,
                    'email'              => $request->email,
                    'verification_token' => $token,
                    'expired_at'         => $expired_token,
                    'count_incorrect'    => 0,
                ];

                EmailReset::firstOrCreate($data);

                $user->email = $request->email;

                $this->sendVerificationToken(['user' => $user, 'token' => $token]);

                DB::commit();

                return ApiResponse::success([
                    'should_verify_email' => true,
                    'message'             => __('skijasi::validation.verification.email_sended'),
                ]);
            } else {
                $user->email = $request->email;
                $user->save();
            }

            DB::commit();

            activity('Authentication')
                ->causedBy(auth()->user() ?? null)
                ->withProperties(['attributes' => [
                    'old' => auth()->user()->email,
                    'new' => $user->email,
                ]])
                ->performedOn($user)
                ->event('updated')
                ->log('Email je ažuriran');

            return ApiResponse::success([
                'should_verify_email' => false,
                'user'                => $user,
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failed($e);
        }
    }

    public function verifyEmail(Request $request)
    {
        try {
            if (! $user = auth()->user()) {
                throw new SingleException(__('skijasi::validation.auth.user_not_found'));
            }

            $request->validate([
                'email' => ['required', 'unique:NadzorServera\Skijasi\Models\User', 'email'],
                'token' => ['required'],
            ]);

            $emai_reset = EmailReset::where('verification_token', $request->token)
                ->where('user_id', $user->id)
                ->first();

            $user = User::find($user->id);

            if ($emai_reset) {
                if (strtotime(date('Y-m-d H:i:s')) > strtotime($emai_reset->expired_at)) {
                    // $user_verification->delete();
                    throw new SingleException('EXPIRED');
                }
                $user->email = $request->email;
                $user->email_verified_at = date('Y-m-d H:i:s');
                $user->save();

                $emai_reset->delete();
            } else {
                throw new SingleException(__('skijasi::validation.verification.invalid_verification_token'));
            }

            $ttl = $this->getTTL();
            $token = auth()->setTTL($ttl)->login($user);

            return $this->createNewToken($token, auth()->user());
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    private function getTTL($remember = false)
    {
        $remember_lifetime = 60 * 24 * 30; // a month
        $ttl = env('SKIJASI_AUTH_TOKEN_LIFETIME', Skijasi::getDefaultJwtTokenLifetime());
        if ($ttl != '') {
            $ttl = (int) $ttl;
        } else {
            $ttl = Skijasi::getDefaultJwtTokenLifetime();
        }
        if ($remember && $ttl < $remember_lifetime) {
            $ttl = $remember_lifetime;
        }

        return $ttl;
    }
}
