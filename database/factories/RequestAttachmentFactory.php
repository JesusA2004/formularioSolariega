<?php

namespace Database\Factories;

use App\Models\Request;
use App\Models\RequestAttachment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RequestAttachment>
 */
class RequestAttachmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->word().'.pdf';

        return [
            'request_id' => Request::factory(),
            'original_name' => $name,
            'file_name' => $this->faker->uuid().'.pdf',
            'file_path' => 'buzon/sample/'.$this->faker->uuid().'.pdf',
            'mime_type' => 'application/pdf',
            'size' => $this->faker->numberBetween(10_000, 2_000_000),
        ];
    }
}
