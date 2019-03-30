<?php
namespace csm2020\PatientApp\Controllers;

use csm2020\PatientApp\Models\Trial;

class TrialsController
{
    public function __construct() {}

    public function getTrialsByRegimeId($rid)
    {
        $results = Trial::getTrialsByRegimeId($rid);
        $trials = [];
        foreach ($results as $result) {
            array_push($trials, $result->toAssoc());
        }
        return $trials;
    }
}
