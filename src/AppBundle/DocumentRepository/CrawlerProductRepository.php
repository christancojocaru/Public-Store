<?php


namespace AppBundle\DocumentRepository;


use Doctrine\ODM\MongoDB\DocumentRepository;

class CrawlerProductRepository extends DocumentRepository
{
    public function findAllOrderedByName()
    {
        return $this->findBy(['name' => 699.9]);
    }
}