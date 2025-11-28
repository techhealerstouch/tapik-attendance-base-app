<?php



namespace App\Models;



use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Notifications\Notifiable;

use Illuminate\Support\Str;



class User extends Authenticatable implements MustVerifyEmail

{

    use HasFactory, Notifiable;



    /**

     * The attributes that are mass assignable.

     *

     * @var array

     */

    protected $fillable = [

        'name',

        'email',

        'image',

        'password',

        'provider',

        'provider_id',

        'email_verified_at',

        'littlelink_name',
        'littlelink_description',
        'mobile_number',
        'website',

        'block',

        'activate_code',

        'activate_status',

        'rfid_no',

        'qr_code_status'

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

    ];

    public function groups()
    {
        return $this->belongsToMany(Group::class)->using(GroupUser::class); // Specify the pivot model
    }

    public function visits()

    {

        return visits($this)->relation();
    }



    public function socialAccounts()

    {

        return $this->hasMany(SocialAccount::class);
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_user')
            ->withPivot('registered_at', 'status')
            ->withTimestamps();
    }

    public function foodServiceClaims()
    {
        return $this->hasMany(FoodServiceClaim::class);
    }

    public function getClaimsForEvent($eventId)
    {
        return $this->foodServiceClaims()
            ->where('event_id', $eventId)
            ->with('foodService')
            ->get();
    }

    public static function findByIdentifier($identifier)
    {
        return static::where('rfid_no', $identifier)
            ->orWhere('activate_code', $identifier)
            ->first();
    }

    public function hasClaimedService($eventId, $foodServiceId)
    {
        return $this->foodServiceClaims()
            ->where('event_id', $eventId)
            ->where('food_service_id', $foodServiceId)
            ->exists();
    }

    protected static function boot()

    {

        parent::boot();



        static::creating(function ($user) {

            if (config('linkstack.disable_random_user_ids') != 'true') {

                if (is_null(User::first())) {

                    $user->id = 1;
                } else {

                    $numberOfDigits = config('linkstack.user_id_length') ?? 6;



                    $minIdValue = 10 ** ($numberOfDigits - 1);

                    $maxIdValue = 10 ** $numberOfDigits - 1;



                    do {

                        $randomId = rand($minIdValue, $maxIdValue);
                    } while (User::find($randomId));



                    $user->id = $randomId;
                }
            }
        });
    }
}
