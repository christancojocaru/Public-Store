<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Cart;
use AppBundle\Entity\Department;
use AppBundle\Entity\Product;
use AppBundle\Service\ModifyProduct;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
     * @Route("/", name="homepage_action")
     * @return Response
     */
    public function items()
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();

        return $this->render(
            "action/homepage.html.twig", [
                "products" => $products
            ]
        );
    }

    /**
     * @Route("/nameAndStock", name="name_and_stock", methods={"POST"})
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function nameAndStockAction(Request $request)
    {
        $currentUser = $this->getUser()->getId();

        $userCarts = $this->getDoctrine()->getRepository(Cart::class)->findByUserId($currentUser);

        $productId = $request->request->get('product_id');

        foreach ($userCarts as $userCart) {
            if ($userCart->getProduct()->getId() == $productId) {
                break;//need an update
            }else {
                $product = $this->getDoctrine()->getRepository(Product::class)->find($productId);


            }
        }

        if (isset($parameters)) {
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
     * @Route("/cart/new", name="new_cart")
     * @param Request $request
     * @return Response
     */
    public function newCartAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->getUser();
        $productId = $request->request->get("product_id");
        $quantity = $request->request->get("quantity");

        $product = $em->getRepository(Product::class)->find($productId);

        $cart = new Cart();
        $cart->setQuantity($quantity);
        $cart->setProduct($product);
        $cart->setUser($currentUser);
        $em->persist($cart);
        $em->flush();

        return new Response("Cart added.");
    }

    /**
     * @Route("/cart", name="cart")
     * @return Response
     */
    public function cart(Request $request)
    {
        $currentUser= $this->get('security.token_storage')->getToken()->getUser();

        $carts = $this->getDoctrine()->getRepository(Cart::class)->findBy(["user" => $currentUser]);


        return $this->render(
            "action/cart.html.twig", [
                "carts" => $carts
        ]);
    }
}