<?php


namespace App\Handler;


use App\Form\RegistrationFormType;
use App\HandlerFactory\AbstractHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationHandler extends AbstractHandler
{
    protected UserPasswordHasherInterface $passwordHasher;

    protected EntityManagerInterface $entityManager;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
    }

    protected function process($data, array $options): void
    {
        $data->setPassword(
            $this->passwordHasher->hashPassword(
                $data,
                $data->getPlainPassword()
            )
        );

        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefault("form_type", RegistrationFormType::class);
        $resolver->setDefault("form_options", [
            "validation_groups" => ["Default", "password"]
            ]
        );
    }
}