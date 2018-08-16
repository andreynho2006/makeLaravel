<?php

namespace Acme;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use GuzzleHttp\ClientInterface;
use ZipArchive;

class NewCommand extends Command 
{
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;

        parent::__construct();
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

        $output->writeln('<comment>Crafting application....</comment>');

        $this->asertApplicationDoesNotExist($directory, $output);

        //dawnload nightly version of laravel
        //extract zip file
        $this->download($zipFile = $this->makeFileName())
             ->extract($zipFile, $directory)
             ->cleanUp($zipFile);
        
        //alert usert that they are ready to go
        $output->writeln('<comment>Application ready</comment>');
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

        file_put_contents($zipfile, $response);

        return $this;
    }

    private function extract($zipFile, $directory)
    {
        $archive = new ZipArchive;

        $archive->open($zipFile);
        $archive->extractTo($directory);
        $archive->close();

        return $this;
    }

    private function cleanUp($zipFile)
    {
        @chmod($zipFile, 0077);
        @unlink($zipFile);

        return $this;
    }
}

