<?php

declare(strict_types=1);

namespace Goat\AccountBundle\Entity;

/**
 * User account entity
 */
class Account
{
    private $id;
    private $mail;
    private $user_name;
    private $user_database;
    private $password_hash;
    private $salt;
    private $key_public;
    private $key_private;
    private $key_type;
    private $is_active;
    private $is_admin;
    private $validate_token;
    private $ts_added;

    /**
     * Get identifier
     *
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Get mail
     *
     * @return string
     */
    public function getMail() : string
    {
        return $this->mail;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername() : string
    {
        return $this->user_name;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getPassword() : string
    {
        return $this->password_hash;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt() : string
    {
        return $this->salt;
    }

    /**
     * Get database
     *
     * @return string
     */
    public function getDatabase() : string
    {
        return $this->user_database;
    }

    /**
     * Get public key if any
     *
     * @return null|string
     */
    public function getPublicKey()
    {
        return $this->key_public;
    }

    /**
     * Get private key if any
     *
     * @return null|string
     */
    public function getPrivateKey()
    {
        return $this->key_private;
    }

    /**
     * Get public and private key type if any
     *
     * @return null|string
     */
    public function getKeyType()
    {
        return $this->key_type;
    }

    /**
     * Is user enabled
     *
     * @return bool
     */
    public function isActive() : bool
    {
        return $this->is_active;
    }

    /**
     * Is user admin
     *
     * @return bool
     */
    public function isAdmin() : bool
    {
        return $this->is_admin;
    }

    /**
     * Get user creation datetime
     *
     * @return \DateTimeInterface
     */
    public function getCreatedAt() : \DateTimeInterface
    {
        return $this->ts_added;
    }
 }
