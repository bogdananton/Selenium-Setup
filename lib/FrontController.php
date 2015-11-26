<?php
namespace SeleniumSetup;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FrontController implements FrontControllerInterface
{
    protected $input;
    protected $output;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    public function startServer()
    {
        // TODO: Implement startServer() method.
    }

    public function stopServer()
    {
        // TODO: Implement stopServer() method.
    }
}
