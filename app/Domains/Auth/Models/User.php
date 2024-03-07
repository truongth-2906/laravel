<?php

namespace App\Domains\Auth\Models;

use App\Domains\Auth\Models\Traits\Attribute\UserAttribute;
use App\Domains\Auth\Models\Traits\Method\UserMethod;
use App\Domains\Auth\Models\Traits\Relationship\UserRelationship;
use App\Domains\Auth\Models\Traits\Scope\UserScope;
use App\Domains\Auth\Notifications\Frontend\ResetPasswordNotification;
use App\Domains\Auth\Notifications\Frontend\VerifyEmail;
use DarkGhostHunter\Laraguard\Contracts\TwoFactorAuthenticatable;
use DarkGhostHunter\Laraguard\TwoFactorAuthentication;
use Database\Factories\UserFactory;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Lab404\Impersonate\Models\Impersonate;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class User.
 */
class User extends Authenticatable implements TwoFactorAuthenticatable
{
    use HasApiTokens,
        HasFactory,
        HasRoles,
        Impersonate,
        MustVerifyEmailTrait,
        Notifiable,
        SoftDeletes,
        TwoFactorAuthentication,
        UserAttribute,
        UserMethod,
        UserRelationship,
        UserScope;

    public const TYPE_ADMIN = 'admin';
    public const TYPE_FREELANCER = 'freelancer';
    public const TYPE_EMPLOYER = 'employer';
    public const DEVICE_PC = '0';
    public const DEVICE_MOBILE = '1';
    public const AVAILABLE = '1';
    public const BIO_LENGTH = 1000;
    public const UN_ACTIVE = 0;
    public const TO_BE_LOGGED_OUT_DEFAULT = 0;
    public const IS_ACTIVE = 1;
    public const IS_DECLINED = 2;
    public const IS_PENDING = 0;
    public const CATEGORY = 'category';
    public const AVATAR = 'avatar';
    public const PORTFOLIO = 'portfolio';
    public const COLUMNS_PROCESS_FRE = [
        self::AVATAR,
        'active',
        self::CATEGORY,
        'country_id',
        'email_verified_at',
        'tag_line',
        'experience_id',
    ];
    public const POINT_LADDER_FREELANCER = [
        self::AVATAR => 10,
        'active' => 20,
        self::CATEGORY => 20,
        'country_id' => 10,
        'email_verified_at' => 20,
        'tag_line' => 10,
        'experience_id' => 10
    ];
    public const SCORE_MAX_FREELANCER = 100;
    public const COLUMNS_PROCESS_EMP = [
        'email',
        'firstname',
        'lastname',
        'bio',
        self::AVATAR,
        'phone_number',
        'sector_id',
        'country_id',
        'calling_code_id',
        'timezone_id',
        'company_id',
        self::PORTFOLIO
    ];
    public const FIELDS_ALLOWED_SORT = [
        'name' => 'name',
        'is_online' => 'is_online',
        'last_login_at' => 'last_login_at',
        'sector_name' => 'sector_name',
        'available' => 'available',
    ];
    public const HIDDEN = 'hidden';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'username',
        'name',
        'firstname',
        'lastname',
        'bio',
        'avatar',
        'hours',
        'rate_per_hours',
        'country_id',
        'timezone_id',
        'company_id',
        'sector_id',
        'email',
        'email_verified_at',
        'password',
        'password_changed_at',
        'active',
        'identity_passbase',
        'available',
        'timezone',
        'last_login_at',
        'last_login_ip',
        'to_be_logged_out',
        'provider',
        'provider_id',
        'experience_id',
        'escrow_email',
        'phone_number',
        'calling_code_id',
        'score',
        'tag_line',
        'is_hidden'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'last_login_at',
        'email_verified_at',
        'password_changed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'last_login_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'to_be_logged_out' => 'boolean',
        'is_online' => 'boolean',
        'is_hidden' => 'boolean'
    ];

    /**
     * @var array
     */
    protected $appends = [
        'logo',
    ];

    /**
     * @var string[]
     */
    protected $with = [
        'permissions',
        'roles',
    ];

    /**
     * Send the password reset notification.
     *
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Send the registration verification email.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmail);
    }

    /**
     * Return true or false if the user can impersonate an other user.
     *
     * @param void
     * @return bool
     */
    public function canImpersonate(): bool
    {
        return $this->can('admin.access.user.impersonate');
    }

    /**
     * Return true or false if the user can be impersonate.
     *
     * @param void
     * @return bool
     */
    public function canBeImpersonated(): bool
    {
        return !$this->isMasterAdmin();
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return UserFactory::new();
    }
}
