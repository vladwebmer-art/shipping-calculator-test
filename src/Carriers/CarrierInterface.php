<?php

namespace App\Carriers;

interface CarrierInterface
{
    /**
     * @param float $weightKg
     * @return array
     */
    public function calculate(float $weightKg): array;
}