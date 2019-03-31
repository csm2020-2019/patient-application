<?php
namespace csm2020\PatientApp\Models;

use csm2020\PatientApp\Database\Database;

use PDO;
use PDOException;
use PDOStatement;

class Patient
{
    private $patientId;
    private $userId;
    private $firstName;
    private $lastName;
    private $email;
    private $dob;
    private $address;
    private $medicalHistory;
    private $diagnosis;
    private $prescriptions;
    private $subscription;

    private function __construct($firstName, $lastName, $email, $dob, $address, $medicalHistory, $diagnosis,
                                 $prescriptions, $subscription, $patientId = null, $userId = null)
    {
        $this->firstName =      $firstName;
        $this->lastName =       $lastName;
        $this->email =          $email;
        $this->dob =            $dob;
        $this->address =        $address;
        $this->medicalHistory = $medicalHistory;
        $this->diagnosis =      $diagnosis;
        $this->prescriptions =  $prescriptions;
        $this->subscription =   $subscription;

        // Optional assignments, almost always will be null though
        $this->patientId =      $patientId;
        $this->userId =         $userId;
    }
    private function __clone() {}

    public static function factory(array $ingredients)
    {
        $patient = new Patient(
            $ingredients['patient_first_name'],
            $ingredients['patient_last_name'],
            $ingredients['patient_email'],
            $ingredients['patient_dob'],
            $ingredients['patient_address'],
            $ingredients['patient_medical_history'],
            $ingredients['patient_diagnosis'],
            $ingredients['patient_prescriptions'],
            $ingredients['patient_email_prescription'],
            $ingredients['patient_id'],
            $ingredients['userId']
        );
        return $patient;
    }

    public static function getPatientByPatientId($pid)
    {
        $pid = Database::sanitise($pid);
        if (!$pid) {
            return null;
        }

        $db = Database::getDatabase();
        try {
            $stmt = $db->prepare('SELECT * FROM patient_records WHERE patient_id = :pid LIMIT 1');
            $stmt->bindParam(':pid', $pid);
            return self::getPatient($stmt);
        } catch (PDOException $exception) {
            return null;
        }
    }

    public static function getPatientByUserId($uid)
    {
        $uid = Database::sanitise($uid);
        if (!$uid) {
            return null;
        }

        $db = Database::getDatabase();
        try {
            $stmt = $db->prepare('SELECT * FROM patient_records WHERE userId = :uid LIMIT 1');
            $stmt->bindParam(':uid', $uid);
            return self::getPatient($stmt);
        } catch (PDOException $exception) {
            return null;
        }
    }

    private static function getPatient(PDOStatement $stmt)
    {
        $stmt->execute();
        $ingredients = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$ingredients) {
            return null;
        }
        return self::factory($ingredients);
    }

    public function update()
    {
        $db = Database::getDatabase();

        // You have to do this to avoid pass by reference errors...
        $address =          $this->getAddress();
        $email =            $this->getEmail();
        $sub =              $this->getSubscription();
        $pid =              $this->getPatientId();

        try {
            $stmt = $db->prepare('UPDATE patient_records SET 
                           patient_address = :address, 
                           patient_email = :email,
                           patient_email_prescription = :sub WHERE patient_id = :pid');
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':sub', $sub);
            $stmt->bindParam(':pid', $pid);
            $stmt->execute();
        } catch (PDOException $e) {
            return null;
        }
        return true;
    }

    public function displayable()
    {
        return [
            'firstName'         => $this->getFirstName(),
            'lastName'          => $this->getLastName(),
            'dob'               => $this->getDob(),
            'medicalHistory'    => $this->getMedicalHistory(),
            'diagnosis'         => $this->getDiagnosis(),
            'prescription'      => $this->getPrescriptions(),
        ];
    }

    public function editable()
    {
        return [
            'address'           => $this->getAddress(),
            'email'             => $this->getEmail(),
            'subscription'      => $this->getSubscription()
        ];
    }

    public function getAppointment()
    {
        $pid = $this->getPatientId();
        $db = Database::getDatabase();
        $stmt = $db->prepare('SELECT * FROM sc_appointments WHERE patient_id = :pid LIMIT 1');
        $stmt->bindParam(':pid', $pid);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            return [];
        }
        return $result;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return null
     */
    public function getPatientId()
    {
        return $this->patientId;
    }

    /**
     * @param null $patientId
     */
    public function setPatientId($patientId)
    {
        $this->patientId = $patientId;
    }

    /**
     * @return null
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param null $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getDob()
    {
        return $this->dob;
    }

    /**
     * @param mixed $dob
     */
    public function setDob($dob)
    {
        $this->dob = $dob;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getMedicalHistory()
    {
        return $this->medicalHistory;
    }

    /**
     * @param mixed $medicalHistory
     */
    public function setMedicalHistory($medicalHistory)
    {
        $this->medicalHistory = $medicalHistory;
    }

    /**
     * @return mixed
     */
    public function getDiagnosis()
    {
        return $this->diagnosis;
    }

    /**
     * @param mixed $diagnosis
     */
    public function setDiagnosis($diagnosis)
    {
        $this->diagnosis = $diagnosis;
    }

    /**
     * @return mixed
     */
    public function getPrescriptions()
    {
        return $this->prescriptions;
    }

    /**
     * @param mixed $prescriptions
     */
    public function setPrescriptions($prescriptions)
    {
        $this->prescriptions = $prescriptions;
    }

    /**
     * @return mixed
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * @param mixed $subscription
     */
    public function setSubscription($subscription)
    {
        $this->subscription = $subscription;
    }
}
