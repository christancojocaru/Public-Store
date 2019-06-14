<?php


namespace AppBundle\Controller\Security;


use AppBundle\Entity\User;
use AppBundle\Form\ChangePasswordForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ChangePasswordController extends Controller
{
    /**
     * @var UserPasswordEncoderInterface $passwordEncoder
     */
    private $passwordEncoder;

    /**
     * @Route("/password", name="security_change_password_action")
     * @param Request $request
     * @param UserInterface $user
     * @return Response
     */
    public function changeAction(Request $request, UserInterface $user)
    {
        $form = $this->createForm(ChangePasswordForm::class);
        $form->add('submit', SubmitType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userData = $form->getData();
            /** @var User $user */
            if ($this->passwordEncoder->isPasswordValid($user, $userData['password'])) {
                $em = $this->getDoctrine()->getManager();
                $user->setPlainPassword($userData['plainPassword']);
                $em->persist($user);
                $em->flush();
                return $this->redirectToRoute('homepage_action');
            }else {
                $form->get('password')->addError( new FormError('Old Password Incorrect!'));
            }
        }

        return $this->render(
            'security/password.html.twig',[
                "form" => $form->createView()
        ]);
    }

    /**
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @required
     */
    public function getPasswordEncoder(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->passwordEncoder = $userPasswordEncoder;
    }
}