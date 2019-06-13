<?php


namespace AppBundle\Form;


use AppBundle\Entity\UserProfile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProfileForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, $this->attrs())
            ->add('lastName', TextType::class, $this->attrs())
            ->add('email', EmailType::class, $this->attrs())
            ->add('mobileNumber', IntegerType::class, $this->attrs())
            ->add('address', TextType::class, $this->attrs())
            ->add('city', TextType::class, $this->attrs())
            ->add('country', TextType::class, $this->attrs());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserProfile::class,
        ]);
    }

    public function attrs()
    {
        return [
            'label_attr' => ['class' => 'control-label'],
            'attr' => ['class' => 'form-control input-circle-right']
        ];
    }
}
