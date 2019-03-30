<?php
namespace csm2020\PatientApp\Models;

use csm2020\PatientApp\Database\Database;

use PDO;
use PDOException;

class Regime
{
    private $regimeId;
    private $patientId;
    private $gpId;
    private $startDate;
    private $endDate;
    private $frequency;

    private function __construct($regimeId, $patientId, $gpId, $startDate, $endDate, $frequency)
    {
        $this->regimeId = $regimeId;
        $this->patientId = $patientId;
        $this->gpId = $gpId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->frequency = $frequency;
    }

    private function __clone() {}

    public static function factory(array $ingredients)
    {
        $regime = new Regime(
            $ingredients['regime_id'],
            $ingredients['patient_id'],
            $ingredients['gp_id'],
            $ingredients['start_date'],
            $ingredients['end_date'],
            $ingredients['frequency']
        );
        return $regime;
    }

    public static function getRegimeByRegimeId($rid, $pid)
    {
        $rid = Database::sanitise($rid);
        $pid = Database::sanitise($pid);
        if (!$rid || !$pid) {
            return null;
        }

        $db = Database::getDatabase();
        try {
            $stmt = $db->prepare('SELECT * FROM exercise_regimes WHERE regime_id = :rid AND patient_id = :pid LIMIT 1');
            $stmt->bindParam(':rid', $rid);
            $stmt->bindParam(':pid', $pid);
            $stmt->execute();
            $ingredients = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$ingredients) {
                return false;
            }
            return self::factory($ingredients);
        } catch (PDOException $exception) {
            return null;
        }
    }

    public static function getRegimesByPatientId($pid)
    {
        $pid = Database::sanitise($pid);
        if (!$pid) {
            return null;
        }

        $db = Database::getDatabase();
        try {
            $stmt = $db->prepare('SELECT * FROM exercise_regimes WHERE patient_id = :pid');
            $stmt->bindParam(':pid', $pid);
            $stmt->execute();

            $regimes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$regimes) {
                return [];
            }
            $builtRegimes = [];
            foreach($regimes as $regime) {
                array_push($builtRegimes, self::factory($regime));
            }
            return $builtRegimes;
        } catch (PDOException $exception) {
            return null;
        }
    }

    public function toAssoc()
    {
        return [
            'regime_id' =>   $this->getRegimeId(),
            'patient_id' =>  $this->getPatientId(),
            'gp_id' =>       $this->getGpId(),
            'start_date' =>  $this->getStartDate(),
            'end_date' =>    $this->getEndDate(),
            'frequency' =>   $this->getFrequency()
        ];
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
    public function getPatientId()
    {
        return $this->patientId;
    }

    /**
     * @param mixed $patientId
     */
    public function setPatientId($patientId)
    {
        $this->patientId = $patientId;
    }

    /**
     * @return mixed
     */
    public function getGpId()
    {
        return $this->gpId;
    }

    /**
     * @param mixed $gpId
     */
    public function setGpId($gpId)
    {
        $this->gpId = $gpId;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param mixed $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param mixed $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * @return mixed
     */
    public function getFrequency()
    {
        return $this->frequency;
    }

    /**
     * @param mixed $frequency
     */
    public function setFrequency($frequency)
    {
        $this->frequency = $frequency;
    }
}
