<?php

namespace App\Carriers;

use Symfony\Component\Finder\Finder;

class CarrierProvider
{
    public const PREFIX = "App\\Carriers\\";

    public const POSTFIX = 'Carrier';

    /**
     * @param string $name
     * @return CarrierInterface|bool
     */
    public function getByName(string $name): CarrierInterface|bool
    {
        $name =  self::PREFIX . ucfirst($name) . self::POSTFIX;

        return (class_exists($name)) ? new $name() : false;
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        $finder = new Finder();
        $finder->files()->in(__DIR__)->name("*" . self::POSTFIX . ".php");

        $carriers = [];

        if ($finder->hasResults()) {
            foreach ($finder as $file) {
                $className = self::PREFIX . basename($file->getRelativePathname(), '.php');
                $carriers[] = new $className;
            }
        }

        return $carriers;
    }
}