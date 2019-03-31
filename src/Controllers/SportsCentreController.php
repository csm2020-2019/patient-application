<?php
namespace csm2020\PatientApp\Controllers;

use csm2020\PatientApp\Models\SportsCentre;

class SportsCentreController
{
    public function __construct() {}

    public function getAllSportsCentres()
    {
        $centres = [];
        $results = SportsCentre::getAllSportCentres();
        if ($results !== []) {
            foreach ($results as $result) {
                array_push($centres, $result->toAssoc());
            }
        }
        return $centres;
    }
}
