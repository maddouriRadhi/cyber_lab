<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        // Accept either isEnabled() or isActive()
        $enabled = method_exists($user, 'isEnabled') ? $user->isEnabled() : $user->isActive();

        if (!$enabled) {
            throw new CustomUserMessageAccountStatusException('Your account has been disabled.');
        }
    }

    public function checkPostAuth(UserInterface $user): void {}
}
