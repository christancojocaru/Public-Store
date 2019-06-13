<?php


namespace AppBundle\Controller\Products;


use AppBundle\Entity\Categories;
use AppBundle\Entity\Product;
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
     * @Route("/department/{id}", name="department_action")
     * @param $id
     * @return Response
     */
    public function departmentAction($id)
    {
        $categories = $this->getDoctrine()->getRepository(Categories::class)->findAllByDepartment($id);
        foreach ($categories as $category) {
            $products[$category->getName()] = $this->getDoctrine()->getRepository(Product::class)->findBy(["category" => $category->getId()]);
        }

        return $this->render(
            "products/department.html.twig", [
                "categories" => $products
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
        $products = $this->getDoctrine()->getRepository(Product::class)->findAllByCategory($id);

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
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        return $this->render(
            'products/show.html.twig', [
                "product" => $product
            ]
        );
    }
}