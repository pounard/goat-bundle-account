<?php

declare(strict_types=1);

namespace Goat\AccountBundle\Installer;

use Goat\Bundle\Installer\Updater;
use Goat\Core\Client\ConnectionInterface;
use Goat\Core\Transaction\Transaction;

/**
 * Self installer.
 */
class AccountUpdater extends Updater
{
    /**
     * {@inheritdoc}
     */
    public function installSchema(ConnectionInterface $connection, Transaction $transaction)
    {
        $connection->query(<<<EOT
CREATE TABLE account (
    id  SERIAL PRIMARY KEY,
    mail VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(128) UNIQUE DEFAULT NULL,
    user_name VARCHAR(255) NOT NULL,
    user_database VARCHAR(64) NOT NULL DEFAULT 'default',
    password_hash VARCHAR(255),
    salt VARCHAR(128),
    key_public BYTEA,
    key_private BYTEA,
    key_type VARCHAR(10),
    is_active BOOLEAN NOT NULL DEFAULT FALSE,
    is_admin BOOLEAN NOT NULL DEFAULT FALSE,
    validate_token VARCHAR(64),
    ts_added TIMESTAMP NOT NULL DEFAULT NOW(),
    UNIQUE (mail),
    UNIQUE (phone)
);
EOT
        );

        $connection->query(<<<EOT
CREATE TABLE account_onetime (
    id_account INTEGER NOT NULL,
    login_token VARCHAR(255) DEFAULT NULL,
    ts_expire TIMESTAMP NOT NULL DEFAULT NOW(),
    FOREIGN KEY (id_account) REFERENCES account (id) ON DELETE CASCADE
);
EOT
        );

        $connection->query(<<<EOT
CREATE TABLE session (
    id VARCHAR(255) NOT NULL,
    created TIMESTAMP NOT NULL DEFAULT NOW(),
    touched TIMESTAMP NOT NULL DEFAULT NOW(),
    data TEXT,
    PRIMARY KEY (id)
);
EOT
        );
    }
}
