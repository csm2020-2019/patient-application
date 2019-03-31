<?php
namespace csm2020\PatientApp\Models;

use csm2020\PatientApp\Database\Database;
use PDO;
use PDOException;

class SportsCentre
{
    private $id;
    private $name;
    private $address;
    private $availability;
    private $userId;

    private function __construct($id, $name, $address, $availability, $userId)
    {
        $this->id =             $id;
        $this->name =           $name;
        $this->address =        $address;
        $this->availability =   $availability;
        $this->userId =         $userId;
    }

    private function __clone() {}

    public function toAssoc()
    {
        return [
            'sportsCenterId'                    => $this->getId(),
            'sportsCenterName'                  => $this->getName(),
            'sportsCenterAddress'               => $this->getAddress(),
            'sportsCenterAvailability'          => $this->getAvailability(),
            //'user_id'                           => $this->getUserId(),
        ];
    }

    public static function factory(array $ingredients)
    {
        $sportsCentre = new SportsCentre(
            $ingredients['sportsCenterId'],
            $ingredients['sportsCenterName'],
            $ingredients['sportsCenterAddress'],
            $ingredients['sportsCenterAvailability'],
            $ingredients['user_id']
        );
        return $sportsCentre;
    }

    public static function getSportCentreById($scid)
    {
        $scid = Database::sanitise($scid);
        if (!$scid) {
            return null;
        }
        $db = Database::getDatabase();
        try {
            $stmt = $db->prepare('SELECT * FROM sports_center WHERE sportsCenterId = :scid LIMIT 1');
            $stmt->bindParam(':pid', $pid);
            $stmt->execute();
            $centres = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$centres) {
                return [];
            }
            $builtCentres = [];
            foreach($centres as $centre) {
                array_push($builtCentres, self::factory($centre));
            }
            return $builtCentres;
        } catch (PDOException $e) {
            return null;
        }
    }

    public static function getAllSportCentres()
    {
        $db = Database::getDatabase();
        try {
            $stmt = $db->prepare('SELECT * FROM sports_center');
            $stmt->execute();
            $centres = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$centres) {
                return [];
            }
            $builtCentres = [];
            foreach($centres as $centre) {
                array_push($builtCentres, self::factory($centre));
            }
            return $builtCentres;
        } catch (PDOException $e) {
            return null;
        }
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getAvailability()
    {
        return $this->availability;
    }

    /**
     * @param mixed $availability
     */
    public function setAvailability($availability)
    {
        $this->availability = $availability;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }
}
