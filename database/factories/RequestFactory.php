<?php

namespace Database\Factories;

use App\Enums\Department;
use App\Enums\RequestStatus;
use App\Enums\RequestType;
use App\Enums\SenderType;
use App\Enums\UrgencyLevel;
use App\Models\Request;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Request>
 */
class RequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isAnonymous = $this->faker->boolean(40);
        $hasEvidence = $this->faker->boolean(35);
        $wantsFollowUp = ! $isAnonymous && $this->faker->boolean(60);
        $hasIncidentDate = $this->faker->boolean(70);

        return [
            'request_type' => $this->faker->randomElement(RequestType::cases())->value,
            'sender_type' => $this->faker->randomElement(SenderType::cases())->value,
            'is_anonymous' => $isAnonymous,
            'full_name' => $isAnonymous ? null : $this->faker->name(),
            'department' => $this->faker->randomElement(Department::cases())->value,
            'location' => $this->faker->randomElement([
                'Planta principal', 'Oficinas corporativas', 'Almacén general', 'Sucursal norte', 'Sucursal sur',
            ]),
            'incident_date' => $hasIncidentDate ? $this->faker->dateTimeBetween('-2 months', 'now')->format('Y-m-d') : null,
            'description' => $this->faker->paragraphs(3, true),
            'involved_people' => $this->faker->optional(0.4)->name(),
            'urgency_level' => $this->faker->randomElement(UrgencyLevel::cases())->value,
            'has_evidence' => $hasEvidence,
            'wants_follow_up' => $wantsFollowUp,
            'contact_info' => $wantsFollowUp ? $this->faker->safeEmail() : null,
            'accepted_terms' => true,
            'status' => $this->faker->randomElement(RequestStatus::cases())->value,
            'ip_address' => $this->faker->ipv4(),
        ];
    }
}
