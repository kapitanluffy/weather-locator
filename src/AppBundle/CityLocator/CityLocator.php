<?php

namespace AppBundle\CityLocator;

use Doctrine\ORM\EntityManagerInterface;
use AppBundle\CityLocator\City;
use AppBundle\CityLocator\CityLocatorInterface;

class CityLocator implements CityLocatorInterface
{
    protected $pdo;

    const EARTH_RADIUS_KM = 6371;

    public function __construct(EntityManagerInterface $em)
    {
        $this->pdo = $em->getConnection();
    }

    /**
     * Get place by name and zip
     *
     * @param  string $name
     * @param  string $zip
     *
     * @return City
     */
    public function get($name, $zip = null)
    {
        $name = trim($name);

        if ($name == false) {
            throw new \InvalidArgumentException("City parameter must be a non-empty string");
        }

        $stmt = $this->pdo->prepare("SELECT * FROM cities WHERE place_name LIKE :name AND postal_code LIKE :postal LIMIT 1");
        $stmt->execute(['name' => "%$name%", 'postal' => "$zip%"]);

        $results = $stmt->fetch();

        if ($results == false) {
            return false;
        }

        return new City($results);
    }

    /**
     * Search for provided city in the list
     *
     * @param  string $city
     *
     * @return City
     */
    public function search($city)
    {
        $city = trim($city);

        if ($city == false) {
            throw new \InvalidArgumentException("City parameter must be a non-empty string");
        }

        $stmt = $this->pdo->prepare("SELECT * FROM cities WHERE place_name LIKE :city OR postal_code LIKE :postal");
        $stmt->execute(['city' => "%$city%", 'postal' => "$city%"]);

        $results = $stmt->fetchAll();

        if ($results == false) {
            return false;
        }

        return array_map(function ($result) {
            return new City($result);
        }, $results);
    }

    public function getNearbyCities(City $city, $limit = 10, $radius = 100)
    {
        $query = "SELECT * FROM (SELECT *, latitude || ',' || longitude as coordinates FROM cities) a GROUP BY coordinates;";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $nearby = [];

        while ($row = $stmt->fetch()) {
            $distance = $this->calculateDistance($city->lat(), $city->lon(), $row['latitude'], $row['longitude']);
            $row['distance'] = $distance;

            if ($distance <= $radius) {
                $nearby[] = $row;
            }
        }

        usort($nearby, function ($a, $b) {
            if ($a['distance'] >= $b['distance']) return 1;
            if ($a['distance'] < $b['distance']) return -1;
        });

        $nearby = array_map(function ($n) {
            return new City($n);
        }, array_slice($nearby, 0, $limit));

        return $nearby;
    }

    protected function calculateDistance($x1, $y1, $x2, $y2)
    {
        $x1 = deg2rad($x1);
        $y1 = deg2rad($y1);
        $x2 = deg2rad($x2);
        $y2 = deg2rad($y2);

        $delta = $y2 - $y1;
        $a = pow(cos($x2) * sin($delta), 2) + pow(cos($x1) * sin($x2) - sin($x1) * cos($x2) * cos($delta), 2);
        $b = sin($x1) * sin($x2) + cos($x2) * cos($x1) * cos($delta);

        $angle = atan2(sqrt($a), $b);
        return $angle * self::EARTH_RADIUS_KM;
    }
}
