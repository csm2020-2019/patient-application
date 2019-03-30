<?php
namespace csm2020\PatientApp\Controllers;

use csm2020\PatientApp\Models\Patient;

class PatientController
{
    public function __construct() {}

    public function get($id)
    {
        $patientData = [];
        $patient = Patient::getPatientByUserId($id);

        $patientData['displayable'] =   $patient->displayable();
        $patientData['editable'] =      $patient->editable();

        return $patientData;
    }

    public function address($address1, $address2, $town, $postcode, $uid)
    {
        if (!$this->validateAddress([$address1, $address2, $town, $postcode])) {
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

        $address = "${address1}, ${address2} ${town}, ${postcode}";

        $patient = Patient::getPatientByUserId($uid);
        if ($patient) {
            $patient->setAddress($address);
            if ($patient->update()) {
                return true;
            }
        }
        return null;
    }

    private function validateAddress(array $components)
    {
        if ($components[1] === null) {
            array_pop($components[1]); // Address 2, which is optional
        }
        foreach ($components as $component) {
            if ($component === null)  {
                return false;
            }
            if (strlen($component) > 30 || strlen($component) < 4) {
                return false;
            }
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

        $patient = Patient::getPatientByUserId($uid);
        if ($patient) {
            $patient->setEmail($email);
            if ($patient->update()) {
                return true;
            }
        }
        return null;
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

        $patient = Patient::getPatientByUserId($uid);
        if ($patient) {
            $patient->setSubscription($checkbox);
            if ($patient->update()) {
                return true;
            }
        }
        return null;
    }
}

