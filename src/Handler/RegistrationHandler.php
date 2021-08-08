<?php


namespace App\Handler;


use App\Form\RegistrationFormType;
use App\HandlerFactory\AbstractHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class RegistrationHandler
 * @package App\Handler
 */
class RegistrationHandler extends AbstractHandler
{
    /**
     * @var UserPasswordHasherInterface
     */
    protected UserPasswordHasherInterface $passwordHasher;

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $entityManager;

    /**
     * @var FlashBagInterface
     */
    protected FlashBagInterface $flashBag;

    /**
     * RegistrationHandler constructor.
     * @param UserPasswordHasherInterface $passwordHasher
     * @param EntityManagerInterface $entityManager
     * @param FlashBagInterface $flashBag
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, FlashBagInterface $flashBag)
    {
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

    /**
     * @param OptionsResolver $resolver
     */
    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefault("form_type", RegistrationFormType::class);
        $resolver->setDefault("form_options", [
            "validation_groups" => ["Default", "password"]
            ]
        );
    }
}
