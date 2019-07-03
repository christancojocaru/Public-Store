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
use Symfony\Component\DomCrawler\Crawler;

class CrawlerCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:crawler';


    protected function configure()
    {
        $this->setDescription('Crawler ss');
    }

    protected function execute(InputInterface $input,OutputInterface  $output)
    {
        $html = '
        <!DOCTYPE html>
        <html>
            <body>
                <p class="message">Hello World!</p>
                <p>Hello Crawler!</p>
            </body>
        </html>';

        $crawler = new Crawler($html);
        $pTag = $crawler->filter('body > p')->first();
        echo $pTag->text();

//        foreach ($crawler as $domElement) {
//            var_dump($domElement->);
//        }
    }
}