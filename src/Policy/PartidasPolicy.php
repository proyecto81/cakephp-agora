<?php

declare(strict_types=1);

namespace Agora\Policy;

use Agora\Model\Entity\Partidas;
use Authorization\IdentityInterface;

/**
 * Partidas policy
 */
class PartidasPolicy extends BasePolicy
{
    public function canIndex(IdentityInterface $user, Partida $partida)
    {
        // SuperAdmin y Admin pueden ver todas las partidas
        return $this->isBackendUser($user);
    }

    /**
     * Check if $user can add Partidas
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \Agora\Model\Entity\Partidas $partidas
     * @return bool
     */
    public function canCrear(IdentityInterface $user, Partida $partida)
    {
        return $this->isAdmin($user) || $this->isMod($user);
    }

    /**
     * Check if $user can edit Partidas
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \Agora\Model\Entity\Partidas $partidas
     * @return bool
     */
    public function canEditar(IdentityInterface $user, Partida $partida)
    {
        return $this->isAdmin($user) || $this->isMod($user);
    }


    /**
     * Check if $user can delete Partidas
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \Agora\Model\Entity\Partidas $partidas
     * @return bool
     */
    public function canDelete(IdentityInterface $user, Partidas $partidas) {
        return $this->isAdmin($user);
    }

    /**
     * Check if $user can view Partidas
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \Agora\Model\Entity\Partidas $partidas
     * @return bool
     */
    public function canView(IdentityInterface $user, Partidas $partidas) {}
}
