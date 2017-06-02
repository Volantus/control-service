<?php
namespace Volantus\OrientationControlService\Src\Networking;

use Volantus\FlightBase\Src\Client\ClientService;
use Volantus\FlightBase\Src\General\Role\ClientRole;
use Volantus\FlightBase\Src\Server\Messaging\IncomingMessage;

/**
 * Class MessageHandler
 *
 * @package Volantus\OrientationControlService\Src\GyroStatus
 */
class MessageHandler extends ClientService
{
    /**
     * @var int
     */
    protected $clientRole = ClientRole::ORIENTATION_CONTROL_SERVICE;

    /**
     * @param IncomingMessage $incomingMessage
     */
    public function handleMessage(IncomingMessage $incomingMessage)
    {
    }
}