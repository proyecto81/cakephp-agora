<?php

declare(strict_types=1);

namespace Agora\Policy;

use Authorization\IdentityInterface;

abstract class BasePolicy
{
    // Restricciones
    protected function isBackendUser(IdentityInterface $user): bool
    {
        return in_array($user->get('tipo_user_id'), [1, 2, 3]);
    }

    protected function isAdmin(IdentityInterface $user): bool
    {
        return $user->get('tipo_user_id') === 1;
    }

    protected function isMod(IdentityInterface $user): bool
    {
        return $user->get('tipo_user_id') === 2;
    }

    protected function isJournal(IdentityInterface $user): bool
    {
        return $user->get('tipo_user_id') === 3;
    }
}
