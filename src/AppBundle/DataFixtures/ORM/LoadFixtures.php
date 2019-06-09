<?php


namespace AppBundle\DataFixtures\ORM;


use AppBundle\Entity\Categories;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\Alice\Fixtures;

class LoadFixtures implements ORMFixtureInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function load(ObjectManager $manager)
    {
        Fixtures::load(
            __DIR__.'/fixtures.yml',
            $manager,
            [
                'providers' => [$this]
            ]
        );
    }

    public function department()//use this method in fixtures.yml by nelmio/alice
    {
        $department = [
            "Fruits",
            "Vegetables",
            "Furniture"
        ];

        $key = array_rand($department);

        return $department[$key];
    }
}