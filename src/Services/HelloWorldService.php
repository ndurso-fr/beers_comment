<?php

namespace App\Services;

use App\Entity\Beer;

class HelloWorldService
{
    /**
     * @param Beer[] $beers
     * @return ?Beer
     */
    public function searchBeerInfosByName(array $beers, string $name):Beer|null
    {
        foreach ($beers as $beer) {
            if ($beer->getName() === $name) {
                return $beer;
            }
        }
        return null;
    }
}