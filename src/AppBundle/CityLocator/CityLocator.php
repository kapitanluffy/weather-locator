<?php

namespace AppBundle\CityLocator;

use Doctrine\ORM\EntityManagerInterface;
use AppBundle\CityLocator\City;

class CityLocator
{
    protected $pdo;

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
}
