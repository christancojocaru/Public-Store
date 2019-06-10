<?php


namespace AppBundle\Controller;


use AppBundle\Entity\User;
use AppBundle\Entity\UserProfile;
use AppBundle\Form\UserProfileForm;
use AppBundle\Form\UserRegistrationForm;
use AppBundle\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
    /**
     * @Route("/register", name="user_register")
     * @param Request $request
     * @return Response
     */
    public function registerAction(Request $request)
    {
        $form = $this->createForm(UserRegistrationForm::class);
        $form->add('submit', SubmitType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $user->setRoles(["ROLE_USER"]);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->get('security.authentication.guard_handler')
                ->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $this->get(LoginFormAuthenticator::class),
                    'main'
                );
        }

        return $this->render(
            'security/register.html.twig', [
                "form" => $form->createView()
            ]);
    }

    /**
     * @Route("/user/account", name="user_account_action")
     * @param Request $request
     * @return Response
     */
    public function accountAction(Request $request)
    {
        $currentUser = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        if ($currentUser->getUserProfile()) {
            $userProfile = $em->getRepository(UserProfile::class)->find($currentUser->getUserProfile()->getId());
        }else {
            $userProfile = new UserProfile();
        }
        $formProfile = $this->createForm(UserProfileForm::class, $userProfile);
        $formProfile->handleRequest($request);
        if ($formProfile->isSubmitted() && $formProfile->isValid()) {
            $currentUser->setUserProfile($userProfile);
            $em->persist($userProfile);
            $em->flush();
            return new Response("form profile submit success");
        }
        return $this->render(
            'action/user/profile.html.twig', [
                'formProfile' => $formProfile->createView()
            ]);
    }
}