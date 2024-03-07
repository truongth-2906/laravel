<?php

namespace App\Domains\Country\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Country
 * @package App\Domains\Country\Models
 */
class Country extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'countries';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'code',
        'calling_code'
    ];
}
