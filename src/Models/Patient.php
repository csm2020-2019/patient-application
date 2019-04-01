<?php
namespace csm2020\PatientApp\Models;

use csm2020\PatientApp\Database\Database;

use PDO;
use PDOException;
use PDOStatement;

/**
 * Class Patient
 * @package csm2020\PatientApp\Models
 * @author Oliver Earl <ole4@aber.ac.uk>
 */
class Patient
{
    /**
     * @var null
     */
    private $patientId;
    /**
     * @var null
     */
    private $userId;
    /**
     * @var
     */
    private $firstName;
    /**
     * @var
     */
    private $lastName;
    /**
     * @var
     */
    private $email;
    /**
     * @var
     */
    private $dob;
    /**
     * @var
     */
    private $address;
    /**
     * @var
     */
    private $medicalHistory;
    /**
     * @var
     */
    private $diagnosis;
    /**
     * @var
     */
    private $prescriptions;
    /**
     * @var
     */
    private $subscription;

    /**
     * Patient constructor.
     * @param $firstName
     * @param $lastName
     * @param $email
     * @param $dob
     * @param $address
     * @param $medicalHistory
     * @param $diagnosis
     * @param $prescriptions
     * @param $subscription
     * @param null $patientId
     * @param null $userId
     */
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

    /**
     *
     */
    private function __clone() {}

    /**
     * @param array $ingredients
     * @return Patient
     */
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

    /**
     * Get Patient by Patient ID
     * @param $pid
     * @return Patient|null
     *
     * Returns a freshly instantiated patient object by searching by its patient ID. Null on failure.
     */
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

    /**
     * Get Patient by User ID
     * @param $uid
     * @return Patient|null
     *
     * An extremely important method as it allows for the effective conversion of a user ID into a patient ID by
     * retrieving the patient with the affiliated user ID foreign key. Returns null on failure.
     */
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

    /**
     * Get Patient Method
     * @param PDOStatement $stmt
     * @return Patient|null
     *
     * In order to reduce code duplication, both types of Get Patient by pid or uid will call this method to finish
     * the rest of the query, passing its fully formed PDOStatement as a parameter. Returns the completed patient object
     * or null if something goes wrong.
     */
    private static function getPatient(PDOStatement $stmt)
    {
        $stmt->execute();
        $ingredients = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$ingredients) {
            return null;
        }
        return self::factory($ingredients);
    }

    /**
     * Update Method
     * @return bool|null
     *
     * Writes current changes to the patient object. For safety, it only saves data that is currently considered
     * modifiable but could be opened up later. If a database error occurs, null is returned.
     */
    public function update()
    {
        $db = Database::getDatabase();

        // You have to do this to avoid pass by reference errors...
        $address =          $this->getAddress();
        $email =            $this->getEmail();
        $sub =              $this->getSubscription();
        $pid =              $this->getPatientId();
        $uid =              $this->getUserId();

        try {
            $stmt = $db->prepare('UPDATE patient_records SET 
                           patient_address = :address, 
                           patient_email = :email,
                           patient_email_prescription = :sub,
                           userId = :uid WHERE patient_id = :pid');
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':sub', $sub);
            $stmt->bindParam(':pid', $pid);
            $stmt->bindParam(':uid', $uid);
            $stmt->execute();
        } catch (PDOException $e) {
            return null;
        }
        return true;
    }

    /**
     * @return array
     */
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

    /**
     * @return array
     */
    public function editable()
    {
        return [
            'address'           => $this->getAddress(),
            'email'             => $this->getEmail(),
            'subscription'      => $this->getSubscription()
        ];
    }

    /**
     * Get Appointment Method
     * @return array|mixed
     *
     * Returns the current patient's 'appointment' using the patient ID. This is their preferred sports centre.
     *
     * TODO: This probably isn't the right place for this method, it's just here historically. Refactor.
     */
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
