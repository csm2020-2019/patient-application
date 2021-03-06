<?php
namespace csm2020\PatientApp\Controllers;

use csm2020\PatientApp\Models\Patient;
use csm2020\PatientApp\Models\Regime;
use csm2020\PatientApp\Models\Trial;

/**
 * Class RegimeController
 * @package csm2020\PatientApp\Controllers
 * @author Oliver Earl <ole4@aber.ac.uk>
 */
class RegimeController
{

    /**
     * RegimeController constructor.
     */
    public function __construct() {}

    /**
     * Regimes Method
     * @param $uid
     * @return array|null
     *
     * Using a user's ID, this method first builds its patient equivalent by ID. If it exists, it will proceed to pull
     * all regimes attached to that current patient and builds up an array. If there's none, this will still return
     * an empty array which will be interpreted by the frontend as simply not having any regimes. Otherwise, a patient
     * can have limitless regimes set by their GP. If anything goes wrong, this method returns null, which is
     * interpreted as an error by the rest of the application.
     */
    public function regimes($uid)
    {
        $patient = Patient::getPatientByUserId($uid);
        if ($patient) {
            $results = Regime::getRegimesByPatientId($patient->getPatientId());
            $regimes = [];
            foreach ($results as $result) {
                array_push($regimes, $result->toAssoc());
            }
            return $regimes;
        }
        return null;
    }

    /**
     * Regime Method
     * @param $rid
     * @param $uid
     * @return array|null
     *
     * Using a specific regime ID and user ID, this method returns all the details needed for the specific regime view.
     * This includes not only the regime information contained in the regime object, but also all of the trials that
     * are associated to the specific regime by invoking a Trials Controller object, as well as gathering all the
     * contact information of the GP who assigned the regime from a method in the User Controller.
     *
     * Returns a large associative array of information for use by the frontend, or null.
     */
    public function regime($rid, $uid)
    {
        $patient = Patient::getPatientByUserId($uid);
        if ($patient) {
            $regime = Regime::getRegimeByRegimeId($rid, $patient->getPatientId());
            if ($regime !== null) {
                if ($regime === false) {
                    return [];
                }
                $dataArray = $regime->toAssoc();
                $dataArray['trials'] = [];
                $dataArray['rd'] = [];

                $trialsController = new TrialsController();
                $dataArray['trials'] = $trialsController->getTrialsByRegimeId($rid);

                $userController = new UserController();
                $dataArray['gp'] = $userController->getRDDetailsById($regime->getRdId());
                return $dataArray;
            }
        }
        return null;
    }
}
