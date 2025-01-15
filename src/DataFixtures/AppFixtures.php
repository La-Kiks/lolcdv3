<?php

namespace App\DataFixtures;

use App\Entity\Champion;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
       $champion = new Champion();
       $champion->setName('Test')
           ->setCustomId(9292)
           ->setAlias('testing')
           ->setImage('testurl.url')
       ;
       $manager->persist($champion);

        $manager->flush();
    }
}
