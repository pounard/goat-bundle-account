<?php

namespace Goat\AccountBundle\Security\User;

use Goat\AccountBundle\Mapper\AccountMapperAwareTrait;
use Goat\AccountBundle\Security;
use Goat\AccountBundle\Security\Access;
use Goat\Mapper\Error\EntityNotFoundError;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class GoatAccountProvider implements UserProviderInterface
{
    use AccountMapperAwareTrait;

    /**
     * {inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        try {
            $account = $this->accountMapper->findUserByMail($username);
        } catch (EntityNotFoundError $e) {
            throw new UsernameNotFoundException();
        }

        // @todo Better than this
        if ($account->isAdmin()) {
            $roles = [Access::ROLE_NORMAL, Access::ROLE_ADMIN];
        } else {
            $roles = [Access::ROLE_NORMAL];
        }

        return new GoatUser($account, $roles);
    }

    /**
     * {inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException();
        }

        // @todo Nothing else than username and password matters for now.
        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return $class === GoatUser::class;
    }
}
