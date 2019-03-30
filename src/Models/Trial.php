<?php
namespace csm2020\PatientApp\Models;

class Trial
{
    private $trialId;
    private $regimeId;
    private $type;
    private $duration;
    private $intensitySpeed;
    private $intensitySlope;
    private $completedTime;

    private function __construct($trialId, $regimeId, $type, $duration, $intensitySpeed, $intensitySlope,
                                 $completedTime)
    {
        $this->trialId = $trialId;
        $this->regimeId = $regimeId;
        $this->type = $type;
        $this->duration = $duration;
        $this->intensitySpeed = $intensitySpeed;
        $this->intensitySlope = $intensitySlope;
        $this->completedTime = $completedTime;
    }

    private function __clone() {}

    public static function factory(array $ingredients)
    {
        $trial = new Trial(
            $ingredients['trial_id'],
            $ingredients['regime_id'],
            $ingredients['type'],
            $ingredients['duration'],
            $ingredients['intensity_speed'],
            $ingredients['intensity_slope'],
            $ingredients['completed_time']
        );
        return $trial;
    }
}
