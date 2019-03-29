<?php
namespace csm2020\PatientApp\Controllers;

use csm2020\PatientApp\Database\Database;
use csm2020\PatientApp\Models\Patient;

use PDO;
use PDOException;

class PatientController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getDatabase();
    }

    public function get($id)
    {
        $patientData = [];
        if (!$id) {
            return null;
        }
        try {
            $stmt = $this->db->prepare('SELECT * FROM patient_records WHERE userId = :uid LIMIT 1');
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
        } catch (PDOException $e) {
            return null;
        }
    }

    public function post(array $ingredients) {}

    public function email($email, $uid)
    {
        if (!email || !$uid) {
            return null;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        try {
            $stmt = $this->db->prepare(
                'UPDATE patient_records SET patient_email = :email WHERE userId = :uid');
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':uid', $uid);
            $stmt->execute();
        } catch (PDOException $e) {
            return null;
        }
        return true;
    }

    public function emailSubscription($checkbox, $uid)
    {
        if (!$checkbox || !$uid) {
            return null;
        }

        if ($checkbox === 'true') {
            $checkbox = 1;
        } else {
            $checkbox = 0;
        }

        try {
            $stmt = $this->db->prepare(
                'UPDATE patient_records SET patient_email_prescription = :sub WHERE userId = :uid');
            $stmt->bindParam(':sub', $checkbox);
            $stmt->bindParam(':uid', $uid);
            $stmt->execute();
        } catch (PDOException $e) {
            return null;
        }
        return true;
    }
}

