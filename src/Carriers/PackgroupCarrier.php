<?php

namespace App\Carriers;

class PackgroupCarrier implements CarrierInterface
{
    const string NAME = 'PackGroup';

    const string CODE = 'packgroup';

    const string CURRENCY = 'EUR';

    /**
     * @param float $weightKg
     * @return array
     */
    public function calculate(float $weightKg): array
    {
        $price = $weightKg;
        return [
            "carrier"  => self::CODE,
            "weightKg" => $weightKg,
            "currency" => self::CURRENCY,
            "price"    => $price,
        ];
    }
}