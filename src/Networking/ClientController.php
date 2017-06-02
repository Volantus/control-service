<?php
namespace Volantus\OrientationControlService\Src\Networking;

use Symfony\Component\Console\Output\OutputInterface;
use Volantus\FlightBase\Src\Client\ClientService;
use Volantus\FlightBase\Src\Server\Messaging\MessageService;

/**
 * Class ClientController
 *
 * @package Volantus\OrientationControlService\Src\Networking
 */
class ClientController extends \Volantus\FlightBase\Src\Client\ClientController
{
    /**
     * ClientController constructor.
     *
     * @param OutputInterface    $output
     * @param ClientService|null $service
     */
    public function __construct(OutputInterface $output, ClientService $service = null)
    {
        parent::__construct($output, $service ?: new MessageHandler($output, new MessageService()));

        $this->connectToRelayServer();
    }
}