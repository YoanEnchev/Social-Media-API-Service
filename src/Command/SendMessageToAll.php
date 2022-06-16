<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Service\RequestParamsGenerator;

class SendMessageToAll extends Command
{
    private $em;

    public function __construct(EntityManagerInterface $em, RequestParamsGenerator $requestParamsGenerator)
    {
        parent::__construct();
        $this->em = $em;
        $this->reqParamGenerator = $requestParamsGenerator;
    }

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:message:send:all';

    protected function configure(): void
    {
        $this
            // configure an argument
            ->addArgument('message-text', InputArgument::REQUIRED, 'The username of the user.')
            // ...
        ;
    }
    

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Extracting users...');
        $userIds = array_map(fn($obj) => $obj->getId(), $this->em->getRepository(User::class)->findAll());

        
        $output->writeln('Sending request to notification service...');
        
        $client = new Client();
        $client->request(
            'POST', $this->getParameter('app.notificationServiceBaseUrl') . 'api/cli-commands', [
                'form_params' => [
                    'users_ids' => $userIds,
                    'message_text' => $input->getArgument('message-text')
                ],
                'headers' => $this->reqParamGenerator->getBearerHeaderArray()
            ]
        );
        
        $output->writeln('Submited message to all users.');
        return Command::SUCCESS;

        // or return this if some error happened during the execution
        // (it's equivalent to returning int(1))
        // return Command::FAILURE;

        // or return this to indicate incorrect command usage; e.g. invalid options
        // or missing arguments (it's equivalent to returning int(2))
        // return Command::INVALID
    }
}