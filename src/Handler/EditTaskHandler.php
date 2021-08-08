<?php


namespace App\Handler;


use App\Form\TaskType;
use App\HandlerFactory\AbstractHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class EditTaskHandler
 * @package App\Handler
 */
class EditTaskHandler extends AbstractHandler
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
     * EditTaskHandler constructor.
     * @param UserPasswordHasherInterface $passwordHasher
     * @param EntityManagerInterface $entityManager
     * @param FlashBagInterface $flashBag
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager,
                                FlashBagInterface $flashBag)
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
        $this->entityManager->flush();
        $this->flashBag->add('success', 'La tâche a bien été modifiée.');
    }

    /**
     * @param OptionsResolver $resolver
     */
    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefault("form_type", TaskType::class);
    }
}
