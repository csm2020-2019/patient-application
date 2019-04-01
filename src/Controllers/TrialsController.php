<?php
namespace csm2020\PatientApp\Controllers;

use csm2020\PatientApp\Models\Trial;

/**
 * Class TrialsController
 * @package csm2020\PatientApp\Controllers
 * @author Oliver Earl <ole4@aber.ac.uk>
 */
class TrialsController
{
    /**
     * TrialsController constructor.
     */
    public function __construct() {}

    /**
     * Get Trials By Regime ID Method
     * @param $rid
     * @return array|null
     *
     * A simple method that returns all trials found in the database associated with a specific regime ID. Errors return
     * null, and the associative array of trials is returned on success.
     */
    public function getTrialsByRegimeId($rid)
    {
        $results = Trial::getTrialsByRegimeId($rid);
        if ($results === null) {
            return null;
        }
        $trials = [];
        foreach ($results as $result) {
            array_push($trials, $result->toAssoc());
        }
        return $trials;
    }
}
