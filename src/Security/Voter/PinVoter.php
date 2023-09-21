<?php

namespace App\Security\Voter;

use App\Entity\Pin;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class PinVoter extends Voter
{
    public const MANAGE = 'PIN_MANAGE';
    public const CREATE = 'PIN_CREATE';

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return $attribute === self::CREATE || in_array($attribute, [self::MANAGE])
            && $subject instanceof Pin;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::MANAGE:
                return $user->isVerified() && $user == $subject->getUser();
            case self::CREATE:
                return $user->isVerified();
        }

        return false;
    }
}
