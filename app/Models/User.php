<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Casts\ThaiDateTimeFormat;
use App\Enums\RegistrationType;
use App\Enums\UserType;
use App\Filters\UserFilter;
use App\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    public const IMAGE_FOLDER = 'users';

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'prefix_id',
        'firstname',
        'lastname',
        'phone',
        'email',
        'username',
        'password',
        'type',
        'district_id',
        'department_id',
        'position_id',
        'registration_type'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'prefix_id' => 'integer',
            'department_id' => 'integer',
            'position_id' => 'integer',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'type' => UserType::class,
            'registration_type' => RegistrationType::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'parentable');
    }

    public function approval(): HasOne
    {
        return $this->hasOne(UserApproval::class);
    }

    public function prefix(): HasOne
    {
        return $this->hasOne(Prefix::class, 'id', 'prefix_id');
    }

    public function department(): HasOne
    {
        return $this->hasOne(Department::class, 'id', 'department_id');
    }

    public function position(): HasOne
    {
        return $this->hasOne(Position::class, 'id', 'position_id');
    }

    public function area(): HasOne
    {
        return $this->hasOne(Geography::class, 'district_code', 'district_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(TeamMember::class, 'id', 'user_id');
    }

    public function shelterstaff(): BelongsTo
    {
        return $this->belongsTo(ShelterStaff::class, 'id', 'user_id');
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return (new UserFilter($filters))->apply($query);
    }

    // send forgot password
    // public function sendPasswordResetNotification($token)
    // {
    //     $this->notify(new ResetPassword($token, $this->email));
    // }
}
