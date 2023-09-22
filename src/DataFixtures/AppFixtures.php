<?php

namespace App\DataFixtures;

use App\Entity\Beer;
use App\Services\CallApiService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;


class AppFixtures extends Fixture
{
    private Generator $faker;
    private CallApiService $callApiService;

    public function __construct(CallApiService $callApiService)
    {
        $this->faker = Factory::create('fr_FR');
        $this->callApiService = $callApiService;
    }

    public function load(ObjectManager $manager): void
    {
        $beers = $this->getAllBeers();

        //dd($beers[]);

        foreach($beers as $apiBeer) {
            $beer = (new Beer())
                ->setName($apiBeer['name'])
                ->setUrl($apiBeer['image_url'])
                ->setPunkId($apiBeer['id']);
            $manager->persist($beer);
        }

        $manager->flush();
    }

    private function getPageBeer(int $pageNumber):array {
        return $this->callApiService->getApi('GET', "https://api.punkapi.com/v2/beers?page=". $pageNumber ."&per_page=80");
    }

    private function getAllBeers():array {
        $beers = [];
        $pages = 80;
        $pageNumber = 1;
        while ($pages === 80) {
            $newBeers = $this->getPageBeer($pageNumber);
            $pageNumber++;
            $pages = count($newBeers);
            foreach ($newBeers as $newBeer) {
                $beers[] = $newBeer;
            }
        }
        return $beers;
    }
}
