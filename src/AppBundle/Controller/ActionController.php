<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Department;
use AppBundle\Entity\Products;
use AppBundle\Service\ExportCSV;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActionController extends Controller
{
    /**
     * @Route("/show", name="show_action")
     */
    public function show()
    {
        return $this->render(
            "action/show.html.twig");
    }

    /**
     * @Route("/update", name="update_action")
     * @param Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        $parameters = $request->request->all()["form"];
        $em = $this->getDoctrine()->getManager();
        $department = $em->getRepository(Department::class)->find($parameters["department"]);

        $products = new Products();
        $products->setName($parameters["name"]);
        $products->setPrice($parameters["price"]);
        $products->setStock($parameters["stock"]);
        $products->setDepartment($department);

        $em->persist($products);
        $em->flush();

        return new Response("Products: " . $products->getName() . $products->getPrice() . $products->getStock() . $products->getDepartment()->getName() . " was succesfully created!");


    }

    /**
     * @Route("/create", name="create_action")
     * @return Response
     */
    public function create()
    {
        /**@var Department $departments */
        $departments = $this->getDoctrine()->getRepository(Department::class)->getAll();

        /**@var Products $product */
        $product = new Products();

        $form = $this->createFormBuilder($product)
            ->setAction($this->generateUrl("update_action"))
            ->add("name", TextType::class)
            ->add("price", NumberType::class)
            ->add("stock", NumberType::class)


            ->add("department", ChoiceType::class, [
                'choices' => $departments
            ])
            ->add("submit", SubmitType::class, ["label" => "Submit"])
            ->getForm();

        return $this->render(
            "action/create.html.twig", [
                "form" => $form->createView()
            ]

        );
    }

    /**
     * @Route("/items")
     * @return Response
     */
    public function items()
    {
        $products = $this->getDoctrine()->getRepository(Products::class)->findAll();

        return $this->render(
            "action/showItems.html.twig", [
                "products" => $products
            ]
        );
    }

    /**
     * @Route("/cart/{name}", name="cart")
     * @return Response
     */
    public function cart($name, Request $request)
    {
        /**
         * @var Products $product
         */
        $product = current($this->getDoctrine()->getRepository(Products::class)->findByName($name));

        $form = $this->createFormBuilder()
            ->add('quantity', NumberType::class)
            ->add('submit', SubmitType::class, ['label' => 'Export CSV'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $quantity = $form->getData()["quantity"];
            $price = $product->getPrice();
            $list = array(
                array('No.', 'Product Name', 'Quantity', 'Price per product', 'Total Price'),
                array('1', $product->getName(), $quantity, $price, $quantity * $price)
            );
            $e = new ExportCSV;
            $e->exportCSV($list);

        }
        return $this->render(
            "action/cart.html.twig", [
                "form" => $form->createView(),
                "product" => $product
        ]);
    }
}