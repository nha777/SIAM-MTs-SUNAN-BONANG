<?php

namespace Database\Factories\Modules\Student\Models;

use App\Modules\Student\Models\Guardian;
use App\Modules\Student\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        $guardian = Guardian::query()->first() ?: Guardian::create([
            'guardian_name' => fake()->name(),
            'guardian_relation' => 'ayah',
            'phone_number' => fake()->unique()->numerify('08##########'),
            'address' => fake()->address(),
            'user_id' => null,
        ]);

        return [
            'guardian_id' => $guardian->id,
            'class_id' => null,
            'nisn' => (string) fake()->unique()->numerify('##########'),
            'name' => fake()->name(),
            'gender' => fake()->randomElement(['L', 'P']),
            'birth_place' => fake()->city(),
            'birth_date' => fake()->dateTimeBetween('-20 years', '-5 years')->format('Y-m-d'),
            'status' => 'aktif',
        ];
    }
}
