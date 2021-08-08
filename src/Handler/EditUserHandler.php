<?php

namespace App\Handler;

use App\Form\UserType;
use App\HandlerFactory\AbstractHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class EditUserHandler
 * @package App\Handler
 */
class EditUserHandler extends AbstractHandler
{
    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $entityManager;

    /**
     * @var UserPasswordHasherInterface
     */
    protected UserPasswordHasherInterface $passwordHasher;

    /**
     * @var FlashBagInterface
     */
    protected FlashBagInterface $flashBag;

    /**
     * EditUserHandler constructor.
     * @param UserPasswordHasherInterface $passwordHasher
     * @param EntityManagerInterface $entityManager
     * @param FlashBagInterface $flashBag
     */
    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        FlashBagInterface $flashBag
    ) {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
    }

    /**
     * @param $data
     * @param array $options
     */
    protected function process($data, array $options): void
    {
        $data->setPassword(
            $this->passwordHasher->hashPassword(
                $data,
                $data->getPlainPassword()
            )
        );

        $this->entityManager->flush();

        $this->flashBag->add('success', "L'utilisateur a bien été modifié");
    }

    /**
     * @param OptionsResolver $resolver
     */
    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefault("form_type", UserType::class);
        $resolver->setDefault("form_options", [
                "validation_groups" => ["Default", "password"]
            ]);
    }
}
