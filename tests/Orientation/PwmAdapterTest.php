<?php
namespace Volantus\OrientationControlService\Tests\Orientation;
use Volantus\FlightBase\Src\General\Network\Socket;
use Volantus\OrientationControlService\Src\Orientation\ChannelCollection;
use Volantus\OrientationControlService\Src\Orientation\PwmAdapter;
use Volantus\OrientationControlService\Src\Orientation\PwmPinConfig;

/**
 * Class PwmAdapterTest
 *
 * @package Volantus\OrientationControlService\Tests\Orientation
 */
class PwmAdapterTest extends \PHPUnit_Framework_TestCase
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
     * @var PwmAdapter
     */
    private $adapter;

    protected function setUp()
    {
        $this->socket = $this->getMockBuilder(Socket::class)->disableOriginalConstructor()->getMock();
        $this->pinConfig = new PwmPinConfig(1, 2, 3, 4, 5, 6, 7, 8);
        $this->adapter = new PwmAdapter($this->socket, $this->pinConfig);
    }

    public function test_send_throttleSetCorrectly()
    {
        $this->socket->expects(self::at(0))
            ->method('send')
            ->with(self::equalTo(pack('L*', 8, $this->pinConfig->getThrottle(), 1200, 0)));

        $channelCollection = new ChannelCollection(1200, 1000, 1000, 1000, []);
        $this->adapter->send($channelCollection);
    }

    public function test_send_pitchSetCorrectly()
    {
        $this->socket->expects(self::at(2))
            ->method('send')
            ->with(self::equalTo(pack('L*', 8, $this->pinConfig->getPitch(), 1200, 0)));

        $channelCollection = new ChannelCollection(1000, 1000, 1200, 1000, []);
        $this->adapter->send($channelCollection);
    }

    public function test_send_rollSetCorrectly()
    {
        $this->socket->expects(self::at(3))
            ->method('send')
            ->with(self::equalTo(pack('L*', 8, $this->pinConfig->getRoll(), 1200, 0)));

        $channelCollection = new ChannelCollection(1000, 1200, 1000, 1000, []);
        $this->adapter->send($channelCollection);
    }

    public function test_send_yawSetCorrectly()
    {
        $this->socket->expects(self::at(1))
            ->method('send')
            ->with(self::equalTo(pack('L*', 8, $this->pinConfig->getYaw(), 1200, 0)));

        $channelCollection = new ChannelCollection(1000, 1000, 1000, 1200, []);
        $this->adapter->send($channelCollection);
    }

    public function test_send_socketCleaned()
    {
        $this->socket->expects(self::at(4))->method('listen');

        $channelCollection = new ChannelCollection(1000, 1000, 1000, 1000, []);
        $this->adapter->send($channelCollection);
    }

    public function test_send_auxSendCorrectly()
    {
        $this->socket->expects(self::at(4))
            ->method('send')
            ->with(self::equalTo(pack('L*', 8, $this->pinConfig->getAuxChannels()[1], 1100, 0)));

        $this->socket->expects(self::at(5))
            ->method('send')
            ->with(self::equalTo(pack('L*', 8, $this->pinConfig->getAuxChannels()[2], 1200, 0)));

        $this->socket->expects(self::at(6))
            ->method('send')
            ->with(self::equalTo(pack('L*', 8, $this->pinConfig->getAuxChannels()[3], 1300, 0)));

        $this->socket->expects(self::at(7))
            ->method('send')
            ->with(self::equalTo(pack('L*', 8, $this->pinConfig->getAuxChannels()[4], 1400, 0)));

        $channelCollection = new ChannelCollection(1000, 1000, 1000, 1000, [1 => 1100, 2 => 1200, 3 => 1300, 4 => 1400]);
        $this->adapter->send($channelCollection);
    }

    public function test_send_onlyExistingAuxChannelsSet()
    {
        $this->socket->expects(self::at(4))
            ->method('send')
            ->with(self::equalTo(pack('L*', 8, $this->pinConfig->getAuxChannels()[2], 1200, 0)));

        $this->socket->expects(self::at(5))->method('listen');

        $channelCollection = new ChannelCollection(1000, 1000, 1000, 1000, [2 => 1200]);
        $this->adapter->send($channelCollection);
    }

    /**
     * @expectedException \Volantus\OrientationControlService\Src\Orientation\AuxChannelOutOfRangeException
     * @expectedExceptionMessage Aux channel 5 is not supported by PWM protocol adapter (1-4 only)
     */
    public function test_send_auxChannelsOutOfRange()
    {
        $channelCollection = new ChannelCollection(1000, 1000, 1000, 1000, [5 => 1200]);
        $this->adapter->send($channelCollection);
    }

    public function test_send_dutyCycleOnlySetOnChange()
    {
        // +5 for first full set
        // +1 for changed throttle
        $this->socket->expects(self::exactly(6))->method('send');

        $channelCollection = new ChannelCollection(1000, 1100, 1200, 1300, [1 => 1400]);
        $this->adapter->send($channelCollection);
        $channelCollection = new ChannelCollection(2000, 1100, 1200, 1300, [1 => 1400]);
        $this->adapter->send($channelCollection);
    }
}