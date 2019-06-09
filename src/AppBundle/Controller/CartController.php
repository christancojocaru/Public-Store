<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends Controller
{
    /**
     * @Route("/cart", name="cart_action")
     * @return Response
     */
    public function cartAction()
    {
        $currentUser= $this->get('security.token_storage')->getToken()->getUser();
        $carts = $this->getDoctrine()->getRepository(Cart::class)->findBy(['user' =>$currentUser]);

        return $this->render(
            "action/cart.html.twig", [
            "carts" => $carts
        ]);
    }

    /**
     * @Route("/deleteCart", name="delete_cart_ajax", methods={"POST"})
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