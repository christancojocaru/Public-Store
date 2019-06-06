<?php

namespace AppBundle\Command;

use AppBundle\Entity\Department;
use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use AppBundle\Entity\UserProfile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FixturesProductsCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:fixture.products';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;


    protected function configure()
    {
        $this->setDescription('Fixture for products');
    }

    protected function execute(InputInterface $input,OutputInterface  $output)
    {
        foreach ($this->departments() as $department => $products) {
            $newDepartment = new Department();
            $newDepartment->setName($department);
            $this->entityManager->persist($newDepartment);

            foreach ($products as $product) {
                $newProduct = new Product();
                $newProduct->setName($product);
                $newProduct->setStock(rand(1, 20));
                $newProduct->setPrice(rand(10, 100) / 10);
                $newProduct->setDepartment($newDepartment);
                $this->entityManager->persist($newProduct);
            }
        }

        $newUserProfile = new UserProfile();
        $newUserProfile->setFirstName("cristi");
        $newUserProfile->setLastName("marian");
        $newUserProfile->setEmail("mihai@yahoo.com");
        $newUserProfile->setMobileNumber('0731016859');
        $newUserProfile->setAddress("Cal. Targovistei");
        $newUserProfile->setCity("Gura Ocnitei");
        $newUserProfile->setCountry("RO");
        $this->entityManager->persist($newUserProfile);

        $newUser = new User();
        $newUser->setUsername("mihai");
        $newUser->setUserProfile($newUserProfile);
        $newUser->setPlainPassword("mihai");
        $newUser->setRoles(["ROLE_USER"]);
        $this->entityManager->persist($newUser);

        $this->entityManager->flush();
    }

    private function departments()
    {
        return [
            "Fruits" => [
                "Apple",
                "Mandarin",
                "Orange",
                "Grapefruit",
                "Lime",
                "Pomelo"
            ],
            "Furniture" => [
                "Chair",
                "Stool",
                "Table",
                "Sofa",
                "Armchair",
                "Bed"
            ],
            "Vegetables" => [
                "Carrot",
                "Potato"
            ]
        ];
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @required
     */
    public function setEntityManager(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

}