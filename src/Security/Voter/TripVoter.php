<?php

namespace App\Security\Voter;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class TripVoter extends Voter
{
    public const EDIT = 'TRIP_EDIT';
    public const DELETE = 'TRIP_DELETE';
    public const PUBLIH = 'TRIP_PUBLIH';

    public function __construct(private Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DELETE, self::PUBLIH])
            && $subject instanceof \App\Entity\Trip;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            $vote?->addReason('The user must be logged in to access this resource.');

            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::PUBLIH:
            case self::EDIT:
                if ($subject->getUser() === $user) {
                    return true;
                }
                break;

            case self::DELETE:
                if ($subject->getUser() === $user || $this->security->isGranted("ROLE_ADMIN")) {
                    return true;
                }
                break;
        }

        return false;
    }
}
