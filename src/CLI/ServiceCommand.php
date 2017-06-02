<?php
namespace Volantus\OrientationControlService\Src\CLI;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Volantus\OrientationControlService\Src\Networking\ClientController;

/**
 * Class ServiceCommand
 *
 * @package Volantus\OrientationControlService\Src\CLI
 */
class ServiceCommand extends Command
{
    protected function configure()
    {
        $this->setName('service');
        $this->setDescription('Determines and redirects the gyro status');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $controller = new ClientController($output);
        $controller->run();
    }
}