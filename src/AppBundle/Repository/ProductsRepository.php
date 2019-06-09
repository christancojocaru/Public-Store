<?php


namespace AppBundle\Repository;


use AppBundle\Entity\Product;
use Doctrine\ORM\EntityRepository;

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
}