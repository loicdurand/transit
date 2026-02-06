<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\TypeEnvoi;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $types_envoi = [
            [
                'libelle' => 'palette',
                'max' => null
            ],
            [
                'libelle' => 'colis',
                'max' => 5
            ]
        ];

        foreach ($types_envoi as $type) {
            $type_envoi = new TypeEnvoi();
            $type_envoi->setLibelle($type['libelle']);
            $type_envoi->setMaximum($type['max']);
            $manager->persist($type_envoi);
            $manager->flush();
        }

        $manager->flush();
    }
}
