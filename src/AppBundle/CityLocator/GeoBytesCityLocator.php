<?php

namespace AppBundle\CityLocator;

use AppBundle\CityLocator\City;
use AppBundle\CityLocator\CityLocatorInterface;

class GeoBytesCityLocator implements CityLocatorInterface
{
    protected $locator;

    protected $urls = [
        'nearby_cities' => 'http://getnearbycities.geobytes.com/GetNearbyCities'
    ];

    public function __construct(CityLocatorInterface $locator)
    {
        $this->locator = $locator;
    }

    public function get($name, $zip = null)
    {
        return $this->locator->get($name, $zip);
    }

    public function search($name)
    {
        return $this->locator->search($name);
    }

    /**
     * Get nearby cities based on provided City object
     *
     * @param  City   $city
     * @param  int    $limit
     * @param  int    $radius
     *
     * @return City[]
     */
    public function getNearbyCities(City $city, $limit = 10, $radius = 100)
    {
        $query = ['radius' => $radius, 'limit' => $limit, 'latitude' => $city->lat(), 'longitude' => $city->lon()];
        $query = http_build_query($query);

        $url = sprintf("%s?%s", $this->urls['nearby_cities'], $query);

        $cities = $this->fetch($url);
        $nearby = [];

        foreach ($cities as $city) {
            $nearby[] = new City([
                'country_code' => $city[6],
                'place_name' => $city[1],
                'postal_code' => $city[2],
                'latitude' => $city[8],
                'longitude' => $city[10],
                'original' => $city
            ]);
        }

        return $nearby;
    }

    protected function fetch($url, $options = [])
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt_array($ch, $options);

        $content = curl_exec($ch);
        curl_close($ch);

        return json_decode($content, true);
    }
}
