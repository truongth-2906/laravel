<?php

use App\Domains\Auth\Models\User;
use App\Domains\Category\Models\Category;
use App\Domains\Company\Models\Company;
use App\Domains\Country\Models\Country;
use App\Domains\Experience\Models\Experience;
use App\Domains\Job\Models\Job;
use App\Domains\Sector\Models\Sector;
use App\Domains\Timezone\Models\Timezone;
use Carbon\Carbon;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Collection;

if (!function_exists('appName')) {
    /**
     * Helper to grab the application name.
     *
     * @return mixed
     */
    function appName()
    {
        return config('app.name', 'Raau01');
    }
}

if (!function_exists('carbon')) {
    /**
     * Create a new Carbon instance from a time.
     *
     * @param $time
     * @return Carbon
     *
     * @throws Exception
     */
    function carbon($time)
    {
        return new Carbon($time);
    }
}

if (!function_exists('homeRoute')) {
    /**
     * Return the route to the "home" page depending on authentication/authorization status.
     *
     * @return string
     */
    function homeRoute()
    {
        if (auth()->check()) {
            if (auth()->user()->isAdmin()) {
                return 'admin.freelancer.index';
            }

            if (auth()->user()->isEmployer()) {
                return 'frontend.employer.index';
            }

            if (auth()->user()->isFreelancer()) {
                return 'frontend.freelancer.index';
            }
        }

        return FRONTEND_LOGIN;
    }
}

if (!function_exists('checkRoute')) {
    /**
     * @return bool
     */
    define(
        "LIST_ROUTE_ADMIN",
        [
            'admin.freelancer.create',
            'admin.freelancer.edit',
            'admin.employer.create',
            'admin.employer.edit',
            'admin.job.create',
            'admin.job.edit',
            'admin.freelancer.setting'
        ]
    );
    define(
        "LIST_ROUTE_USER",
        [
            'frontend.freelancer.index',
            'frontend.employer.index',
            'frontend.employer.find-freelancer',
            'frontend.freelancer.job-detail',
        ]
    );

    function checkRoute($isAdmin = true)
    {
        if ($isAdmin) {
            return in_array(Route::currentRouteName(), LIST_ROUTE_ADMIN);
        }
        return in_array(Route::currentRouteName(), LIST_ROUTE_USER);
    }
}

/**
 * Return the keyword when after format
 *
 * @return string
 */
if (!function_exists('escapeLike')) {
    /**
     * @param $keyword
     * @return array|string|string[]
     */
    function escapeLike($keyword)
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $keyword);
    }
}

if (!function_exists('getListTimezone')) {
    /**
     * @return mixed
     */
    function getListTimezone()
    {
        return Timezone::orderBy('offset')->get();
    }
}

if (!function_exists('getListCountry')) {
    /**
     * @return Country[]|Collection
     */
    function getListCountry()
    {
        return Country::all();
    }
}

if (!function_exists('getListExperience')) {
    /**
     * @return Experience[]|Collection
     */
    function getListExperience()
    {
        return Experience::all();
    }
}

if (!function_exists('getListCategory')) {
    /**
     * @return Category[]|Collection
     */
    function getListCategory()
    {
        return Category::all();
    }
}

if (!function_exists('getListCompany')) {
    /**
     * @return Company[]|Collection
     */
    function getListCompany()
    {
        return Company::all();
    }
}

if (!function_exists('getListSector')) {
    /**
     * @return Sector[]|Collection
     */
    function getListSector()
    {
        return Sector::all();
    }
}

if (!function_exists('getLengthBio')) {
    /**
     * @param int $valueMax
     * @param int $lengthBio
     * @return int
     */
    function getLengthBio(int $valueMax, int $lengthBio): int
    {
        $remainingWord = $valueMax - $lengthBio;

        if ($remainingWord < 0) {
            return 0;
        } else {
            return $remainingWord;
        }
    }
}

if (!function_exists('randomNumber')) {
    /**
     * @param int $min
     * @param int $max
     * @return int
     * @throws Exception
     */
    function randomNumber(int $min, int $max): int
    {
        return random_int($min, $max);
    }
}

if (!function_exists('percentUser')) {
    /**
     * @param int $number
     * @param int $max
     * @return int
     */
    function percentUser(int $number, int $max): int
    {
        return ceil($number / $max * 100);
    }
}

if (!function_exists('renderStarReview')) {
    /**
     * @param $value
     * @return array
     */
    function renderStarReview($value)
    {
        $data = [];
        $whole = (int)$value;
        for ($i = 1; $i <= 5; $i++) {
            $data[$i] = $i <= $whole ? ACTIVE_STAR : INACTIVE_STAR;
        }

        if (is_float($value)) {
            $data[$whole + 1] = HALF_PART_STAR;
        }

        return $data;
    }
}

if (!function_exists('getPreviousRouteName')) {
    /**
     * get previous route name.
     *
     * @return string
     */
    function getPreviousRouteName()
    {
        return app('router')->getRoutes()->match(app('request')->create(url()->previous()))->getName();
    }
}

if (!function_exists('renderRatePointAvg')) {
    /**
     * @param $value
     * @return float|int|mixed
     */
    function renderRatePointAvg($value)
    {
        $point = 0;
        if (!is_float($value)) {
            return $value;
        }
        $whole = (int)$value;
        $decimal = $value - $whole;
        switch (true) {
            case $decimal < 0.25:
                $point = $whole + 0;
                break;
            case $decimal >= 0.25 && $decimal < 0.75:
                $point = $whole + 0.5;
                break;
            case $decimal >= 0.75:
                $point = $whole + 1;
                break;
            default:
        }

        return $point;
    }
}

if (!function_exists('formatDate')) {
    /**
     * @param $date
     * @return false|string
     */
    function formatDate($date)
    {
        if (!$date) {
            return false;
        }
        return date('d M Y', strtotime($date));
    }
}

if (!function_exists('notificationRoute')) {
    /**
     * @param string $suffix
     * @param mixed $parameter
     * @return false|string
     */
    function notificationRoute(string $suffix, $parameter = [], $type = null)
    {
        $type = is_null($type) ? auth()->user()->type : $type;
        return route('frontend.' . $type . '.notifications.' . $suffix, $parameter);
    }
}

if (!function_exists('convertFileSize')) {
    /**
     * @param $size
     * @return string
     */
    function convertFileSize($size)
    {
        if (!$size) {
            return 0;
        }
        $unit = 1024;
        if ($size < $unit * $unit) {
            $size = round($size / $unit, 1) . 'KB';
        } else {
            $size = round($size / $unit / $unit, 1) . 'MB';
        }
        return $size;
    }
}

if (!function_exists('formatDateChat')) {
    /**
     * @param $date
     * @return false|string
     */
    function formatDateChat($date)
    {
        if (date('Ymd', strtotime($date)) == date('Ymd')) {
            return 'Today';
        } else {
            if (date('Ymd', strtotime($date)) == date('Ymd') - 1) {
                return 'Yesterday';
            }
        }
        return date('d M Y', strtotime($date));
    }
}


if (!function_exists('formatHourMinute')) {
    /**
     * @param $date
     * @return false|string
     */
    function formatHourMinute($date)
    {
        return date('H:i', strtotime($date));
    }
}

if (!function_exists('echo_token')) {
    /**
     * Get the Laravel Echo token value.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    function echo_token()
    {
        $session = app('session');

        if (isset($session)) {
            return $session->get('echo_token', '');
        }

        throw new RuntimeException('Application session store not set.');
    }
}

if (!function_exists('symbolUnitMoney')) {
    /**
     * @param $unit
     * @return string
     */
    function symbolUnitMoney($unit)
    {
        switch ($unit) {
            case 'aud':
            case 'cad':
            case 'usd':
            case 'and':
                $symbol = '$';
                break;
            case 'euro':
                $symbol = '€';
                break;
            case 'gbp':
                $symbol = '£';
                break;
            default:
                $symbol = '$';
        }

        return $symbol;
    }
}

if (!function_exists('job')) {
    /**
     * @return Job|mixed
     * @throws BindingResolutionException
     */
    function job()
    {
        return app()->make(Job::class, func_get_args());
    }
}

if (!function_exists('user')) {
    /**
     * @return Job|mixed
     * @throws BindingResolutionException
     */
    function user()
    {
        return app()->make(User::class, func_get_args());
    }
}

