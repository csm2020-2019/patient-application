<?php
namespace csm2020\PatientApp\Models;

use csm2020\PatientApp\Database\Database;
use PDO;
use PDOException;

/**
 * Class Trial
 * @package csm2020\PatientApp\Models
 * @author Oliver Earl <ole4@aber.ac.uk>
 */
class Trial
{
    /**
     * @var
     */
    private $trialId;
    /**
     * @var
     */
    private $regimeId;
    /**
     * @var
     */
    private $type;
    /**
     * @var
     */
    private $duration;
    /**
     * @var
     */
    private $intensitySpeed;
    /**
     * @var
     */
    private $intensitySlope;

    /**
     * Trial constructor.
     * @param $trialId
     * @param $regimeId
     * @param $type
     * @param $duration
     * @param $intensitySpeed
     * @param $intensitySlope
     */
    private function __construct($trialId, $regimeId, $type, $duration, $intensitySpeed, $intensitySlope)
    {
        $this->trialId = $trialId;
        $this->regimeId = $regimeId;
        $this->type = $type;
        $this->duration = $duration;
        $this->intensitySpeed = $intensitySpeed;
        $this->intensitySlope = $intensitySlope;}

    /**
     *
     */
    private function __clone() {}

    /**
     * @param array $ingredients
     * @return Trial
     */
    public static function factory(array $ingredients)
    {
        $trial = new Trial(
            $ingredients['trial_id'],
            $ingredients['regime_id'],
            $ingredients['type'],
            $ingredients['duration'],
            $ingredients['intensity_speed'],
            $ingredients['intensity_slope']
        );
        return $trial;
    }

    /**
     * @return array
     */
    public function toAssoc()
    {
        return [
            'trial_id'          => $this->getTrialId(),
            'regime_id'         => $this->getRegimeId(),
            'type'              => $this->getType(),
            'duration'          => $this->getDuration(),
            'intensity_speed'   => $this->getIntensitySpeed(),
            'intensity_slope'   => $this->getIntensitySlope(),
        ];
    }

    /**
     * Get Trials By Regime ID Method
     * @param $rid
     * @return array|null
     *
     * Returns all trials containing a foreign key to the specified regime ID. If there aren't any, then it will return
     * an empty array in any case, otherwise an associative array containing trial objects. Null on fail.
     */
    public static function getTrialsByRegimeId($rid)
    {
        $rid = Database::sanitise($rid);
        if (!$rid) {
            return null;
        }

        $db = Database::getDatabase();
        try {
            $stmt = $db->prepare('SELECT * FROM exercise_trials WHERE regime_id = :rid');
            $stmt->bindParam(':rid', $rid);
            $stmt->execute();

            $trials = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$trials) {
                return [];
            }
            $builtTrials = [];
            foreach($trials as $trial) {
                array_push($builtTrials, self::factory($trial));
            }
            return $builtTrials;
        } catch (PDOException $exception) {
            return null;
        }
    }

    /**
     * @return mixed
     */
    public function getTrialId()
    {
        return $this->trialId;
    }

    /**
     * @param mixed $trialId
     */
    public function setTrialId($trialId)
    {
        $this->trialId = $trialId;
    }

    /**
     * @return mixed
     */
    public function getRegimeId()
    {
        return $this->regimeId;
    }

    /**
     * @param mixed $regimeId
     */
    public function setRegimeId($regimeId)
    {
        $this->regimeId = $regimeId;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param mixed $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * @return mixed
     */
    public function getIntensitySpeed()
    {
        return $this->intensitySpeed;
    }

    /**
     * @param mixed $intensitySpeed
     */
    public function setIntensitySpeed($intensitySpeed)
    {
        $this->intensitySpeed = $intensitySpeed;
    }

    /**
     * @return mixed
     */
    public function getIntensitySlope()
    {
        return $this->intensitySlope;
    }

    /**
     * @param mixed $intensitySlope
     */
    public function setIntensitySlope($intensitySlope)
    {
        $this->intensitySlope = $intensitySlope;
    }

    /**
     * @return mixed
     */
    public function getCompletedTime()
    {
        return $this->completedTime;
    }

    /**
     * @param mixed $completedTime
     */
    public function setCompletedTime($completedTime)
    {
        $this->completedTime = $completedTime;
    }
}
