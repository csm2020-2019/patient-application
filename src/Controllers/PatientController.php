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

    public function address($address1, $address2, $town, $postcode, $uid)
    {
        if (!$address1 || !$town || !$postcode || !$uid) {
            return null;
        }
        // Cleanse tags of any funny business
        $address1 =     trim(stripslashes(htmlspecialchars(strip_tags($address1))));
        if ($address2) {
            $address2 = trim(stripslashes(htmlspecialchars(strip_tags($address2))));
            $address2 = "{$address2},";
        } else {
            $address2 = '';
        }
        $town =         trim(stripslashes(htmlspecialchars(strip_tags($town))));
        $postcode =     trim(stripslashes(htmlspecialchars(strip_tags($postcode))));

        // One last check on the postcode
        // https://stackoverflow.com/questions/14935013/preg-match-regex-required-for-specific-uk-postcode-area-code
        $accepted_numbers = array_merge(range(15, 22), range(31, 41));
        if (!preg_match('#^(GIR ?0AA|[A-PR-UWYZ]([0-9]{1,2}|([A-HK-Y][0-9]([0-9ABEHMNPRV-Y])?)|[0-9][A-HJKPS-UW]) ?[0-9][ABD-HJLNP-UW-Z]{2})$#', $postcode) && !substr($postcode, 0, 2) == 'DN' && !in_array(substr($postcode, 2, 2), $accepted_numbers)) {
            return null;
        }
        $address = "${address1}, ${address2} ${town}, ${postcode}";
        try {
            $stmt = $this->db->prepare(
                'UPDATE patient_records SET patient_address = :address WHERE userId = :uid');
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':uid', $uid);
            $stmt->execute();
        } catch (PDOException $e) {
            return null;
        }
        return true;
    }

    public function email($email, $uid)
    {
        if (!$email || !$uid) {
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

