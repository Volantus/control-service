<?php
namespace Volantus\OrientationControlService\Tests\Networking;

use Volantus\FlightBase\Src\Client\ClientService;
use Volantus\FlightBase\Src\General\Role\ClientRole;
use Volantus\FlightBase\Tests\Client\ClientServiceTest;
use Volantus\OrientationControlService\Src\Networking\MessageHandler;

/**
 * Class MessageHandlerTest
 *
 * @package Volantus\OrientationControlService\Tests\Networking
 */
class MessageHandlerTest extends ClientServiceTest
{
    /**
     * @return ClientService
     */
    protected function createService(): ClientService
    {
        return new MessageHandler($this->dummyOutput, $this->messageService);
    }

    protected function getExpectedClientRole(): int
    {
        return ClientRole::ORIENTATION_CONTROL_SERVICE;
    }
}