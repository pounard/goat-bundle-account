<?php

declare(strict_types=1);

namespace Goat\AccountBundle\Controller;

use Goat\AccountBundle\Entity\Account;
use Goat\AccountBundle\Mapper\AccountMapper;
use Goat\AccountBundle\Security\User\GoatUser;
use Goat\Bundle\Controller\DatabaseAwareControllerTrait;
use Goat\Mapper\Error\EntityNotFoundError;

trait AccountMapperAwareController
{
    use DatabaseAwareControllerTrait;

    /**
     * Get task or throw a 404 or 403 error depending on data
     *
     * @param int $id
     *   Account identifier
     *
     * @return Account
     */
    final protected function findAccountOr404($id) : Account
    {
        try {
            return $this->getAccountMapper()->findOne($id);
        } catch (EntityNotFoundError $e) {
            throw $this->createNotFoundException();
        }
    }

    /**
     * Get current logged in user account or die
     *
     * @return Account
     */
    final protected function getUserAccountOrDie() : Account
    {
        $user = $this->getUser();

        if ($user instanceof GoatUser) {
            $account = $this->getUser()->getAccount();

            if ($account) {
                return $account;
            }
        }

        throw $this->createNotFoundException();
    }

    /**
     * Get default account mapper
     *
     * @return AccountMapper
     */
    final protected function getAccountMapper() : AccountMapper
    {
        return $this->getMapper('Goat:Account');
    }
}
