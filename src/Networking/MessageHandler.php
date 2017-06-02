<?php
namespace Volantus\OrientationControlService\Src\Networking;

use Symfony\Component\Console\Output\OutputInterface;
use Volantus\FlightBase\Src\Client\ClientService;
use Volantus\FlightBase\Src\General\Motor\IncomingMotorControlMessage;
use Volantus\FlightBase\Src\General\Role\ClientRole;
use Volantus\FlightBase\Src\Server\Messaging\IncomingMessage;
use Volantus\FlightBase\Src\Server\Messaging\MessageService;
use Volantus\OrientationControlService\Src\Orientation\MspAdapter;
use Volantus\OrientationControlService\Src\Orientation\OrientationController;
use Volantus\OrientationControlService\Src\Orientation\PwmAdapter;

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
     * @var OrientationController
     */
    private $orientationController;

    public function __construct(OutputInterface $output, MessageService $messageService, OrientationController $orientationController = null)
    {
        parent::__construct($output, $messageService);
        $this->orientationController = $orientationController;

        if ($this->orientationController == null) {
            if (getenv('RECEIVER_PROTOCOL') === 'MSP') {
                $this->orientationController = new OrientationController(new MspAdapter());
            } else {
                $this->orientationController = new OrientationController(new PwmAdapter());
            }
        }
    }

    /**
     * @param IncomingMessage $incomingMessage
     */
    public function handleMessage(IncomingMessage $incomingMessage)
    {
        if ($incomingMessage instanceof IncomingMotorControlMessage) {
            $this->writeInfoLine('MessageHandler', 'Received motor control message => setting PWM signals');
            $this->orientationController->handleControlMessage($incomingMessage->getMotorControl());
        }
    }
}