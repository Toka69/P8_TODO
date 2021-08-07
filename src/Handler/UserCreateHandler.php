<?php


namespace App\Handler;


use App\Form\RegistrationFormType;
use App\Form\UserType;
use App\HandlerFactory\AbstractHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCreateHandler extends AbstractHandler
{
    protected $entityManager;

    protected $passwordHasher;

    protected $flashBag;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, FlashBagInterface $flashBag)
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
    }

    protected function process($data, array $options): void
    {
        $data->setRoles(["ROLE_USER"]);
        $data->setPassword(
            $this->passwordHasher->hashPassword(
                $data,
                $data->getPlainPassword()
            )
        );
        $this->entityManager->persist($data);
        $this->entityManager->flush();
        $this->flashBag->add('success', 'L\'utilisateur a été créé');
    }

    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefault("form_type", UserType::class);
        $resolver->setDefault("form_options", [
                "validation_groups" => ["Default", "password"]
            ]
        );
    }
}
