<?php

namespace AppBundle\Weather;

use AppBundle\Weather\WeatherInterface;
use AppBundle\Weather\OpenWeatherMapProxy;
use Cmfcmf\OpenWeatherMap as BaseOpenWeatherMap;

class OpenWeatherMap implements WeatherInterface
{
    protected $owm;

    public function __construct(BaseOpenWeatherMap $owm)
    {
        $this->owm = $owm;
    }

    public function get($lat, $lon)
    {
        return $this->owm->getCitiesInCycle($lat, $lon);
    }
}
