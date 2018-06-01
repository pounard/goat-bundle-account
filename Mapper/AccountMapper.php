<?php

declare(strict_types=1);

namespace Goat\AccountBundle\Mapper;

use Goat\AccountBundle\Entity\Account;
use Goat\AccountBundle\Security\Crypt;
use Goat\Mapper\Error\EntityNotFoundError;
use Goat\Mapper\WritableSelectMapper;
use Goat\Runner\RunnerInterface;

/**
 * Account mapper
 */
class AccountMapper extends WritableSelectMapper
{
    /**
     * Default contructor
     *
     * @param RunnerInterface $runner
     */
    public function __construct(RunnerInterface $runner)
    {
        parent::__construct($runner, Account::class, ['id'], $runner->select('account'));
    }

    /**
     * Find a single user per its mail address
     *
     * @param string $mail
     *
     * @throws EntityNotFoundError
     *   In case account does not exists and $raiseErrorOnMissing is true
     *
     * @return Account
     */
    public function findUserByMail(string $mail, $raiseErrorOnMissing = true) : Account
    {
        return $this->findFirst(['mail' => $mail], $raiseErrorOnMissing);
    }

    /**
     * Generate a new user salt and change its password along with it
     *
     * @param Account $account
     * @param string $password
     *
     * @return Account
     *   The updated user account
     */
    public function updatePassword(Account $account, string $password) : Account
    {
        $salt     = Crypt::createSalt();
        $password = Crypt::getPasswordHash($password, $salt);

        return $this
            ->createUpdate(['id' => $account->getId()])
            ->sets(['salt' => $salt,'password_hash' => $password])
            ->returning('account.*')
            ->execute([], ['class' => Account::class])
            ->fetch()
        ;
    }
}
