<?php

use AppBundle\Weather\OpenWeatherMap;
use Cmfcmf\OpenWeatherMap as BaseOpenWeatherMap;
use Cmfcmf\OpenWeatherMap\CurrentWeather;
use Cmfcmf\OpenWeatherMap\Util\City;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class OpenWeatherMapTest extends TestCase
{
    public function testGet()
    {
        $base = $this->getMockBuilder(BaseOpenWeatherMap::class)
            ->setMethods(['getCitiesInCycle'])
            ->getMock();

        $weather = $this->getMockBuilder(CurrentWeather::class)
            ->disableOriginalConstructor()
            ->getMock();

        $weather->city = new City(1, 'foo', -35, 149, 'bar', 0);

        $base->method('getCitiesInCycle')
            ->with(-35, 149)
            ->willReturn([$weather]);

        $owm = new OpenWeatherMap($base);
        $results = $owm->get(-35, 149);

        foreach ($results as $result) {
            $this->assertInstanceof(CurrentWeather::class, $result);
        }
    }
}
