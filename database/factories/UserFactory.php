<?php

namespace Database\Factories;

use App\Domains\Auth\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Class UserFactory.
 */
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type' => $this->faker->randomElement([User::TYPE_ADMIN, User::TYPE_FREELANCER, User::TYPE_EMPLOYER]),
            'name' => $this->faker->name(),
            'firstname' => $this->faker->firstName(),
            'lastname' => $this->faker->lastName(),
            'username' => $this->faker->unique()->userName(),
            'tag_line' => $this->faker->text(50),
            'bio' => $this->faker->text(50),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'secret',
            'password_changed_at' => null,
            'remember_token' => Str::random(10),
            'timezone' => 'Asia/Tokyo',
            'active' => true,
            'available' => true,
        ];
    }

    /**
     * @return UserFactory
     */
    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => User::TYPE_ADMIN,
            ];
        });
    }

    /**
     * @return UserFactory
     */
    public function user()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => $this->faker->randomElement([User::TYPE_FREELANCER, User::TYPE_EMPLOYER]),
            ];
        });
    }

    /**
     * @return UserFactory
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'active' => true,
            ];
        });
    }

    /**
     * @return UserFactory
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'active' => false,
            ];
        });
    }

    /**
     * @return UserFactory
     */
    public function confirmed()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => now(),
            ];
        });
    }

    /**
     * @return UserFactory
     */
    public function unconfirmed()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    /**
     * @return UserFactory
     */
    public function passwordExpired()
    {
        return $this->state(function (array $attributes) {
            return [
                'password_changed_at' => now()->subYears(5),
            ];
        });
    }

    /**
     * @return UserFactory
     */
    public function deleted()
    {
        return $this->state(function (array $attributes) {
            return [
                'deleted_at' => now(),
            ];
        });
    }

    /**
     * @return UserFactory
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
     * @return UserFactory
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
     * @return UserFactory
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
     * @return UserFactory
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
     * @return UserFactory
     */
    public function sector($sectors)
    {
        return $this->state(function (array $attributes) use ($sectors) {
            return [
                'sector_id' => $sectors[array_rand($sectors)],
            ];
        });
    }

    /**
     * @return UserFactory
     */
    public function employer()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => User::TYPE_EMPLOYER,
            ];
        });
    }

    /**
     * @return UserFactory
     */
    public function freelancer()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => User::TYPE_FREELANCER,
            ];
        });
    }
}
