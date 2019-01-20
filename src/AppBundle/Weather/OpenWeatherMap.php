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
     * @param  int    $atLeast
     *
     * @return array
     */
    public function get($lat, $lon, $atLeast = 10)
    {
        $weathers = $this->owm->getCitiesInCycle($lat, $lon);
        $fetched = [];
        $last = 0;

        while (count($weathers) < $atLeast && $last != count($weathers)) {
            $last = count($weathers);

            foreach ($weathers as $weather) {
                // @todo decorate owm with caching
                if (in_array($weather->city->id, $fetched) == true) continue;

                $extras = $this->owm->getCitiesInCycle($weather->city->lat, $weather->city->lon);
                $fetched[] = $weather->city->id;
                $weathers = array_merge($weathers, $extras);
                $this->unique($weathers);
            }
        }

        return array_slice($weathers, 0, $atLeast);
    }

    protected function unique(&$weathers)
    {
        $ids = [];

        foreach ($weathers as $key => $weather) {
            if (in_array($weather->city->id, $ids) == false) {
                $ids[] = $weather->city->id;
                continue;
            }

            if (in_array($weather->city->id, $ids) == true) {
                unset($weathers[$key]);
            }
        }
    }
}
