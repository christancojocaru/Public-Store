<?php


namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class CartRepository extends EntityRepository
{
    public function findByUserId($id)
    {
        return $this->createQueryBuilder("cart")
            ->where("cart.user= :id")
            ->setParameter("id" , $id)
            ->getQuery()
            ->execute();
    }
}