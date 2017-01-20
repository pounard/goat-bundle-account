<?php

declare(strict_types=1);

namespace Goat\AccountBundle\Mapper;

/**
 * Account mapper aware base for a few objects
 */
trait AccountMapperAwareTrait
{
    /**
     * @var AccountMapper
     */
    private $accountMapper;

    /**
     * Default constructor
     *
     * @param AccountMapper $accountMapper
     */
    public function __construct(AccountMapper $accountMapper)
    {
        $this->accountMapper = $accountMapper;
    }

    /**
     * Set account mapper
     *
     * @param AccountMapper $accountMapper
     */
    public function setAccountMapper(AccountMapper $accountMapper)
    {
        $this->accountMapper = $accountMapper;
    }
}
