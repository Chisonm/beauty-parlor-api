<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @OA\Schema(
 * required={"password"},
 * @OA\Xml(name="data"),
 * @OA\Xml(name="data"),
 * @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 * @OA\Property(property="role", type="string", readOnly="true", description="User role" , example="vendor"),
 * @OA\Property(property="email", type="string", readOnly="true", format="email", description="User unique email address", example="daniel32@gmail.com"),
 * @OA\Property(property="email_verified_at", type="date", readOnly="true", format="date-time", description="Datetime marker of verification status", example="2019-02-25 12:59:20"),
 * @OA\Property(property="first_name", type="string", maxLength=32, example="daniel"),
 * @OA\Property(property="last_name", type="string", maxLength=32, example="chisom"),
 * @OA\Property(property="username", type="string", maxLength=32, example="chisom"),
 * @OA\Property(property="status", type="string", maxLength=32, example="Active"),
 * @OA\Property(property="phone_number", type="string", maxLength=32, example="08012345678"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Initial creation timestamp", readOnly="true"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp", readOnly="true"),
 * )
 *
 * Class User
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    const STATUS_ACTIVE = 'active';

    const STATUS_INACTIVE = 'inactive';

    const ROLE_VENDOR = 'vendor';

    const ROLE_ADMIN = 'admin';

    const ROLE_CLIENT = 'client';

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function shops()
    {
        return $this->hasMany(Shop::class);
    }
}
