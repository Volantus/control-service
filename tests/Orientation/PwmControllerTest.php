<?php
namespace Volantus\OrientationControlService\Tests\Orientation;

use Volantus\FlightBase\Src\General\GyroStatus\GyroStatus;
use Volantus\FlightBase\Src\General\Motor\MotorControlMessage;
use Volantus\FlightBase\Src\General\Network\Socket;
use Volantus\OrientationControlService\Src\Orientation\ChannelCollection;
use Volantus\OrientationControlService\Src\Orientation\OrientationController;
use Volantus\OrientationControlService\Src\Orientation\PwmController;
use Volantus\OrientationControlService\Src\Orientation\PwmPinConfig;
use Volantus\OrientationControlService\Src\Orientation\ReceiverProtocolAdapter;

/**
 * Class PwmControllerTest
 *
 * @package Volantus\OrientationControlService\Tests\Orientation
 */
class OrientationControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ReceiverProtocolAdapter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $protocolAdapter;

    /**
     * @var OrientationController
     */
    private $controller;

    protected function setUp()
    {
        $this->protocolAdapter = $this->getMockBuilder(ReceiverProtocolAdapter::class)->getMock();
        $this->controller = new OrientationController($this->protocolAdapter);
    }

    public function test_handleControlMessage_motorsNotStared_disarmSignalCorrect()
    {
        $this->protocolAdapter->expects(self::once())
            ->method('send')
            ->will(self::returnCallback(function (ChannelCollection $channelCollection) {
                self::assertEquals([1 => OrientationController::MOTOR_DISARM_LEVEL], $channelCollection->getAuxChannels());
            }));

        $message = new MotorControlMessage(new GyroStatus(1, 2, 3), 0.5, 0, false);
        $this->controller->handleControlMessage($message);
    }

    public function test_handleControlMessage_motorsNotStared_throttleSetToZeroLevel()
    {
        $this->protocolAdapter->expects(self::once())
            ->method('send')
            ->will(self::returnCallback(function (ChannelCollection $channelCollection) {
                self::assertEquals(OrientationController::ZERO_LEVEL, $channelCollection->getThrottle());
            }));

        $message = new MotorControlMessage(new GyroStatus(1, 2, 3), 0.5, 0, false);
        $this->controller->handleControlMessage($message);
    }

    public function test_handleControlMessage_motorsStared_motorsArmSignalCorrect()
    {
        $this->protocolAdapter->expects(self::once())
            ->method('send')
            ->will(self::returnCallback(function (ChannelCollection $channelCollection) {
                self::assertEquals([1 => OrientationController::MOTOR_ARM_LEVEL], $channelCollection->getAuxChannels());
            }));

        $message = new MotorControlMessage(new GyroStatus(1, 2, 3), 0.5, 0, true);
        $this->controller->handleControlMessage($message);
    }

    public function test_handleControlMessage_motorsStared_throttleCorrect()
    {
        $this->protocolAdapter->expects(self::once())
            ->method('send')
            ->will(self::returnCallback(function (ChannelCollection $channelCollection) {
                self::assertEquals(1756, $channelCollection->getThrottle());
            }));

        $message = new MotorControlMessage(new GyroStatus(1, 2, 3), 0.75555, 0, true);
        $this->controller->handleControlMessage($message);
    }

    public function test_handleControlMessage_yawCalculatedCorrectly()
    {
        $this->protocolAdapter->expects(self::once())
            ->method('send')
            ->will(self::returnCallback(function (ChannelCollection $channelCollection) {
                self::assertEquals(1417, $channelCollection->getYaw());
            }));

        $message = new MotorControlMessage(new GyroStatus(-30, 2, 3), 0.5, 0, true);
        $this->controller->handleControlMessage($message);
    }

    public function test_handleControlMessage_pitchCalculatedCorrectly()
    {
        $this->protocolAdapter->expects(self::once())
            ->method('send')
            ->will(self::returnCallback(function (ChannelCollection $channelCollection) {
                self::assertEquals(1500, $channelCollection->getPitch());
            }));

        $message = new MotorControlMessage(new GyroStatus(90, 45, 0), 0.5, 0, true);
        $this->controller->handleControlMessage($message);
    }

    public function test_handleControlMessage_rollCalculatedCorrectly()
    {
        $this->protocolAdapter->expects(self::once())
            ->method('send')
            ->will(self::returnCallback(function (ChannelCollection $channelCollection) {
                self::assertEquals(1625, $channelCollection->getRoll());
            }));

        $message = new MotorControlMessage(new GyroStatus(1, 45, 3), 0.5, 0, true);
        $this->controller->handleControlMessage($message);
    }
}