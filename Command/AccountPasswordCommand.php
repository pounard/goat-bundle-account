<?php

namespace Goat\AccountBundle\Command;

use Goat\AccountBundle\Mapper\AccountMapper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class AccountPasswordCommand extends Command
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
            ->setName('account:password')
            ->setDescription('Change account password')
            ->addArgument('mail', InputArgument::REQUIRED, "User's mail for which to set password")
        ;
    }

    /**app
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mail = $input->getArgument('mail');
        $account = $this->accountMapper->findUserByMail($mail);

        $helper = $this->getHelper('question');

        $passwordValidator = function ($value) {
            if (trim($value) == '') {
                throw new \Exception('The password can not be empty');
            }
            return $value;
        };

        $question = new Question('Please enter the new password: ');
        $question->setValidator($passwordValidator);
        $question->setHidden(true);
        $question->setMaxAttempts(3);
        $password = $helper->ask($input, $output, $question);

        $question = new Question('Please confirm the password: ');
        $question->setValidator($passwordValidator);
        $question->setHidden(true);
        $question->setMaxAttempts(3);
        $confirm = $helper->ask($input, $output, $question);

        if ($password !== $confirm) {
            $output->writeln('<error>passwords do not match</error>');
            return;
        }

        $this->accountMapper->updatePassword($account, $password);

        $output->writeln('<info>password successfully set</info>');
    }
}
