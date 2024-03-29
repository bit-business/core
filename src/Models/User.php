<?php

namespace NadzorServera\Skijasi\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable implements JWTSubject
{
    // use HasFactory;
    use HasApiTokens, HasFactory, Notifiable, LogsActivity;

    protected $table = null;

    /**
     * Constructor for setting the table name dynamically.
     */
    public function __construct(array $attributes = [])
    {
        $prefix = config('skijasi.database.prefix');
        $this->table = $prefix.'users';
        parent::__construct($attributes);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'username',
        'brojmobitela',
        'email',

        'datumrodjenja',
        'drzava',
        'grad',
        'postanskibroj',
        'adresa',
        'oib',
        'spol',

        'password',
        'avatar',
        'additional_info',
        'last_sent_token_at',

        'urlinstagram',
        'urlfacebook',

        'noviprofil',
        
        'new_avatar',
        'avatar_approved',
        'zahtjev_approved',

        'user_type',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_sent_token_at' => 'datetime',
    ];

    protected static $logAttributes = [
        'name',
        'email',
        'avatar',
        'additional_info',
    ];

    protected static $logFillable = [
        'name',
        'email',
        'avatar',
        'additional_info',
    ];

    protected static $logName = 'User';

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Zabilježen je sljedeci podatak {$eventName}";
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, config('skijasi.database.prefix').'user_roles');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->dontSubmitEmptyLogs();
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }



}
