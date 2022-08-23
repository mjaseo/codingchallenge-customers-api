<?php

namespace App\Command;

use App\Http\RandomUserApiClient;
use App\Service\RandomUserApiDataManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportDataRandomUserApiCommand extends Command
{
    protected static $defaultName = 'app:import-data-random-user-api';
    protected static $defaultDescription = 'Command to import random users from https://randomuser.me/';

    /** @var RandomUserApiClient */
    private $randomUserApiClient;

    /** @var RandomUserApiDataManager */
    private $dataImportManager;

    public function __construct(RandomUserApiClient $randomUserApiClient, RandomUserApiDataManager $dataImportManager)
    {
        $this->randomUserApiClient = $randomUserApiClient;

        $this->dataImportManager = $dataImportManager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'number_of_results',
                InputArgument::OPTIONAL,
                'Specify number of results.',
                $this->randomUserApiClient->getNoOfResults())
            ->addArgument(
                'nationality',
                InputArgument::OPTIONAL,
                'Specify user nationality',
                $this->randomUserApiClient->getNationality()
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $no_of_results = $input->getArgument('number_of_results');
        $nationality = $input->getArgument('nationality');

        $this->dataImportManager->persistRandomUserApi($no_of_results, $nationality);

        $updatedUsers = $this->dataImportManager->usersUpdated();
        if(count($updatedUsers) > 0) {
            $output->writeln('Updated Users: ' . count($updatedUsers));
            array_map(function($user) use ($output) {
                $output->writeln($user);
            }, $updatedUsers);
        }

        $createdUsers = $this->dataImportManager->usersCreated();
        if(count($createdUsers) > 0) {
            $output->writeln('Created Users: ' . count($createdUsers));
            array_map(function($user) use ($output) {
                $output->writeln($user);
            }, $createdUsers);
        }

        return Command::SUCCESS;
    }
}
