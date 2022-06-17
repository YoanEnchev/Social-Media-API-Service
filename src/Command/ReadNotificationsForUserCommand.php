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

class ReadNotificationsForUserCommand extends Command
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
    protected static $defaultName = 'app:message:readall';

    protected function configure(): void
    {
        $this
            // configure an argument
            ->addArgument('user-id', InputArgument::REQUIRED, "Id of the user that'll receive notification.")
            // ...
        ;
    }
    

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $userId = $input->getArgument('user-id');

        if($this->em->getRepository(User::class)->find($userId) === null) {
            $output->writeln('No user with such id exists.');
            return Command::INVALID;
        }

        $output->writeln('Sending request to notification service...');
        
        try {
            $client = new Client();
            $client->request(
                'POST', $this->servicesUrls->getNotificationBaseUrl() . 'api/cli/mark-as-read', [
                    'form_params' => [
                        'user_id' => $userId,
                    ],
                    'headers' => $this->reqParamGenerator->getBearerHeaderArray()
                ]
            );
        }
        catch(RequestException $ex) {

            $output->writeln('Failed marking as read notifications.');
            $output->writeln(ServiceResponse::processException($ex)['message']);

            return Command::FAILURE;
        }
        
        $output->writeln('Read messages successfully.');
        
        return Command::SUCCESS;
    }
}