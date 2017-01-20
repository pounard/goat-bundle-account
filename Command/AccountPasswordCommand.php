<?php

namespace Goat\AccountBundle\Command;

use Goat\AccountBundle\Mapper\AccountMapperAwareTrait;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AccountPasswordCommand extends ContainerAwareCommand
{
    use AccountMapperAwareTrait;

    /**
     * Hides trait constructor
     *
     * @param string $name
     */
    public function __construct($name = null)
    {
        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);

        $this->setAccountMapper($container->get('goat_account.account_mapper'));
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

