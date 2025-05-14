<?php

declare(strict_types=1);

namespace Agora\Policy;

use Authorization\IdentityInterface;
use Cake\Http\ServerRequest;

class BackendControllerPolicy extends BasePolicy
{
    public function canAccess(IdentityInterface $user, ServerRequest $request): bool
    {
        return $this->isBackendUser($user);
    }
}