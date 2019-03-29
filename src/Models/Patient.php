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

    private function __construct($firstName, $lastName, $dob, $address, $medicalHistory, $diagnosis, $prescriptions,
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
    private function __clone() {}

    public static function factory(array $ingredients)
    {
        $patient = new Patient(
            $ingredients['patient_first_name'],
            $ingredients['patient_last_name'],
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
            'subscription'      => $this->getSubscription()
        ];
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
