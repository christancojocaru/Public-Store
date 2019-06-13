<?php


namespace AppBundle\Controller\User;


use AppBundle\Entity\Cart;
use AppBundle\Entity\UserProfile;
use AppBundle\Form\UserProfileForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ActionController
 * @package AppBundle\Controller\User
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * @Route("/cart", name="user_cart_action")
     * @return Response
     */
    public function cartAction()
    {
        $currentUser= $this->get('security.token_storage')->getToken()->getUser();
        $carts = $this->getDoctrine()->getRepository(Cart::class)->findBy(['user' =>$currentUser]);

        return $this->render(
            "user/cart.html.twig", [
            "carts" => $carts
        ]);
    }
    /**
     * @Route("/account", name="user_account_action")
     * @param Request $request
     * @return Response
     */
    public function accountAction(Request $request)
    {
        $userProfile = $this->getUser()->getUserProfile();
        $formProfile = $this->createForm(UserProfileForm::class, $userProfile);
        $updateStatus = null;
        $formProfile->handleRequest($request);
        if ($formProfile->isSubmitted() && $formProfile->isValid()) {
            /** @var UserProfile $userProfile */
            $userProfile = $formProfile->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($userProfile);
            $em->flush();
            $updateStatus = "DONE!";
        }
        return $this->render(
            'user/profile.html.twig', [
            'formProfile' => $formProfile->createView(),
            'updateStatus' => $updateStatus
        ]);
    }
}