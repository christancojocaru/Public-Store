<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Department;
use AppBundle\Entity\Product;
use AppBundle\Service\ExportCSV;
use AppBundle\Service\ModifyProduct;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActionController extends Controller
{
    /**
     * @Route("/nameAndStock", name="name_and_stock", methods={"POST"})
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function nameAndStockAction(Request $request)
    {
        $parameters = $request->request->all();
        if (isset($parameters['product_id'])) {
            $product = $this->getDoctrine()->getRepository(Product::class)->find($parameters['product_id']);
            if (!is_null($product)) {
                $arrayProduct = [
                    'name' => $product->getName(),
                    'stock' => $product->getStock(),
                ];

                return new JsonResponse($arrayProduct);
            };

            return new Response("Product not found!");
        }

        return new Response("Incorect parameter");
    }

    /**
     * @Route("/addtoCart", methods={"POST"})
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function addtoCartAction(Request $request)
    {

        $parameters = $request->request->all();
        if (isset($parameters['product_id']) && isset($parameters['quantity'])) {
            $product = $this->getDoctrine()->getRepository(Product::class)->find($parameters['product_id']);
            if (!is_null($product)) {
                $arrayProduct = [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'quantity' => $parameters['quantity'],
                    'totalPrice' => $product->getPrice() * intval($parameters['quantity']),
                ];

                return new JsonResponse($arrayProduct);
            };

            return new Response("Product not found or quantity is not a number!");
        }

        return new Response("Incorect parameter");
    }

    /**
     * @Route("/list")
     * @return Response
     */
    public function items()
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();

        return $this->render(
            "action/list.html.twig", [
                "products" => $products
            ]
        );
    }

    /**
     * @Route("/create", name="create_action")
     * @param Request $request
     * @param ModifyProduct $modifyProduct
     * @return Response
     */
    public function create(Request $request, ModifyProduct $modifyProduct)
    {
        /**@var Department $departments */
        $departments = $this->getDoctrine()->getRepository(Department::class)->getAll();

        /**@var Product $product */
        $product = new Product();

        $form = $this->createFormBuilder($product)
            ->add("name", TextType::class)
            ->add("price", NumberType::class)
            ->add("stock", NumberType::class)
            ->add("department", ChoiceType::class, [
                'choices' => $departments
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
            "action/create.html.twig", [
                "form" => $form->createView()
            ]
        );
    }

    /**
     * @Route("/update", name="update_action")
     * @param Request $request
     * @param ModifyProduct $modifyProduct
     * @return Response
     */
    public function update(Request $request, ModifyProduct $modifyProduct)
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
            ->add("department", TextType::class, [
                "data" => $product->getDepartment()->getName(),
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
            "action/create.html.twig", [
                "form" => $form->createView()
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
         * @var Product $product
         */
        $product = current($this->getDoctrine()->getRepository(Product::class)->findByName($name));

        $form = $this->createFormBuilder()
            ->add('id', HiddenType::class, ['data' => $product->getId()])
            ->add('quantity', IntegerType::class, [
                'data' => 1,
                'attr' => [
                    'max' => $product->getStock(),
                    'min' => 1
                ]
            ])
            ->add('submit', SubmitType::class, ['label' => 'Export CSV'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $productId = $form->getData()['id'];

            $submittedProduct = $this->getDoctrine()->getRepository(Product::class)->find($productId);
            $name = $submittedProduct->getName();
            $price = $submittedProduct->getPrice();

            $quantity = $form->getData()["quantity"];


            $list = array(
                array('No.', 'Product Name', 'Quantity', 'Price per product', 'Total Price'),
                array('1', $name, $quantity, $price, $quantity * $price)
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