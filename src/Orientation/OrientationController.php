<?php
namespace Volantus\OrientationControlService\Src\Orientation;

use Volantus\FlightBase\Src\General\Motor\MotorControlMessage;

/**
 * Interface OrientationController
 *
 * @package Volantus\OrientationControlService\Src\Orientation
 */
interface OrientationController
{
    /**
     * @param MotorControlMessage $message
     */
    public function handleControlMessage(MotorControlMessage $message);
}