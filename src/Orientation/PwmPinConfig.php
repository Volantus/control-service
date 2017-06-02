<?php
namespace Volantus\OrientationControlService\Src\Orientation;

/**
 * Class PwmPinConfig
 *
 * @package Volantus\OrientationControlService\Src\Orientation
 */
class PwmPinConfig
{
    /**
     * @var int
     */
    private $throttle;

    /**
     * @var int
     */
    private $pitch;

    /**
     * @var int
     */
    private $roll;

    /**
     * @var int
     */
    private $yaw;

    /**
     * @var int
     */
    private $aux1;

    /**
     * @var int
     */
    private $aux2;

    /**
     * @var int
     */
    private $aux3;

    /**
     * @var int
     */
    private $aux4;

    /**
     * PwmPinConfig constructor.
     *
     * @param int $throttle
     * @param int $pitch
     * @param int $roll
     * @param int $yaw
     * @param int $aux1
     * @param int $aux2
     * @param int $aux3
     * @param int $aux4
     */
    public function __construct(int $throttle = null, int $pitch = null, int $roll = null, int $yaw = null, int $aux1 = null, int $aux2 = null, int $aux3 = null, int $aux4 = null)
    {
        $this->throttle = $throttle ?: $this->fetchFromEnv('THROTTLE');
        $this->pitch = $pitch ?: $this->fetchFromEnv('PITCH');
        $this->roll = $roll ?: $this->fetchFromEnv('ROLL');
        $this->yaw = $yaw ?: $this->fetchFromEnv('YAW');
        $this->aux1 = $aux1 ?: $this->fetchFromEnv('AUX1');
        $this->aux2 = $aux2 ?: $this->fetchFromEnv('AUX2');
        $this->aux3 = $aux3 ?: $this->fetchFromEnv('AUX3');
        $this->aux4 = $aux4 ?: $this->fetchFromEnv('AUX4');
    }

    /**
     * @param string $key
     *
     * @return int
     */
    private function fetchFromEnv(string $key): int
    {
        $pin = getenv($key . '_PIN');

        if ($pin === false) {
            throw new \RuntimeException('Pin ' . $key . ' needs to be configured.');
        }

        return $pin;
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
    public function getPitch(): int
    {
        return $this->pitch;
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
    public function getYaw(): int
    {
        return $this->yaw;
    }

    /**
     * @return int
     */
    public function getAux1(): int
    {
        return $this->aux1;
    }

    /**
     * @return int
     */
    public function getAux2(): int
    {
        return $this->aux2;
    }

    /**
     * @return int
     */
    public function getAux3(): int
    {
        return $this->aux3;
    }

    /**
     * @return int
     */
    public function getAux4(): int
    {
        return $this->aux4;
    }
}