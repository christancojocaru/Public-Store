<?php


namespace AppBundle\Repository;


use AppBundle\Entity\Categories;
use AppBundle\Entity\Product;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class ProductsRepository extends EntityRepository
{
    public function findByName($name)
    {
        /**
         * @param $name
         * return mixed
         */
        return $this->createQueryBuilder('p')
            ->where("p.name = :name")
            ->setParameter("name", $name)
            ->getQuery()
            ->execute();
    }

    public function findOneByName($name)
    {
        $product["name"] = $name;
        return $this->findOneBy($product);
    }

    public function findAllByCategory($categoryId)
    {
        return $this->createQueryBuilder('p')
            ->where("p.category = :categoryId")
            ->setParameter("categoryId", $categoryId)
            ->getQuery()
            ->execute();
    }

    public function checkProductName($productName)
    {
        $result = [];
        $products = $this->findBy(["name" => $productName]);
        if ($products) {
            /** @var Product $product */
            foreach ($products as $product) {
                $result[] = $product->getName();
            }
        }

        return $result;
    }
}