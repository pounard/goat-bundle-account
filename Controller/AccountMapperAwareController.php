<?php

declare(strict_types=1);

namespace Goat\AccountBundle\Controller;

use Goat\AccountBundle\Entity\Account;
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
    protected function findAccountOr404($id) : Account
    {
        try {
            return $this->getMapper('Goat:Account')->findOne($id);
        } catch (EntityNotFoundError $e) {
            throw $this->createNotFoundException();
        }
    }

    /**
     * Get current logged in user account or die
     *
     * @return Account
     */
    protected function getUserAccountOrDie() : Account
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
}
