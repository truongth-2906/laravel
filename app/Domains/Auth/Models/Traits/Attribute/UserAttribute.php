<?php

namespace App\Domains\Auth\Models\Traits\Attribute;

use Illuminate\Support\Facades\Hash;

/**
 * Trait UserAttribute.
 */
trait UserAttribute
{
    /**
     * @param $password
     */
    public function setPasswordAttribute($password): void
    {
        // If password was accidentally passed in already hashed, try not to double hash it
        // Note: Password Histories are logged from the \App\Domains\Auth\Observer\UserObserver class
        $this->attributes['password'] =
            (strlen($password) === 60 && preg_match('/^\$2y\$/', $password)) ||
            (strlen($password) === 95 && preg_match('/^\$argon2i\$/', $password)) ?
                $password :
                Hash::make($password);
    }

    /**
     * @return mixed
     */
    public function getLogoAttribute()
    {
        return $this->getAvatar();
    }

    /**
     * @return string
     */
    public function getPermissionsLabelAttribute()
    {
        if ($this->hasAllAccess()) {
            return 'All';
        }

        if (!$this->permissions->count()) {
            return 'None';
        }

        return collect($this->getPermissionDescriptions())
            ->implode('<br/>');
    }

    /**
     * @return string
     */
    public function getRolesLabelAttribute()
    {
        if ($this->hasAllAccess()) {
            return 'All';
        }

        if (!$this->roles->count()) {
            return 'None';
        }

        return collect($this->getRoleNames())
            ->each(function ($role) {
                return ucwords($role);
            })
            ->implode('<br/>');
    }

    /**
     * @return int
     */
    public function getFullProcess()
    {
        return count($this::COLUMNS_PROCESS_EMP);
    }

    /**
     * @return int
     */
    public function getProcess(): int
    {
        $listColumn = $this::COLUMNS_PROCESS_EMP;
        $numberActive = 0;

        foreach ($listColumn as $col) {
            if ($this->attributes['type'] === $this::TYPE_EMPLOYER && $col == $this::AVATAR) {
                if (optional($this->company)->logo) {
                    $numberActive++;
                }
                continue;
            }

            if ($col == $this::PORTFOLIO) {
                if ($this->portfolios()->count()) {
                    $numberActive++;
                }
                continue;
            }

            if ($this->attributes[$col] != null) {
                $numberActive++;
            }
        }

        return $numberActive;
    }
}
