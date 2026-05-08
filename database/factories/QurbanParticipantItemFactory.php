<?php

namespace Database\Factories;

use App\Models\QurbanParticipant;
use App\Models\QurbanParticipantItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QurbanParticipantItem>
 */
class QurbanParticipantItemFactory extends Factory
{
    protected $model = QurbanParticipantItem::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = fake()->numberBetween(500_000, 15_000_000);
        $coupons = fake()->numberBetween(1, 5);

        return [
            'qurban_participant_id' => QurbanParticipant::factory(),
            'qurban_type' => fake()->randomElement(QurbanParticipantItem::QURBAN_TYPES),
            'price' => $price,
            'total_coupon' => $coupons,
            'total_price' => $price * $coupons,
        ];
    }

    public function type(string $type): static
    {
        return $this->state(function () use ($type) {
            $price = fake()->numberBetween(500_000, 15_000_000);
            $coupons = fake()->numberBetween(1, 5);

            return [
                'qurban_type' => $type,
                'price' => $price,
                'total_coupon' => $coupons,
                'total_price' => $price * $coupons,
            ];
        });
    }
}
