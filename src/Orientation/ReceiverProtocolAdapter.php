<?php
namespace Volantus\OrientationControlService\Src\Orientation;

/**
 * Interface ReceiverProtocolAdapter
 *
 * @package Volantus\OrientationControlService\Src\Orientation
 */
interface ReceiverProtocolAdapter
{
    /**
     * @param ChannelCollection $channelCollection
     *
     * @return void
     */
    public function send(ChannelCollection $channelCollection);
}