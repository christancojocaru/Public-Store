<?php


namespace AppBundle\Controller\Homepage;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends Controller
{
    /**
     * @Route("/", name="homepage_action")
     */
    public function homepageAction()
    {
        $roles = $this->getUser()->getRoles();
        if (in_array("ROLE_ADMIN", $roles)) {
            return $this->redirectToRoute("admin_homepage_action");
        }
        return $this->render(
            "homepage/homepage.html.twig");
    }
}