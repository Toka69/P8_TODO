<?php


namespace App\Security\Voter;


use App\Entity\Task;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskVoter extends Voter
{

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, ['EDIT', 'TOGGLE', 'DELETE'])
            && $subject instanceof Task;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if ($user instanceof UserInterface && $user === $subject->getUser()){
            return true;
        }

        return false;
    }
}