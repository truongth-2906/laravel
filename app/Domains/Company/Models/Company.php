<?php

namespace App\Domains\Company\Models;

use App\Domains\Company\Models\Traits\Attribute\CompanyAttribute;
use App\Domains\Company\Models\Traits\Method\CompanyMethod;
use Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Company.
 */
class Company extends Model
{
    use HasFactory,
        CompanyMethod,
        CompanyAttribute;

    /**
     * @var string
     */
    protected $table = 'companies';

    protected $fillable = [
        'name',
        'logo'
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'avatar'
    ];

    /**
     * @return CompanyFactory
     */
    protected static function newFactory()
    {
        return CompanyFactory::new();
    }
}
