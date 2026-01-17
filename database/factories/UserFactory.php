<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'username' => $this->faker->unique()->userName(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => $this->faker->optional()->dateTimeThisYear(),
            'password' => Hash::make('password'), // Mật khẩu mặc định
            'provider' => $this->faker->optional(0.3)->randomElement(['google', 'facebook', 'github']),
            'provider_id' => $this->faker->optional(0.3)->uuid(),
            'managed_by' => $this->faker->optional(0.1)->numberBetween(1, 10), // 10% có managed_by
            'is_active' => $this->faker->boolean(90), // 90% là active
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => Str::random(10),
            'created_at' => $this->faker->dateTimeThisYear(),
            'updated_at' => $this->faker->dateTimeThisYear(),
            'deleted_at' => $this->faker->optional(0.05)->dateTimeThisYear(), // 5% có soft delete
        ];
    }
}