<?php
namespace csm2020\PatientApp\Models;

use csm2020\PatientApp\Database\Database;

use PDO;
use PDOException;

/**
 * Class Regime
 * @package csm2020\PatientApp\Models
 * @author Oliver Earl <ole4@aber.ac.uk>
 */
class Regime
{
    /**
     * @var
     */
    private $regimeId;
    /**
     * @var
     */
    private $patientId;
    /**
     * @var
     */
    private $rdId;
    /**
     * @var
     */
    private $startDate;
    /**
     * @var
     */
    private $endDate;
    /**
     * @var
     */
    private $frequency;

    /**
     * Regime constructor.
     * @param $regimeId
     * @param $patientId
     * @param $rd
     * @param $startDate
     * @param $endDate
     * @param $frequency
     */
    private function __construct($regimeId, $patientId, $rd, $startDate, $endDate, $frequency)
    {
        $this->regimeId = $regimeId;
        $this->patientId = $patientId;
        $this->rdId = $rd;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->frequency = $frequency;
    }

    /**
     *
     */
    private function __clone() {}

    /**
     * @param array $ingredients
     * @return Regime
     */
    public static function factory(array $ingredients)
    {
        // regime id 1
        // patient id 30
        // rd id 3
        $regime = new Regime(
            $ingredients['regime_id'],
            $ingredients['patient_id'],
            $ingredients['rd_id'],
            $ingredients['start_date'],
            $ingredients['end_date'],
            $ingredients['frequency']
        );
        return $regime;
    }

    /**
     * Get Regime by Regime ID
     * @param $rid
     * @param $pid
     * @return bool|Regime|null
     *
     * Returns a Regime if a regime exists with this particular ID. Returns false if it doesn't exist. Returns null
     * if any errors or sanitisation problems occur.
     */
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

    /**
     * Get Regimes By Patient ID
     * @param $pid
     * @return array|null
     *
     * Returns an array of regimes belonging to a particular patient. If they don't have any, this array is empty.
     * If anything goes wrong, the method returns null.
     */
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

    /**
     * @return array
     */
    public function toAssoc()
    {
        return [
            'regime_id' =>   $this->getRegimeId(),
            'patient_id' =>  $this->getPatientId(),
            'rd_id' =>       $this->getRdId(),
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
    public function getRdId()
    {
        return $this->rdId;
    }

    /**
     * @param mixed $rdId
     */
    public function setRdId($rdId)
    {
        $this->rdId = $rdId;
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
