<?php
namespace csm2020\PatientApp\Controllers;

use csm2020\PatientApp\Models\Patient;
use csm2020\PatientApp\Models\Regime;
use csm2020\PatientApp\Models\Trial;


class RegimeController
{

    public function __construct() {}

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
                $dataArray['gp'] = [];

                $trialsController = new TrialsController();
                $dataArray['trials'] = $trialsController->getTrialsByRegimeId($rid);

                $userController = new UserController();
                $dataArray['gp'] = $userController->getGPDetailsById($regime->getGpId());

                return $dataArray;
            }
        }
        return null;
    }
}
