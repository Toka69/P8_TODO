<?php


namespace App\Security\Voter;


use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{

    protected function supports(string $attribute, $subject)
    {
        return in_array($attribute, ['EDIT'])
            && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if ($user instanceof UserInterface && ($user->getUserIdentifier() === $subject->getUserIdentifier() OR $user->getRoles()[0] === 'ROLE_ADMIN')){
            return true;
        }

        return false;
    }
}