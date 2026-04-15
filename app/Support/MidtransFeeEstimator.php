<?php

namespace App\Support;

class MidtransFeeEstimator
{
    public function estimate(?string $paymentType, int $grossAmount): ?array
    {
        if (blank($paymentType) || $grossAmount <= 0) {
            return null;
        }

        $paymentType = strtolower(trim($paymentType));
        $percentageRates = config("services.midtrans.fee_estimator.percentage_rates", []);
        $flatFees = config("services.midtrans.fee_estimator.flat_fees", []);

        $feeAmount = null;

        if (array_key_exists($paymentType, $percentageRates) && $percentageRates[$paymentType] !== null) {
            $feeAmount = (int) round($grossAmount * (float) $percentageRates[$paymentType]);
        } elseif (array_key_exists($paymentType, $flatFees) && $flatFees[$paymentType] !== null) {
            $feeAmount = (int) $flatFees[$paymentType];
        }

        if ($feeAmount === null) {
            return null;
        }

        return [
            "midtrans_fee_amount" => max(0, $feeAmount),
            "net_amount" => max(0, $grossAmount - $feeAmount),
            "net_amount_source" => "estimated_payment_rule",
        ];
    }
}
