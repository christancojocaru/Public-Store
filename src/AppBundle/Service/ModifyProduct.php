<?php


namespace AppBundle\Service;


use AppBundle\Entity\Categories;
use AppBundle\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class ModifyProduct
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function create(array $parameters)
    {
        $productDepartment = $this->em->getRepository(Categories::class)->find($parameters["department"]);

        $product = new Product();
        $product->setName($parameters["name"]);
        $product->setPrice($parameters["price"]);
        $product->setStock($parameters["stock"]);
        $product->setCategory($productDepartment);

        $this->em->persist($product);
        $this->em->flush();
    }

    public function update(array $parameters, $product)
    {
        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id ' . $parameters["name"]
            );
        }

        if ($product->getName() != $parameters["name"]) {
            $product->setName($parameters["name"]);
        }

        $product->setPrice($parameters["price"]);
        $product->setStock($parameters["stock"]);
        $this->em->flush();
    }
}