<?php

namespace App\DataFixtures;

use App\Entity\Champion;
use App\Entity\Spell;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Test champion
       $champion = new Champion();
       $champion->setName('Test Champ')
           ->setCustomId(rand(9999, 9999999))
           ->setAlias('testing champ')
           ->setImage('test-url.url')
       ;
       $manager->persist($champion);

       $manager->flush();

       // Test spell
        $spell = new Spell();
        $spell->setImage('test-image.url')
            ->setName('Test Spell')
            ->setCooldowns([1,2,3,4,5])
            ->setPatch('Patch no')
            ->setKeyboard('Q')
            ->addChampion($champion)
        ;

        $champion->addSpell($spell);

        $manager->persist($champion);
        $manager->persist($spell);

        $manager->flush();
    }
}
