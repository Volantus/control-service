<?php
namespace Volantus\OrientationControlService\Tests\Orientation;

use Ratchet\Client\WebSocket;
use Volantus\FlightBase\Src\Client\Server;
use Volantus\FlightBase\Src\General\MSP\MSPRequestMessage;
use Volantus\MSPProtocol\Src\Protocol\Request\SetRawReceiverInput;
use Volantus\OrientationControlService\Src\Orientation\ChannelCollection;
use Volantus\OrientationControlService\Src\Orientation\MspAdapter;

/**
 * Class MspAdapterTest
 *
 * @package Volantus\OrientationControlService\Tests\Orientation
 */
class MspAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Server|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $serverA;

    /**
     * @var Server|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $serverB;

    /**
     * @var MspAdapter
     */
    protected $adapter;

    protected function setUp()
    {
        $this->serverA = $this->getMockBuilder(Server::class)->disableOriginalConstructor()->getMock();
        $this->serverB = $this->getMockBuilder(Server::class)->disableOriginalConstructor()->getMock();

        $this->adapter = new MspAdapter([$this->serverA, $this->serverB]);
    }

    public function test_send_rollCorrect()
    {
        $this->assertChannelValue(0, 1100);

        $channelCollection = new ChannelCollection(1000, 1100, 1200, 1300, []);
        $this->adapter->send($channelCollection);
    }

    public function test_send_pitchCorrect()
    {
        $this->assertChannelValue(1, 1200);

        $channelCollection = new ChannelCollection(1000, 1100, 1200, 1300, []);
        $this->adapter->send($channelCollection);
    }

    public function test_send_yawCorrect()
    {
        $this->assertChannelValue(2, 1300);

        $channelCollection = new ChannelCollection(1000, 1100, 1200, 1300, []);
        $this->adapter->send($channelCollection);
    }

    public function test_send_throttleCorrect()
    {
        $this->assertChannelValue(3, 1500);

        $channelCollection = new ChannelCollection(1500, 1100, 1200, 1300, []);
        $this->adapter->send($channelCollection);
    }

    public function test_send_auxCorrect()
    {
        $this->assertChannelValue(5, 1800);

        $channelCollection = new ChannelCollection(1500, 1100, 1200, 1300, [2 => 1800]);
        $this->adapter->send($channelCollection);
    }

    /**
     * @expectedException \Volantus\OrientationControlService\Src\Orientation\AuxChannelOutOfRangeException
     * @expectedExceptionMessage AUX channel 13 is not supported by MSP adapter (1-12)
     */
    public function test_send_auxOutOfRange()
    {
        $channelCollection = new ChannelCollection(1500, 1100, 1200, 1300, [13 => 1800]);
        $this->adapter->send($channelCollection);
    }


    /**
     * @param int $channelId
     * @param int $expectedValue
     */
    private function assertChannelValue(int $channelId, int $expectedValue)
    {
        /** @var Server|\PHPUnit_Framework_MockObject_MockObject $connection */
        foreach ([$this->serverA, $this->serverB] as $server) {
            $server->expects(self::once())
                ->method('sendGenericMessage')
                ->will(self::returnCallback(function ($payload) use ($channelId, $expectedValue) {
                    /** @var MSPRequestMessage $payload */
                    self::assertInstanceOf(MSPRequestMessage::class, $payload);
                    /** @var SetRawReceiverInput $mspRequest */
                    $mspRequest = $payload->getMspRequest();
                    self::assertInstanceOf(SetRawReceiverInput::class, $mspRequest);

                    self::assertEquals($expectedValue, $mspRequest->getChannels()[$channelId]);
                }));
        }

    }
}