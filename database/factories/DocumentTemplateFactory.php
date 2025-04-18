<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\DocumentTemplate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DocumentTemplate>
 */
class DocumentTemplateFactory extends Factory
{
    protected $model = DocumentTemplate::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fileExtension = 'docx';
        $fileName = $this->faker->word();

        return [
            'user_id' => User::factory(),
            'name' => $this->faker->words(3, true),
            'file_path' => 'files/'.$this->faker->unique()->sha1().'.'.$fileExtension,
            'disk' => 'local',
            'file_orig_name' => $fileName.'.'.$fileExtension,
            'file_ext' => $fileExtension,
            'file_size' => $this->faker->numberBetween(1000, 1000000), // от 1KB до 1MB
        ];
    }

    /**
     * @return $this
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->getAttribute('id'),
        ]);
    }

    /**
     * @return $this
     */
    public function withExtension(string $extension): static
    {
        return $this->state(function (array $attributes) use ($extension) {
            $fileName = pathinfo($attributes['file_orig_name'], PATHINFO_FILENAME);

            return [
                'file_orig_name' => $fileName.'.'.$extension,
                'file_path' => pathinfo($attributes['path'], PATHINFO_DIRNAME).'/'.$fileName.'.'.$extension,
                'file_ext' => $extension,
            ];
        });
    }
}
