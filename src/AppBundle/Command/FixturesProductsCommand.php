<?php

namespace AppBundle\Command;

use AppBundle\Entity\Categories;
use AppBundle\Entity\Departments;
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
    protected static $defaultName = 'app:fixture.load';

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
        foreach ($this->departments() as $department => $categories) {
            $newDepartment = new Departments();
            $newDepartment->setName($department);
            $this->entityManager->persist($newDepartment);

            foreach ($categories as $category => $products) {
                $newCategory = new Categories();
                $newCategory->setName($category);
                $newCategory->setDepartment($newDepartment);
                $this->entityManager->persist($newCategory);

                foreach ($products as $product) {
                    $newProduct = new Product();
                    $newProduct->setName($product);
                    $newProduct->setStock(rand(1, 30));
                    $newProduct->setPrice(rand(100, 100000) / 100);
                    $newProduct->setCategory($newCategory);
                    $this->entityManager->persist($newProduct);
                }
            }
        }

        $newUserProfile = new UserProfile();
        $newUserProfile->setFirstName("cristi");
        $newUserProfile->setLastName("marian");
        $newUserProfile->setEmail("cristi@yahoo.com");
        $newUserProfile->setMobileNumber('0731016859');
        $newUserProfile->setAddress("Cal. Targovistei");
        $newUserProfile->setCity("Gura Ocnitei");
        $newUserProfile->setCountry("RO");
        $this->entityManager->persist($newUserProfile);

        $newUser = new User();
        $newUser->setUsername("cristi");
        $newUser->setUserProfile($newUserProfile);
        $newUser->setPlainPassword("cristi");
        $newUser->setRoles(["ROLE_ADMIN"]);
        $this->entityManager->persist($newUser);

        $this->entityManager->flush();
    }

    private function departments()
    {
        return [
            "Electronics" => [
                "SmartPhones" => [
                    "Huawei P20 Lite",
                    "Samsung Galaxy S8 Plus"
                ] ,
                "Laptops" => [
                    "ASUS A540MA",
                    "HP 15-da0040nq"
                ]
            ],
            "Appliances" => [
                "Refrigerators" => [
                    "Arctic AD54240P+",
                    "Beko RDNE535E20DZM"
                ] ,
                "Cookers" => [
                    "Beko FSGT62110DXO",
                    "Samus SM 550 ABS"
                ]
            ],
            "Furnitures" => [
                "Couches" => [
                    "Lima",
                    "Olivia"
                ] ,
                "Chairs" => [
                    "Kring Fit",
                    "Kring Bokai"
                ]
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