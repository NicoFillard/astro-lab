<?php

namespace App\DataFixtures;

use App\Entity\Astronaut;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AstronautFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $astronaut = new Astronaut();
            $astronaut->setName('Astronaut - ' . $i );
            $manager->persist($astronaut);
        }

        $manager->flush();
    }
}
