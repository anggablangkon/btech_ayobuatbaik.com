<?php

namespace Database\Factories;

use App\Models\QurbanParticipant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QurbanParticipant>
 */
class QurbanParticipantFactory extends Factory
{
    protected $model = QurbanParticipant::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $contact = fake()->numerify("08##########");

        return [
            "full_name" => fake()->name(),
            "nik" => fake()->optional(0.85)->numerify("################"),
            "contact_number" => $contact,
            "email" => fake()->optional(0.5)->safeEmail(),
            "address" => fake()->streetAddress(),
            "city" => fake()->city(),
            "province" => fake()->state(),
            "postal_code" => fake()->numerify("#####"),
            "country" => "Indonesia",
            "coupon_code" => QurbanParticipant::generateCouponCode(),
            "pickup_date" => fake()->dateTimeBetween("-1 month", "+1 month"),
            "pickup_time" => fake()->time("H:i"),
            "status" => "pending",
            "note" => fake()->optional(0.35)->sentence(),
            "total_coupon" => (string) fake()->numberBetween(1, 12),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn() => ["status" => "pending"]);
    }

    public function taken(): static
    {
        return $this->state(fn() => ["status" => "taken"]);
    }

    public function rejected(): static
    {
        return $this->state(fn() => ["status" => "rejected"]);
    }
}
