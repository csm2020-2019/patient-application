<?php
namespace csm2020\PatientApp\Controllers;

use csm2020\PatientApp\Database\Database;
use csm2020\PatientApp\Models\Regime;
use csm2020\PatientApp\Models\Trial;

use PDO;
use PDOException;

class RegimeController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getDatabase();
    }

    public function getRegime($pid)
    {
        if (!$pid) {
            return null;
        }

        try {
            $stmt = $this->db->prepare('SELECT * FROM exercise_regimes WHERE patient_id = :pid LIMIT 1');
            $stmt->bindParam(':pid', $pid);
            $stmt->execute();
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getTrials($regimeId)
    {
        if (!$regimeId) {
            return null;
        }

        $trials = [];
        try {
            $stmt = $this->db->prepare('SELECT * FROM exercise_trials WHERE regime_id = :rid');
            $stmt->bindParam(':rid', $regimeId);
            $stmt->execute();

            $results = $stmt->fetch(PDO::FETCH_ASSOC);
            foreach ($results as $result) {
                array_push($trials, Trial::factory($result));
            }
            return $trials;
        } catch (PDOException $e) {
            return null;
        }
    }

}
