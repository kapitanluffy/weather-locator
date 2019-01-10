<?php

namespace AppBundle\Weather;

use AppBundle\Weather\WeatherInterface;
use AppBundle\Weather\OpenWeatherMapProxy;

class OpenWeatherMap implements WeatherInterface
{
    protected $owm;

    public function __construct($apiKey)
    {
        $this->owm = new OpenWeatherMapProxy($apiKey);
    }

    public function get($lat, $lon)
    {
        return $this->owm->getCitiesInCycle($lat, $lon);
    }
}
