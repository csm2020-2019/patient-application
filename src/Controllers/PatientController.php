<?php
namespace csm2020\PatientApp\Controllers;

use csm2020\PatientApp\Database\Database;
use csm2020\PatientApp\Models\Patient;

use PDO;
use Exception;

class PatientController
{
    public function __construct()
    {

    }

    public function get($id)
    {
        $patientData = [];
        $db = Database::getDatabase();
        if (!$id) {
            return null;
        }
        try {
            $stmt = $db->prepare('SELECT * FROM patient_records WHERE userId = :uid LIMIT 1');
            $stmt->bindParam(':uid', $id);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                return null;
            }

            $patient = Patient::factory($result);

            $patientData['displayable'] =   $patient->displayable();
            $patientData['editable'] =      $patient->editable();

            return $patientData;
        } catch (Exception $e) {
            return 'interesting error goes here';
        }
    }

    public function post(array $ingredients): bool
    {

    }
}

