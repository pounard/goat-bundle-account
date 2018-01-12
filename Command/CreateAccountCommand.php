<?php

namespace Goat\AccountBundle\Command;

use Goat\AccountBundle\Mapper\AccountMapper;
use Goat\Mapper\Error\EntityNotFoundError;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateAccountCommand extends Command
{
    private $accountMapper;

    /**
     * Default constructor
     */
    public function __construct(AccountMapper $accountMapper)
    {
        $this->accountMapper = $accountMapper;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('account:create')
            ->setDescription('Create new account')
            ->addArgument('name', InputArgument::REQUIRED, "User display name")
            ->addArgument('mail', InputArgument::REQUIRED, "User e-mail address")
            ->addOption('admin', null, InputOption::VALUE_NONE, 'Create the user as administrator')
            ->addOption('disabled', null, InputOption::VALUE_NONE, 'Leave the user disabled upon creation')
            ->addOption('send-mail', null, InputOption::VALUE_NONE, 'Send login token mail to new user')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = trim($input->getArgument('name'));
        $mail = trim($input->getArgument('mail'));

        if (!$name) {
            $output->writeln('<error>name cannot be empty</error>');
            return;
        }
        if (!$mail) {
            $output->writeln('<error>mail cannot be empty</error>');
            return;
        }

        try {
            $this->accountMapper->findUserByMail($mail);
            throw new \InvalidArgumentException(sprintf("%s: account already exists", $mail));
        } catch (EntityNotFoundError $e) {
        }

        $this
            ->accountMapper
            ->getRunner()
            ->insertValues('account')
            ->columns(['mail', 'user_name', 'is_admin', 'is_active'])
            ->values([$mail, $name, $input->getOption('admin'), !$input->getOption('disabled')])
            ->execute()
        ;

        if ($input->getOption('send-mail')) {
            $output->writeln('<error>sending mail is not implemented yet</error>');
        }

        $output->writeln('<info>user created with no password, use the account:password command to set one</info>');
    }
}

