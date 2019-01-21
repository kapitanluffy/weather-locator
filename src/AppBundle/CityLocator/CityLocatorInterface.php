<?php

namespace AppBundle\CityLocator;

interface CityLocatorInterface
{
    /**
     * Get place by name and zip
     *
     * @param  string $name
     *
     * @return City
     */
    public function get($name);

    /**
     * Search for provided city in the list
     *
     * @param  string $city
     *
     * @return City
     */
    public function search($city);

    public function getNearbyCities(City $city, $limit = 10, $radius = 100);
}
