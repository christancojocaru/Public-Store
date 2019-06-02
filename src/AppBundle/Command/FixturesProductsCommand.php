<?php

namespace AppBundle\Command;

use AppBundle\Entity\Department;
use AppBundle\Entity\Product;
use AppBundle\Entity\User;
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

        foreach ($this->users() as $user) {
            $newUser = new User();
            $newUser->setEmail($user["email"]);
            $newUser->setPlainPassword($user["plainPassword"]);
            $newUser->setRoles($user["role"]);
            $this->entityManager->persist($newUser);
        }

        $this->entityManager->flush();
    }

    private function users()
    {
        return [
            "user1" => [
                "email" => "cristi@yahoo.com",
                "plainPassword" => "cristi",
                "role" => ["ROLE_ADMIN"]
            ],
            "user2" => [
                "email" => "mihai@gmail.com",
                "plainPassword" => "mihai",
                "role" => ["ROLE_USER"]
            ]
        ];
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