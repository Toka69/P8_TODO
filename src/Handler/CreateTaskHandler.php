<?php


namespace App\Handler;


use App\Form\TaskType;
use App\HandlerFactory\AbstractHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;

class CreateTaskHandler extends AbstractHandler
{
    protected EntityManagerInterface $entityManager;

    protected UserPasswordHasherInterface $passwordHasher;

    protected FlashBagInterface $flashBag;

    protected Security $security;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager,
                                Security $security, FlashBagInterface $flashBag)
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
        $this->security = $security;
    }

    protected function process($data, array $options): void
    {
        $data->setIsDone(false);
        $data->setUser($this->security->getUser());
        $this->entityManager->persist($data);
        $this->entityManager->flush();

        $this->flashBag->add('success', 'La tâche a été bien été ajoutée.');
    }

    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefault("form_type", TaskType::class);
        $resolver->setDefault("form_options", [
                "validation_groups" => ["Default", "password"]
            ]
        );
    }
}
