<?php
namespace Volantus\OrientationControlService\Tests\Orientation;

use Volantus\FlightBase\Src\General\GyroStatus\GyroStatus;
use Volantus\FlightBase\Src\General\Motor\MotorControlMessage;
use Volantus\FlightBase\Src\General\Network\Socket;
use Volantus\OrientationControlService\Src\Orientation\PwmController;
use Volantus\OrientationControlService\Src\Orientation\PwmPinConfig;

/**
 * Class PwmControllerTest
 *
 * @package Volantus\OrientationControlService\Tests\Orientation
 */
class PwmControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Socket|\PHPUnit_Framework_MockObject_MockObject
     */
    private $socket;

    /**
     * @var PwmPinConfig
     */
    private $pinConfig;

    /**
     * @var PwmController
     */
    private $controller;

    protected function setUp()
    {
        $this->socket = $this->getMockBuilder(Socket::class)->disableOriginalConstructor()->getMock();
        $this->pinConfig = new PwmPinConfig(1, 2, 3, 4, 5, 6, 7, 8);
        $this->controller = new PwmController($this->socket, $this->pinConfig);
    }

    public function test_handleControlMessage_motorsNotStared_disarmSignalSend()
    {
        $this->socket->expects(self::at(0))
            ->method('send')
            ->with(self::equalTo(pack('L*', 8, $this->pinConfig->getAux1(), PwmController::MOTOR_DISARM_LEVEL, 0)));

        $message = new MotorControlMessage(new GyroStatus(1, 2, 3), 0.5, 0, false);
        $this->controller->handleControlMessage($message);
    }

    public function test_handleControlMessage_motorsNotStared_throttleSetToZeroLevel()
    {
        $this->socket->expects(self::at(1))
            ->method('send')
            ->with(self::equalTo(pack('L*', 8, $this->pinConfig->getThrottle(), PwmController::ZERO_LEVEL, 0)));

        $message = new MotorControlMessage(new GyroStatus(1, 2, 3), 0.5, 0, false);
        $this->controller->handleControlMessage($message);
    }

    public function test_handleControlMessage_motorsStared_motorsArmed()
    {
        $this->socket->expects(self::at(0))
            ->method('send')
            ->with(self::equalTo(pack('L*', 8, $this->pinConfig->getAux1(), PwmController::MOTOR_ARM_LEVEL, 0)));

        $message = new MotorControlMessage(new GyroStatus(1, 2, 3), 0.5, 0, true);
        $this->controller->handleControlMessage($message);
    }

    public function test_handleControlMessage_motorsStared_throttleCorrect()
    {
        $this->socket->expects(self::at(1))
            ->method('send')
            ->with(self::equalTo(pack('L*', 8, $this->pinConfig->getThrottle(), 1756, 0)));

        $message = new MotorControlMessage(new GyroStatus(1, 2, 3), 0.75555, 0, true);
        $this->controller->handleControlMessage($message);
    }

    public function test_handleControlMessage_yawAppliedCorrectly()
    {
        $this->socket->expects(self::at(2))
            ->method('send')
            ->with(self::equalTo(pack('L*', 8, $this->pinConfig->getYaw(), 1417, 0)));

        $message = new MotorControlMessage(new GyroStatus(-30, 2, 3), 0.5, 0, true);
        $this->controller->handleControlMessage($message);
    }

    public function test_handleControlMessage_pitchAppliedCorrectly()
    {
        $this->socket->expects(self::at(3))
            ->method('send')
            ->with(self::equalTo(pack('L*', 8, $this->pinConfig->getPitch(), 1500, 0)));

        $message = new MotorControlMessage(new GyroStatus(90, 45, 0), 0.5, 0, true);
        $this->controller->handleControlMessage($message);
    }

    public function test_handleControlMessage_rollAppliedCorrectly()
    {
        $this->socket->expects(self::at(4))
            ->method('send')
            ->with(self::equalTo(pack('L*', 8, $this->pinConfig->getRoll(), 1625, 0)));

        $message = new MotorControlMessage(new GyroStatus(1, 45, 3), 0.5, 0, true);
        $this->controller->handleControlMessage($message);
    }

    public function test_handleControlMessage_socketCleaned()
    {
        $this->socket->expects(self::at(5))->method('listen');

        $message = new MotorControlMessage(new GyroStatus(1, 2, 3), 0.5, 0, true);
        $this->controller->handleControlMessage($message);
    }

    public function test_handleControlMessage_dutyCycleOnlySetOnChange()
    {
        // +5 for first full set
        // +1 for changed throttle
        $this->socket->expects(self::exactly(6))->method('send');

        $message = new MotorControlMessage(new GyroStatus(10, 20, 30), 0.5, 0, true);
        $this->controller->handleControlMessage($message);
        $message = new MotorControlMessage(new GyroStatus(10, 20, 30), 0.6, 0, true);
        $this->controller->handleControlMessage($message);
    }
}