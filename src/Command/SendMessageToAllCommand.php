<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Service\RequestParamsGenerator;
use App\Service\ServiceResponse;
use App\Service\ServicesUrls;
use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\RequestException;

class SendMessageToAllCommand extends Command
{
    private $em;
    private $servicesUrls;

    public function __construct(EntityManagerInterface $em, RequestParamsGenerator $requestParamsGenerator, ServicesUrls $servicesUrls)
    {
        parent::__construct();
        $this->em = $em;
        $this->reqParamGenerator = $requestParamsGenerator;
        $this->servicesUrls = $servicesUrls;
    }

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:message:send:all';

    protected function configure(): void
    {
        $this
            // configure an argument
            ->addArgument('message-text', InputArgument::REQUIRED, "The text of the notification that'll be sent to all users.")
            // ...
        ;
    }
    

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Extracting users...');
        $output->writeln('Sending request to notification service...');
        
        try {
            $client = new Client();
            $client->request(
                'POST', $this->servicesUrls->getNotificationBaseUrl() . 'api/cli/send-system-message', [
                    'form_params' => [
                        'users_ids' => array_map(fn($obj) => $obj->getId(), $this->em->getRepository(User::class)->findAll()),
                        'message_text' => $input->getArgument('message-text')
                    ],
                    'headers' => $this->reqParamGenerator->getBearerHeaderArray()
                ]
            );
        }
        catch(RequestException $ex) {

            $output->writeln('Failed sending message.');
            $output->writeln(ServiceResponse::processException($ex)['message']);

            return Command::FAILURE;
        }
        
        $output->writeln('Sent message to all users successfully.');
        
        return Command::SUCCESS;
    }
}