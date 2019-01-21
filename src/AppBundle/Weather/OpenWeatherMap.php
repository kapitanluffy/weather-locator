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

    /**
     * Get weather data based on lat and lon
     *
     * @param  string $lat
     * @param  string $lon
     *
     * @return array
     */
    public function get($lat, $lon)
    {
        return $this->owm->getWeather(['lat' => $lat, 'lon' => $lon]);
    }
}
