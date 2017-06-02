<?php
namespace Volantus\OrientationControlService\Src\Orientation;

use Volantus\FlightBase\Src\General\Motor\MotorControlMessage;
use Volantus\FlightBase\Src\General\Network\Socket;

/**
 * Class PwmController
 *
 * @package Volantus\OrientationControlService\Src\Orientation
 */
class PwmController implements OrientationController
{
    const ZERO_LEVEL   = 1000;
    const MIDDLE_LEVEL = 1500;
    const FULL_LEVEL   = 2000;
    const FULL_RANGE   = 1000;
    const HALF_RANGE   = 500;

    const MOTOR_ARM_LEVEL    = 1200;
    const MOTOR_DISARM_LEVEL = 1700;

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
     * PwmController constructor.
     *
     * @param Socket            $socket
     * @param PwmPinConfig|null $pinConfig
     */
    public function __construct(Socket $socket = null, PwmPinConfig $pinConfig = null)
    {
        $this->socket = $socket ?: new Socket('127.0.0.1', 8888);
        $this->pinConfig = $pinConfig ?: new PwmPinConfig();

        $this->currentDutyCycles[$this->pinConfig->getThrottle()] = -1;
        $this->currentDutyCycles[$this->pinConfig->getRoll()]     = -1;
        $this->currentDutyCycles[$this->pinConfig->getPitch()]    = -1;
        $this->currentDutyCycles[$this->pinConfig->getYaw()]      = -1;
        $this->currentDutyCycles[$this->pinConfig->getAux1()]     = -1;
        $this->currentDutyCycles[$this->pinConfig->getAux2()]     = -1;
        $this->currentDutyCycles[$this->pinConfig->getAux3()]     = -1;
        $this->currentDutyCycles[$this->pinConfig->getAux4()]     = -1;
    }

    /**
     * @param MotorControlMessage $message
     */
    public function handleControlMessage(MotorControlMessage $message)
    {
        $desiredPosition = $message->getDesiredPosition();

        if ($message->areMotorsStarted()) {
            $this->setDutyCycle($this->pinConfig->getAux1(), self::MOTOR_ARM_LEVEL);

            $throttle = ($message->getHorizontalThrottle() * self::FULL_RANGE) + self::ZERO_LEVEL;
            $throttle = round($throttle);
            $this->setDutyCycle($this->pinConfig->getThrottle(), $throttle);
        } else {
            $this->setDutyCycle($this->pinConfig->getAux1(), self::MOTOR_DISARM_LEVEL);
            $this->setDutyCycle($this->pinConfig->getThrottle(), self::ZERO_LEVEL);
        }

        $this->setGyroDutyCycle($this->pinConfig->getYaw(), $desiredPosition->getYaw());
        $this->setGyroDutyCycle($this->pinConfig->getPitch(), $desiredPosition->getPitch());
        $this->setGyroDutyCycle($this->pinConfig->getRoll(), $desiredPosition->getRoll());

        $this->socket->listen();
    }

    /**
     * @param int   $pin
     * @param float $gyroStatus
     */
    private function setGyroDutyCycle(int $pin, float $gyroStatus)
    {
        $dutyCycle = self::MIDDLE_LEVEL + (self::HALF_RANGE * ($gyroStatus / 180));
        $dutyCycle = round($dutyCycle);

        $this->setDutyCycle($pin, $dutyCycle);
    }

    /**
     * @param int $pin
     * @param int $dutyCycle
     */
    private function setDutyCycle(int $pin, int $dutyCycle)
    {
        if ($this->currentDutyCycles[$pin] != $dutyCycle) {
            $message = pack('L*', 8, $pin, $dutyCycle, 0);
            $this->socket->send($message);

            $this->currentDutyCycles[$pin] = $dutyCycle;
        }
    }
}