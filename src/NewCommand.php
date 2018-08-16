<?php

namespace Acme;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use GuzzleHttp\ClientInterface;

class NewCommand extends Command 
{
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function configure()
    {
        $this->setName("new")
             ->setDescription("Create a new Laravel application")
             ->addArgument("name", InputArgument::REQUIRED);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        //assert that the folder not exist
        $directory = getcwd() . '/' . $input->getArgument('name');
        $this->asertApplicationDoesNotExist($directory, $output);

        //dawnload nightly version of laravel
        $this->download($this->makeFileName())
             ->extract();
        //extract zip file


        //alert usert that they are ready to go
    }

    private function asertApplicationDoesNotExist($directory, OutputInterface $output)
    {
        if (is_dir($directory))
        {
            $output->writeln('Application already exists');

            exit(1);
        }
    }

    private function makeFileName()
    {
        return getcwd() . './laravel_' . md5(time().uniqid()) . '.zip';
    }

    private function download($zipfile)
    {
        $response = $this->client->get('http://cabinet.laravel.com/latest.zip')->getBody();

        file_put_content($zipfile, $response);

        return $this;
    }
}

