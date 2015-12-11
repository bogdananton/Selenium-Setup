<?php
namespace SeleniumSetup\Command\System;

use GuzzleHttp\Client;
use GuzzleHttp\Event\ProgressEvent;
use SeleniumSetup\Environment;
use SeleniumSetup\SeleniumSetup;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DownloadCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('download')
            ->setDescription('')
            ->addArgument('from', InputArgument::REQUIRED, 'The URL.')
            ->addArgument('to', InputArgument::REQUIRED, 'The location on disk.');
    }

    /**
     * Execute the command.
     * @todo put try catch http://stackoverflow.com/questions/16939794/copy-remote-file-using-guzzle
     *
     * @param  \Symfony\Component\Console\Input\InputInterface $input
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $client = new Client();
        $client->setDefaultOption('verify', SeleniumSetup::$APP_ROOT_PATH . DIRECTORY_SEPARATOR . SeleniumSetup::SSL_CERT_FILENAME);
        $request = $client->createRequest('GET', $input->getArgument('from'), ['save_to'=> $input->getArgument('to')]);

        $computeRemainingSize = function(ProgressEvent $e) {
            if ($e->downloaded <= 0) {
                return 0;
            }
            $remainingSize = $e->downloadSize - $e->downloaded;
            if ($remainingSize > 0) {
                return round($e->downloaded / $e->downloadSize, 2) * 100;
            } else {
                return 100;
            }
        };

        // $progress = new ProgressBar($output, 5);
        // $progress->start();

        $request->getEmitter()->on('progress', function (ProgressEvent $e) use ($computeRemainingSize, $output) {
            
            $output->write(
                sprintf("Downloaded %s%%\r", $computeRemainingSize($e))
            );
            
            //$a = $computeRemainingSize($e);
            //if ($a == 100) {
            //    $progress->finish();
            //} else {
            //    if ($a % 10 == 0) {
            //        $progress->advance();
            //    }
            //}
        });

        $client->send($request);
    }

    /**
     * Find the correct executable to run depending on the OS.
     *
     * @param Environment $env
     * @return string
     */
    protected function executable(Environment $env)
    {

    }
}