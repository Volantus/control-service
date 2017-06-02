<?php
namespace Volantus\OrientationControlService\Src\Orientation;

/**
 * Class ChannelCollection
 *
 * @package Volantus\OrientationControlService\Src\Orientation
 */
class ChannelCollection
{
    /**
     * @var int
     */
    private $throttle;

    /**
     * @var int
     */
    private $roll;

    /**
     * @var int
     */
    private $pitch;

    /**
     * @var int
     */
    private $yaw;

    /**
     * @var array
     */
    private $auxChannels;

    /**
     * ChannelCollection constructor.
     *
     * @param int   $throttle
     * @param int   $roll
     * @param int   $pitch
     * @param int   $yaw
     * @param array $auxChannels
     */
    public function __construct(int $throttle, int $roll, int $pitch, int $yaw, array $auxChannels = [])
    {
        $this->throttle = $throttle;
        $this->roll = $roll;
        $this->pitch = $pitch;
        $this->yaw = $yaw;
        $this->auxChannels = $auxChannels;
    }

    /**
     * @return int
     */
    public function getThrottle(): int
    {
        return $this->throttle;
    }

    /**
     * @return int
     */
    public function getRoll(): int
    {
        return $this->roll;
    }

    /**
     * @return int
     */
    public function getPitch(): int
    {
        return $this->pitch;
    }

    /**
     * @return int
     */
    public function getYaw(): int
    {
        return $this->yaw;
    }

    /**
     * @return array
     */
    public function getAuxChannels(): array
    {
        return $this->auxChannels;
    }
}