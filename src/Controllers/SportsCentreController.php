<?php
namespace csm2020\PatientApp\Controllers;

use csm2020\PatientApp\Models\Patient;
use csm2020\PatientApp\Models\SportsCentre;

/**
 * Class SportsCentreController
 * @package csm2020\PatientApp\Controllers
 * @author Oliver Earl <ole4@aber.ac.uk>
 */
class SportsCentreController
{
    public function __construct() {}

    /**
     * Get All Sports Centres Method
     * @return array|null
     *
     * This method will attempt to retrieve all the sports centres stored in the database. If there aren't any, for
     * some bizarre reason, then an empty array is returned and the frontend is free to interpret that however it wishes,
     * which is at this time of writing a simple notice that none are on the system currently, which could well be the
     * case somehow. Otherwise, an array of all the sports centres and their respective information is returned for
     * rendering. If something somewhere goes wrong, null is returned.
     */
    public function getAllSportsCentres()
    {
        $centres = [];
        $results = SportsCentre::getAllSportCentres();
        if ($results === null) {
            return null;
        }
        if ($results !== []) {
            foreach ($results as $result) {
                array_push($centres, $result->toAssoc());
            }
        }
        return $centres;
    }

    /**
     * Set Sports Centre
     * @param $uid
     * @param $scid
     * @return bool|null
     *
     * This method is used to assign a user to their preferred sports centre - sometimes referred to as an assignment
     * or a preference elsewhere in the program. It takes the current user ID, and the sports centre ID provided by the
     * route from the frontend.
     *
     * All 'appointments' associated with the patient are first removed with prejudice, just in case. Then from there,
     * the patient has their appointment set. Database or sanitisation errors will cause this method to return null,
     * or true should everything go smoothly.
     */
    public function setSportsCentre($uid, $scid)
    {
        $patient = Patient::getPatientByUserId($uid);
        $centre = SportsCentre::getSportCentreById($scid);


        if (!$centre->removeExistingAppointments($patient->getPatientId())) {
            return null;
        }
        if (!$centre->setAppointment($patient->getPatientId())) {
            return null;
        }
        return true;
    }
}
