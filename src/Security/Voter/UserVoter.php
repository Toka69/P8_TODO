<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserVoter
 * @package App\Security\Voter
 */
class UserVoter extends Voter
{
    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, ['EDIT'])
            && $subject instanceof User;
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

        $userIdentifier = $user->getUserIdentifier();
        $subjectIdentifier = $subject->getUserIdentifier();
        $userRole = $user->getRoles()[0];

        if ($user instanceof UserInterface && ($userIdentifier === $subjectIdentifier or $userRole === 'ROLE_ADMIN')) {
            return true;
        }

        return false;
    }
}
