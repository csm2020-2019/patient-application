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

    private function __clone()
    {
    }

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
