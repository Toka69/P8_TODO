<?php

namespace App\Security\Voter;

use App\Entity\Task;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class TaskVoter
 * @package App\Security\Voter
 */
class TaskVoter extends Voter
{
    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, ['EDIT', 'TOGGLE', 'DELETE'])
            && $subject instanceof Task;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if ($user instanceof UserInterface && $user === $subject->getUser()) {
            return true;
        }

        if (
            $user instanceof UserInterface &&
            current($user->getRoles()) === "ROLE_ADMIN" &&
            $subject->getUser()->getUsername() === "anonyme"
        ) {
            return true;
        }

        return false;
    }
}
