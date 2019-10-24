<?php

namespace AppBundle\Command;

use AppBundle\Document\CrawlerProduct;
use AppBundle\Entity\Categories;
use AppBundle\Entity\Departments;
use AppBundle\Entity\Product;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use Goutte\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CrawlerCommand extends Command
{
    /** @var string  */
    protected static $defaultName = 'app:crawler';
    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var DocumentManager */
    private $documentManager;

    const URL = 'https://altex.ro';

    protected function configure()
    {
        $this->setDescription('Crawling');
//            ->addArgument('url', InputArgument::REQUIRED, "The URL to crawl.'");
    }

    protected function execute(InputInterface $input,OutputInterface  $output)
    {
//        $url = $input->getArgument('url');
        $time_pre = microtime(true);
        $client = new Client();
        $crawler = $client->request('GET', self::URL);

        $departments = $this->extractDepartments($crawler);

        $data = array();
        $allProductsName = array();
//        foreach ($departments as $dEQ => $department) {
        $dEQ = 0;
            echo PHP_EOL.'Dep '.$dEQ.PHP_EOL;
            $departmentUrl = self::URL.$this->extractDepartmentsLink($crawler, $dEQ);
            $departmentClient = new Client();
            $departmentCrawler = $departmentClient->request('GET', $departmentUrl);

            $categories = $this->extractCategories($departmentCrawler);

        $data[$departments[$dEQ]] = array();
        $cEQ = 0;
//            foreach ($categories as $cEQ => $category) {
                $categoriesUrl = $this->extractCategoriesLink($departmentCrawler, $cEQ);
                $categoriesClient = new Client();
                $categoriesCrawler = $categoriesClient->request('GET', $categoriesUrl);

                $subCategories = $this->checkSubCategory($categoriesCrawler);

                if ( $subCategories ) {
                    foreach ($subCategories as $scEQ => $subCategory) {
                        echo 'SecCat'.$scEQ.' / ';
                        $subCategoriesUrl = $this->extractCategoriesLink($categoriesCrawler, $scEQ);
                        $subCategoriesClient = new Client();
                        $subCategoriesCrawler = $subCategoriesClient->request('GET', $subCategoriesUrl);

                        $subSubCategories = $this->checkSubCategory($subCategoriesCrawler);
                        if ($subSubCategories)continue;

                        $productsName = $this->extractProductName($subCategoriesCrawler);
                        $productsBestPrice = $this->extractProductPrice($subCategoriesCrawler);

                        foreach ($productsName as $key => $productName) {
                            if ( array_search($productName, $allProductsName) )continue;
                            $allProductsName[] = $productName;
                            $product[0] = $productName;
                            $product[1] = $productsBestPrice[$key];
                            $key += count($productsName) * $scEQ;
                            $data[$departments[$dEQ]][$categories[$cEQ]][$key] = $product;
                        }
                    }

                }else{
                    echo 'Cat '.$cEQ.' / ';
                    $productsName = $this->extractProductName($categoriesCrawler);
                    $productsBestPrice = $this->extractProductPrice($categoriesCrawler);

                    $data[$departments[$dEQ]][$categories[$cEQ]] = array();
                    foreach ($productsName as $key => $productName) {
                        if ( array_search($productName, $allProductsName) )continue;
                        $allProductsName[] = $productName;
                        $product[0] = $productName;
                        $product[1] = $productsBestPrice[$key];
                        $data[$departments[$dEQ]][$categories[$cEQ]][$key] = $product;
                    }
                }
//            }
//        }
//        $message = $this->addToDatabases($data);
        $time_post = microtime(true);
        $exec_time = $time_post - $time_pre;
//        $output->writeln(PHP_EOL.$message);
        $output->writeln(sprintf('Execution time was : %s seconds', intval($exec_time)));
    }

    private function checkSubCategory($crawler)
    {
        $categories = [];
        $childrens = $crawler
            ->filterXPath('//li[contains(@class, "Products-item")]')
            ->filter('a')
            ->children()
            ->count();

        if ( $childrens > 1 ) {
            $categories = $this->extractCategories($crawler);
        }

        return $categories;
    }

    private function extractCategories($crawler)
    {
        return $crawler
            ->filterXPath('//li[contains(@class, "Products-item")]')
            ->filter('a > h2')
            ->extract("_text");
    }

    private function extractDepartmentsLink($crawler, $eq)
    {
        return $crawler
            ->filterXPath('//li[contains(@class, "ProductsMenu")]')
            ->eq($eq)
            ->filter('a')
            ->attr('href');
    }

    private function extractCategoriesLink($crawler, $eq)
    {
        return $crawler
            ->filterXPath('//li[contains(@class, "Products-item")]')
            ->eq($eq)
            ->filter('a')
            ->attr('href');
    }

    private function extractDepartments($crawler)
{
    return $crawler
        ->filterXPath('//li[contains(@class, "ProductsMenu")]')
        ->filter('a')
        ->extract("_text");
}

    private function extractProductName($crawler)
    {
        return $crawler
            ->filterXPath('//li[contains(@class, "Products-item")]')
            ->filterXPath('//h2')
            ->reduce(function ($node, $i) {
                return ($i % 2) == 0;
            })
            ->extract('_text');
    }

    private function extractProductPrice($crawler)
    {
        return $crawler
            ->filterXPath('//ul[contains(@class, "Products--4to2")]')
            ->filterXPath('//li[contains(@class, "Products-item")]')
            ->filterXPath('//meta')
            ->reduce(function ($node, $i) {
                return ($i % 2) == 0;
            })
            ->extract(array('content'));
    }

    private function addToDatabases($data)
    {
        $noOfProducts = 0;
        $noOfCategories = 0;
        foreach ($data as $department => $categories) {
//            echo PHP_EOL."//////////Dep ".$department.PHP_EOL;
            $newDepartment = new Departments();
            $newDepartment->setName($department);
            $this->entityManager->persist($newDepartment);

            foreach ($categories as $category => $products) {
//                echo "/////Cat ".$category.PHP_EOL;
                $newCategory = new Categories();
                $newCategory->setName($category);
                $newCategory->setDepartment($newDepartment);
                $this->entityManager->persist($newCategory);

                foreach ($products as $product) {
//                    echo "Name ".$product[0]."  ////    ";
//                    echo "Price".$product[1].PHP_EOL;
                    $stock = rand(1, 30);

                    $newProduct = new Product();
                    $newProduct->setName($product[0]);
                    $newProduct->setStock($stock);
                    $newProduct->setPrice($product[1]);
                    $newProduct->setCategory($newCategory);
                    $this->entityManager->persist($newProduct);

                    $newCrawlerProduct = new CrawlerProduct();
                    $newCrawlerProduct->setDepartment($department);
                    $newCrawlerProduct->setCategory($category);
                    $newCrawlerProduct->setName($product[0]);
                    $newCrawlerProduct->setStock($stock);
                    $newCrawlerProduct->setPrice($product[1]);
                    $newCrawlerProduct->setDate();
                    $this->documentManager->persist($newCrawlerProduct);
                    $this->documentManager->flush();
                }
                $noOfProducts += count($products);
            }
            $noOfCategories += count($categories);
        }
        $this->entityManager->flush();

        return sprintf('Saved %s departments and %s categories and %s products into database', count($data), $noOfCategories, $noOfProducts);
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @required
     */
    public function setEntityManager(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param DocumentManager $documentManager
     * @required
     */
    public function setDocumentManager(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }
}