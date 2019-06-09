<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Categories;
use AppBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActionController extends Controller
{
    /**
     * @Route("/", name="homepage_action")
     */
    public function homepageAction()
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findBy([], ["name" => "ASC"]);

        return $this->render(
            "action/homepage.html.twig", [
                "products" => $products
            ]
        );
    }

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
            "action/department.html.twig", [
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
            'action/category.html.twig',[
            "products" =>$products
        ]);
    }
}