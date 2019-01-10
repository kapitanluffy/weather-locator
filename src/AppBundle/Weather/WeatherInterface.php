<?php

namespace AppBundle\Weather;

interface WeatherInterface
{
    /**
     * Get weather based on coordinates
     *
     * @param  int $lat
     * @param  int $lon
     *
     * @return mixed
     */
    public function get($lat, $lon);
}
