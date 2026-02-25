<?php

namespace App\Carriers;

class TranscompanyCarrier implements CarrierInterface
{
    const NAME = 'TransCompany';

    const CODE = 'transcompany';

    const CURRENCY = 'EUR';

    /**
     * @param float $weightKg
     * @return array
     */
    public function calculate(float $weightKg): array
    {
        $price = ($weightKg > 10) ? 100 : 20;
        return [
            "carrier"  => self::CODE,
            "weightKg" => $weightKg,
            "currency" => self::CURRENCY,
            "price"    => $price,
        ];
    }
}