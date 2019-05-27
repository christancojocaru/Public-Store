<?php


namespace AppBundle\Repository;


use AppBundle\Entity\Department;
use Doctrine\ORM\EntityRepository;

class DepartmentRepository extends EntityRepository
{
    public function findByName($name)
    {
        /**
         * @param $name
         * return mixed
         */
        return $this->createQueryBuilder('id')
            ->where("id.name = :name")
            ->setParameter("name", $name)
            ->getQuery()
            ->execute();
    }

    public function getAll()
    {

        $departments = [];
        /** @var Department $department */
        foreach ($this->findAll() as $department){
            $departments[$department->getName()] = $department->getId();
        }

        return $departments;
    }
}