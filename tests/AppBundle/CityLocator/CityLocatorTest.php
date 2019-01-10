<?php

namespace Tests\AppBundle\CityLocator;

use AppBundle\CityLocator\CityLocator;
use AppBundle\CityLocator\City;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CityLocatorTest extends KernelTestCase
{
    protected $em;

    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->em = $kernel->getContainer()->get('doctrine')->getManager();
    }

    public function testGet()
    {
        $locator = new CityLocator($this->em);
        $city = $locator->get('sydney');

        $this->assertInstanceOf(City::class, $city);
        $this->assertRegExp("/sydney/i", $city->name());
    }

    public function testGetWithZip()
    {
        $locator = new CityLocator($this->em);
        $city = $locator->get('sydney', '1006');

        $this->assertInstanceOf(City::class, $city);
        $this->assertRegExp("/sydney/i", $city->name());
        $this->assertEquals('1006', $city->zipCode());
    }

    public function testGetEmptyCity()
    {
        $this->expectException(\InvalidArgumentException::class);

        $locator = new CityLocator($this->em);
        $city = $locator->get('', '1006');
    }

    public function testGetCityNotFound()
    {
        $locator = new CityLocator($this->em);
        $city = $locator->get('NonExistentCity', '1006');

        $this->assertFalse($city);
    }

    public function testSearch()
    {
        $locator = new CityLocator($this->em);
        $cities = $locator->search('melbourne');

        foreach ($cities as $city) {
            $this->assertInstanceOf(City::class, $city);
            $this->assertRegExp("/melbourne/i", $city->name());
        }
    }

    public function testSearchEmptyCity()
    {
        $this->expectException(\InvalidArgumentException::class);

        $locator = new CityLocator($this->em);
        $cities = $locator->search('');
    }

    public function testSearchCityNotFound()
    {
        $locator = new CityLocator($this->em);
        $cities = $locator->search('NonExistentCity');

        $this->assertFalse($cities);
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null;
    }
}
