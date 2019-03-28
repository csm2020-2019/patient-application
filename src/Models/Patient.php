<?php
namespace csm2020\PatientApp\Models;

use csm2020\PatientApp\Database\Database;

use PDO;
use PDOException;

class Patient
{
    private $patientId;
    private $userId;
    private $firstName;
    private $lastName;
    private $dob;
    private $address;
    private $medicalHistory;
    private $diagnosis;
    private $prescriptions;
    private $subscription;

    public function __construct($firstName, $lastName, $dob, $address, $medicalHistory, $diagnosis, $prescriptions,
                                $subscription, $patientId = null, $userId = null)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->dob = $dob;
        $this->address = $address;
        $this->medicalHistory = $medicalHistory;
        $this->diagnosis = $diagnosis;
        $this->prescriptions = $prescriptions;
        $this->subscription = $subscription;

        // Optional assignments, almost always will be null though
        $this->patientId = $patientId;
        $this->userId = $userId;
    }

    private function patientFactory(array $ingredients): Patient
    {
     return null; // TODO: Does this need to go here?
    }

    public function getByPatientId($patientId): Patient
    {
        $db = Database::getDatabase();
        try {
            $stmt = $db->prepare('SELECT * FROM patient_records WHERE patient_id = :pid LIMIT 1');
            $stmt->bindParam(':pid', $patientId);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                return null;
            }
            return $this->patientFactory($result);
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getByUserId($userId)
    {

    }
}
