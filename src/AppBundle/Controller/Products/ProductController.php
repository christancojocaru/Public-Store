<?php


namespace AppBundle\Controller\Products;


use AppBundle\Document\Product as DocumentProduct;
use AppBundle\Entity\Categories;
use AppBundle\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProductController
 * @package AppBundle\Controller
 * @Route("/product")
 */
class ProductController extends Controller
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @Route("/department/{id}", name="department_action")
     * @param $id
     * @return Response
     */
    public function departmentAction($id)
    {
        $categories = $this->em->getRepository(Categories::class)->findAllByDepartment($id);
        /** @var Categories $category */
        foreach ($categories as $category) {
            $data[$category->getName()] = $this->em->getRepository(Product::class)->findBy(["category" => $category->getId()]);
        }

        return $this->render(
            "products/department.html.twig", [
                "categories" => $data
            ]
        );
    }

    /**
     * @Route("/category/{id}", name="category_action")
     * @param $id
     * @return Response
     */
    public function categoryAction($id)
    {
        $products = $this->em->getRepository(Product::class)->findAllByCategory($id);

        return $this->render(
            'products/category.html.twig',[
            "products" =>$products
        ]);
    }

    /**
     * @Route("/{id}", name="show_action")
     * @param $id
     * @return Response
     */
    public function showAction($id)
    {
        /** @var Product $entityProduct */
        $entityProduct = $this->getDoctrine()->getRepository(Product::class)->find($id);

        /** @var DocumentProduct $documentProduct */
        $documentProduct = $this->get('doctrine_mongodb')
            ->getRepository(DocumentProduct::class)
            ->findOneByName($entityProduct->getName());

        if ($entityProduct->getPrice() != $documentProduct->getPrice()) {
            return new Response("Products aren't the same");
        }

        return $this->render(
            'products/show.html.twig', [
                "product" => $entityProduct
            ]
        );
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @required
     */
    public function getProductRepository(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }
}