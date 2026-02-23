<?php

namespace Database\Factories;

use App\Models\Assistant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssistantFactory extends Factory
{
    protected $model = Assistant::class;

    public function definition(): array
    {

        $tipos_documento = ['CC','CE','PAS'];

        // Crear usuario con rol de asistente
        $user = User::factory()->create([
            'role_id' => 3,
        ]);

        return [
            'user_id' => $user->id,
            'nombre' => $this->faker->firstName(),
            'apellido' => $this->faker->lastName(),
            'tipo_documento'   => $this->faker->randomElement($tipos_documento),
            'numero_documento' => $this->faker->unique()->numerify('##########'),
            'correo' => $this->faker->unique()->safeEmail(),
            'telefono'         => $this->faker->phoneNumber(),
        ];
    }
}
