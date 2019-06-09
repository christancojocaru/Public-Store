<?php


namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Categories;
use AppBundle\Entity\Product;
use AppBundle\Service\ModifyProduct;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ActionController extends Controller
{
    /**
     * @Route("/admin/homepage", name="admin_homepage_action")
     * @return Response
     */
    public function adminHomepageAction()
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();

        return $this->render(
            "admin/action/homepage.html.twig", [
                "products" => $products
            ]
        );
    }

    /**
     * @Route("/admin/create", name="admin_create_action")
     * @param Request $request
     * @param ModifyProduct $modifyProduct
     * @return Response
     */
    public function createAction(Request $request, ModifyProduct $modifyProduct)
    {
        /**@var Categories $categories */
        $categories = $this->getDoctrine()->getRepository(Categories::class)->getAll();

        /**@var Product $product */
        $product = new Product();

        $form = $this->createFormBuilder($product)
            ->add("name", TextType::class)
            ->add("price", NumberType::class)
            ->add("stock", NumberType::class)
            ->add("categories", ChoiceType::class, [
                'choices' => $categories
            ])
            ->add("submit", SubmitType::class, ["label" => "Submit"])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $parameters = $request->request->get('form');

            $modifyProduct->create($parameters);

            return new Response("Product was successfully created!");
        }

        return $this->render(
            "admin/action/create.html.twig", [
                "form" => $form->createView()
            ]
        );
    }

    /**
     * @Route("/admin/update", name="admin_update_action")
     * @param Request $request
     * @param ModifyProduct $modifyProduct
     * @return Response
     */
    public function updateAction(Request $request, ModifyProduct $modifyProduct)
    {
        $parameters = $request->query->all();

        /**@var Product $product */
        $product = $this->getDoctrine()->getRepository(Product::class)->findOneByName($parameters);

        if (!$product) {
            throw $this->createNotFoundException('Product with ' . (array_keys($parameters))[0] . '= \'' . $parameters["name"] . '\' do not exist in our store!');
        }

        $form = $this->createFormBuilder($product)
            ->add("name", TextType::class, ["data" => $product->getName()])
            ->add("price", NumberType::class, ["data" => $product->getPrice()])
            ->add("stock", IntegerType::class, ["data" => $product->getStock()])
            ->add("categories", TextType::class, [
                "data" => $product->getCategory()->getName(),
                "disabled" => true
            ])
            ->add("submit", SubmitType::class, ["label" => "Submit"])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $parameters = $request->request->get('form');

            $modifyProduct->update($parameters, $product);

            return new Response("Product was successfully updated!");
        }

        return $this->render(
            "admin/action/update.html.twig", [
                "form" => $form->createView()
            ]
        );
    }
}