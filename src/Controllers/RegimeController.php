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
            $regimes = Regime::getRegimesByPatientId($patient->getPatientId(), false);
            return $regimes;
        }
        return null;
    }

    public function regime($rid)
    {
        $regime = Regime::getRegimeByRegimeId($rid);
        if ($regime) {
            return $regime;
        }
        return null;
    }
}
