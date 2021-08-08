<?php


namespace App\Handler;


use App\Form\TaskType;
use App\HandlerFactory\AbstractHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Class CreateTaskHandler
 * @package App\Handler
 */
class CreateTaskHandler extends AbstractHandler
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
     * @var Security
     */
    protected Security $security;

    /**
     * CreateTaskHandler constructor.
     * @param UserPasswordHasherInterface $passwordHasher
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param FlashBagInterface $flashBag
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager,
                                Security $security, FlashBagInterface $flashBag)
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
        $this->security = $security;
    }

    /**
     * @param $data
     * @param array $options
     */
    protected function process($data, array $options): void
    {
        $data->setIsDone(false);
        $data->setUser($this->security->getUser());
        $this->entityManager->persist($data);
        $this->entityManager->flush();

        $this->flashBag->add('success', 'La tâche a été bien été ajoutée.');
    }

    /**
     * @param OptionsResolver $resolver
     */
    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefault("form_type", TaskType::class);
    }
}
