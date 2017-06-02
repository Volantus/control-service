<?php
namespace Volantus\OrientationControlService\Src\Orientation;

use Volantus\FlightBase\Src\General\Motor\MotorControlMessage;

/**
 * Class OrientationController
 *
 * @package Volantus\OrientationControlService\Src\Orientation
 */
class OrientationController
{
    const ZERO_LEVEL   = 1000;
    const MIDDLE_LEVEL = 1500;
    const FULL_LEVEL   = 2000;
    const FULL_RANGE   = 1000;
    const HALF_RANGE   = 500;

    const MOTOR_ARM_LEVEL    = 1200;
    const MOTOR_DISARM_LEVEL = 1700;

    /**
     * @var ReceiverProtocolAdapter
     */
    private $adapter;

    /**
     * OrientationController constructor.
     *
     * @param ReceiverProtocolAdapter $adapter
     */
    public function __construct(ReceiverProtocolAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param MotorControlMessage $message
     */
    public function handleControlMessage(MotorControlMessage $message)
    {
        $desiredPosition = $message->getDesiredPosition();

        if ($message->areMotorsStarted()) {
            $aux1 = self::MOTOR_ARM_LEVEL;
            $throttle = ($message->getHorizontalThrottle() * self::FULL_RANGE) + self::ZERO_LEVEL;
            $throttle = round($throttle);
        } else {
            $aux1 = self::MOTOR_DISARM_LEVEL;
            $throttle = self::ZERO_LEVEL;
        }

        $channelCollection = new ChannelCollection(
            $throttle,
            $this->calculateGyroValue($desiredPosition->getRoll()),
            $this->calculateGyroValue($desiredPosition->getPitch()),
            $this->calculateGyroValue($desiredPosition->getYaw()),
            [1 => $aux1]
        );
        $this->adapter->send($channelCollection);
    }

    /**
     * @param float $degrees
     *
     * @return int
     */
    private function calculateGyroValue(float $degrees): int
    {
        $dutyCycle = self::MIDDLE_LEVEL + (self::HALF_RANGE * ($degrees / 180));
        return round($dutyCycle);
    }
}