<?php

namespace Database\Factories;

use App\Domains\Job\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class JobFactory.
 */
class JobFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Job::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
        ];
    }

    /**
     * @return JobFactory
     */
    public function company($companies)
    {
        return $this->state(function (array $attributes) use ($companies) {
            return [
                'company_id' => $companies[array_rand($companies)],
            ];
        });
    }

    /**
     * @return JobFactory
     */
    public function country($countries)
    {
        return $this->state(function (array $attributes) use ($countries) {
            return [
                'country_id' => $countries[array_rand($countries)],
            ];
        });
    }

    /**
     * @return JobFactory
     */
    public function experience($experiences)
    {
        return $this->state(function (array $attributes) use ($experiences) {
            return [
                'experience_id' => $experiences[array_rand($experiences)],
            ];
        });
    }

    /**
     * @return JobFactory
     */
    public function user($users)
    {
        return $this->state(function (array $attributes) use ($users) {
            return [
                'user_id' => $users[array_rand($users)],
            ];
        });
    }

    /**
     * @return JobFactory
     */
    public function timezone($timezones)
    {
        return $this->state(function (array $attributes) use ($timezones) {
            return [
                'timezone_id' => $timezones[array_rand($timezones)],
            ];
        });
    }

    /**
     * @return JobFactory
     */
    public function status()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => $this->faker->randomElement([Job::STATUS_CLOSE, Job::STATUS_OPEN]),
            ];
        });
    }
}
