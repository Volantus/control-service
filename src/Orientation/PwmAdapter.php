<?php
namespace Volantus\OrientationControlService\Src\Orientation;

use Volantus\FlightBase\Src\General\Network\Socket;

/**
 * Class PwmAdapter
 *
 * @package Volantus\OrientationControlService\Src\Orientation
 */
class PwmAdapter implements ReceiverProtocolAdapter
{
    /**
     * @var Socket
     */
    private $socket;

    /**
     * @var PwmPinConfig
     */
    private $pinConfig;

    /**
     * @var array
     */
    private $currentDutyCycles = [];

    /**
     * OrientationController constructor.
     *
     * @param Socket            $socket
     * @param PwmPinConfig|null $pinConfig
     */
    public function __construct(Socket $socket = null, PwmPinConfig $pinConfig = null)
    {
        $this->socket = $socket ?: new Socket('127.0.0.1', 8888);
        $this->pinConfig = $pinConfig ?: new PwmPinConfig();
        $auxPins = $this->pinConfig->getAuxChannels();

        $this->currentDutyCycles[$this->pinConfig->getThrottle()] = -1;
        $this->currentDutyCycles[$this->pinConfig->getRoll()]     = -1;
        $this->currentDutyCycles[$this->pinConfig->getPitch()]    = -1;
        $this->currentDutyCycles[$this->pinConfig->getYaw()]      = -1;
        $this->currentDutyCycles[$auxPins[1]]                     = -1;
        $this->currentDutyCycles[$auxPins[2]]                     = -1;
        $this->currentDutyCycles[$auxPins[3]]                     = -1;
        $this->currentDutyCycles[$auxPins[4]]                     = -1;
    }

    /**
     * @param ChannelCollection $channelCollection
     *
     * @return void
     * @throws AuxChannelOutOfRangeException
     */
    public function send(ChannelCollection $channelCollection): void
    {
        $this->setDutyCycle($this->pinConfig->getThrottle(), $channelCollection->getThrottle());
        $this->setDutyCycle($this->pinConfig->getYaw(), $channelCollection->getYaw());
        $this->setDutyCycle($this->pinConfig->getPitch(), $channelCollection->getPitch());
        $this->setDutyCycle($this->pinConfig->getRoll(), $channelCollection->getRoll());

        $auxChannels = $channelCollection->getAuxChannels();
        $auxPins = $this->pinConfig->getAuxChannels();

        foreach ($auxChannels as $channelId => $value) {
            if ($channelId > 0 && $channelId <= 4) {
                $this->setDutyCycle($auxPins[$channelId], $value);
            } else {
                throw new AuxChannelOutOfRangeException('Aux channel ' . $channelId . ' is not supported by PWM protocol adapter (1-4 only)');
            }
        }
    }

    /**
     * @param int $pin
     * @param int $dutyCycle
     */
    private function setDutyCycle(int $pin, int $dutyCycle): void
    {
        if ($this->currentDutyCycles[$pin] != $dutyCycle) {
            $message = pack('L*', 8, $pin, $dutyCycle, 0);
            $this->socket->send($message);
            $this->socket->listen();

            $this->currentDutyCycles[$pin] = $dutyCycle;
        }
    }
}