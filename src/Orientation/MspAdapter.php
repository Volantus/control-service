<?php
namespace Volantus\OrientationControlService\Src\Orientation;

use Volantus\FlightBase\Src\General\MSP\MSPRequestMessage;
use Volantus\FlightBase\Src\General\MSP\MspServerBased;
use Volantus\MSPProtocol\Src\Protocol\Request\SetRawReceiverInput;

/**
 * Class MspAdapter
 *
 * @package Volantus\OrientationControlService\Src\Orientation
 */
class MspAdapter extends MspServerBased implements ReceiverProtocolAdapter
{
    /**
     * @param ChannelCollection $channelCollection
     *
     * @return void
     * @throws AuxChannelOutOfRangeException
     */
    public function send(ChannelCollection $channelCollection)
    {
        $mspChannels = [
            0 => $channelCollection->getRoll(),
            1 => $channelCollection->getPitch(),
            2 => $channelCollection->getYaw(),
            3 => $channelCollection->getThrottle()
        ];

        foreach ($channelCollection->getAuxChannels() as $channelId => $value) {
            if ($channelId > 0 && $channelId <= 12) {
                $mspChannels[$channelId + 3] = $value;
            } else {
                throw new AuxChannelOutOfRangeException('AUX channel ' . $channelId . ' is not supported by MSP adapter (1-12)');
            }
        }

        $message = new MSPRequestMessage(0, new SetRawReceiverInput($mspChannels));
        foreach ($this->freeConnections as $connection) {
            $connection->sendGenericMessage($message);
        }
    }
}