<?php

namespace Database\Factories;

use App\Domains\Company\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class CompanyFactory.
 */
class CompanyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Company::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
        ];
    }
}
