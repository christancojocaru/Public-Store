<?php


namespace AppBundle\Controller\Ajax;


use AppBundle\Entity\Cart;
use AppBundle\Entity\Product;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AjaxController extends Controller
{
    /**
     * @Route("/product/addToCart", name="add_to_cart_ajax", methods={"POST"})
     * @param Request $request
     * @return Response
     * @throws NonUniqueResultException
     */
    public function addToCartAjax(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->getUser();
        $productId = $request->request->get('id');
        $product = $this->getDoctrine()->getRepository(Product::class)->find($productId);

        if (!$product) {
            return new Response("Product not found!");
        }
        /** @var Cart $cartProduct */
        $cartProduct = $this->getDoctrine()->getRepository(Cart::class)->findByUserAndProduct($currentUser, $productId);
        if(!is_null($cartProduct)){
            $cartProduct->incrementQuantity();
            $em->flush();
            return new Response(sprintf("Product %s updated quantity to %d!", $product->getName(), $cartProduct->getQuantity()));
        }

        $newCart = new Cart();
        $newCart->setProduct($product);
        $newCart->setUser($currentUser);
        $newCart->setQuantity(1);
        $em->persist($newCart);
        $em->flush();

        return new Response("Product " . $product->getName() . " was added to your cart!");
    }


    /**
     * @Route("/user/deleteCart", name="delete_cart_ajax", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function deleteCartAjax(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $cartId = $request->request->get("id");
        $cartToDelete = $em->getRepository(Cart::class)->find($cartId);
        if (!$cartToDelete) {
            return new Response("Product not found!");
        }
        $em->remove($cartToDelete);
        $em->flush();

        return new Response("Deleted successfully!");
    }
}