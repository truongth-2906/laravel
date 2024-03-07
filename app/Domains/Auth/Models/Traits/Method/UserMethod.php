<?php

namespace App\Domains\Auth\Models\Traits\Method;

use App\Domains\Auth\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * Trait UserMethod.
 */
trait UserMethod
{
    /**
     * @return bool
     */
    public function isMasterAdmin(): bool
    {
        return $this->id === 1;
    }

    /**
     * @return mixed
     */
    public function isAdmin(): bool
    {
        return $this->type === self::TYPE_ADMIN;
    }

    /**
     * @return bool
     */
    public function isUser(): bool
    {
        return $this->isFreelancer() || $this->isEmployer();
    }

    /**
     * @return mixed
     */
    public function isFreelancer(): bool
    {
        return $this->type === self::TYPE_FREELANCER;
    }

    /**
     * @return mixed
     */
    public function isEmployer(): bool
    {
        return $this->type === self::TYPE_EMPLOYER;
    }

    /**
     * @return mixed
     */
    public function hasAllAccess(): bool
    {
        return $this->isAdmin() && $this->hasRole(config('base.access.role.admin'));
    }

    /**
     * @param $type
     * @return bool
     */
    public function isType($type): bool
    {
        return $this->type === $type;
    }

    /**
     * @return mixed
     */
    public function canChangeEmail(): bool
    {
        return config('base.access.user.change_email');
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active === User::IS_ACTIVE;
    }

    /**
     * @return bool
     */
    public function isDeclined(): bool
    {
        return $this->active === User::IS_DECLINED;
    }

    /**
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->active === User::IS_PENDING;
    }

    /**
     * @return bool
     */
    public function isVerified(): bool
    {
        return $this->email_verified_at !== null;
    }

    /**
     * @return bool
     */
    public function isSocial(): bool
    {
        return $this->provider && $this->provider_id;
    }

    /**
     * @return Collection
     */
    public function getPermissionDescriptions(): Collection
    {
        return $this->permissions->pluck('description');
    }

    /**
     * @param $size
     * @return string
     */
    public function getAvatar($size = null)
    {
        return Storage::disk('azure')->url('/public/users/' . $this->avatar);
    }

    /**
     * @return bool
     */
    public function isAvailable(): bool
    {
        return $this->available == User::AVAILABLE;
    }

    /**
     * @return bool
     */
    public function isOnline()
    {
        return $this->is_online ?? false;
    }

    /**
     * @return array|Application|Translator|string|null
     */
    public function checkIdentity()
    {
        if ($this->isDeclined()) {
            return __('Your application has been rejected, please click the button "VERIFY NOW" to verify again!');
        }

        if ($this->isPending() && !is_null($this->identity_passbase)) {
            return __('Your profile has been uploaded successfully, please wait!');
        }

        if ($this->isActive()) {
            return __('You have successfully verified your identity.');
        }

        return __('Please click the "VERIFY NOW" button to verify your identity!');
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getFieldAllowSort(string $key)
    {
        if (array_key_exists($key, $this::FIELDS_ALLOWED_SORT)) {
            return $this::FIELDS_ALLOWED_SORT[$key];
        }

        return false;
    }

    /**
     * @return bool|void
     */
    public function updateFreelancerScore()
    {
        if ($this->type == $this::TYPE_FREELANCER) {
            return $this->update([
                'score' => $this->countScoreFreelancer()
            ]);
        }
    }

    /**
     * @return int
     */
    protected function countScoreFreelancer(): int
    {
        $listColumn = $this::COLUMNS_PROCESS_FRE;
        $point = 0;

        foreach ($listColumn as $col) {
            if ($col == $this::CATEGORY && $this->categories()->count()) {
                $point = $point + $this::POINT_LADDER_FREELANCER[$this::CATEGORY];
                continue;
            }

            if ($col != $this::CATEGORY && $this->getAttribute($col) != null) {
                $point = $point + $this::POINT_LADDER_FREELANCER[$col];
            }
        }

        return $point;
    }
}
