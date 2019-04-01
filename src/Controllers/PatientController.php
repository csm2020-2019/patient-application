<?php
namespace csm2020\PatientApp\Controllers;

use csm2020\PatientApp\Models\Patient;
use csm2020\PatientApp\Database\Database;

/**
 * Class PatientController
 * @package csm2020\PatientApp\Controllers
 * @author Oliver Earl <ole4@aber.ac.uk>
 */
class PatientController
{
    /**
     * PatientController constructor.
     */
    public function __construct() {}

    /**
     * Get Patient By User ID
     * @param $id
     * @return array|null
     *
     * This method takes a User ID, and uses it to grab a Patient object using a static method. This object is then used
     * to construct an associative array of very select information that the frontend can display as read-only, or be
     * inserted for use in forms, like the patient's address or email address. If anything goes wrong, null is returned.
     */
    public function get($id)
    {
        $patientData = [];
        $patient = Patient::getPatientByUserId($id);
        if (!$patient) {
            return null;
        }

        $patientData['displayable'] =   $patient->displayable();
        $patientData['editable'] =      $patient->editable();

        return $patientData;
    }

    /**
     * Address Method
     * @param $address1
     * @param $address2
     * @param $town
     * @param $postcode
     * @param $uid
     * @return bool|null
     *
     * When an address is provided to the backend to be changed, after sanitisation and validation, the aspects of the
     * address are concatenated into a master string for insertion into the database.
     *
     * This is done by retrieving a Patient object using the current user's ID, and if there's no funny business, setting
     * the address property and updating the changes accordingly. If it all goes swell, true is returned. Otherwise,
     * a null value is returned.
     */
    public function address($address1, $address2, $town, $postcode, $uid)
    {
        $address1 =     Database::sanitise($address1);
        $address2 =     Database::sanitise($address2);
        $town =         Database::sanitise($town);
        $postcode =     Database::sanitise($postcode);

        if (!$this->validateAddress([$address1, $address2, $town, $postcode])) {
            return null;
        }
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

    /**
     * Validate Address
     * @param array $components
     * @return bool
     *
     * This small method checks that none of the inputs are null and are neither empty, or too small or too long.
     * This is also enforced client-side, so this mainly exists to circumvent any shenanigans. False is returned on any
     * failure of validation. True if everything is okay.
     */
    public function validateAddress(array $components)
    {
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

    /**
     * Email Method
     * @param $email
     * @param $uid
     * @return bool|null
     *
     * This method is used for changing the user's email address. After sanitisation and validation, in the same way
     * as the address method works - a Patient object is insantiated using the current user ID, the property is
     * modified, and then the changes are saved to the database. Any funny business or failure returns null. True
     * is returned if everything goes swimmingly.
     */
    public function email($email, $uid)
    {
        $email = Database::sanitise($email);

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

    /**
     * Checkbox Method
     * @param $checkbox
     * @param $uid
     * @return bool|null
     *
     * Works in the exact same way as the other two methods for patient information, although this takes into account
     * certain quirks when submitting data from HTML checkboxes and any type issues. Patient object is insantiated,
     * updated, and saved. Null on failure.
     */
    public function emailSubscription($checkbox, $uid)
    {
        Database::sanitise($checkbox);

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

    /**
     * Get Appointment
     * @param $uid
     * @return array|mixed
     *
     * This method takes a user ID to instantiate a Patient like its siblings, and acts as a gateway to the appropriate
     * method in the model itself. Ideally returns an array of sports centre data.
     */
    public function getAppointment($uid)
    {
        $patient = Patient::getPatientByUserId($uid);
        return $patient->getAppointment();
    }
}

