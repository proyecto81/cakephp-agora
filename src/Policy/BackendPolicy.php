<?php

declare(strict_types=1);

namespace Agora\Policy;

use Authorization\IdentityInterface;

class BackendPolicy extends BasePolicy
{
    public function canAccess(IdentityInterface $user)
    {
        //return true;
        return parent::isBackendUser($user);
    }
}
