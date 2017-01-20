<?php

namespace Goat\AccountBundle;

use Goat\AccountBundle\Command\AccountPasswordCommand;
use Goat\AccountBundle\Command\CreateAccountCommand;

use Symfony\Component\Console\Application;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class GoatAccountBundle extends Bundle
{
    public function registerCommands(Application $application)
    {
        $application->add(new AccountPasswordCommand());
        $application->add(new CreateAccountCommand());
    }
}
