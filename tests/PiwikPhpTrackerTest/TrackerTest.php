<?php
namespace PiwikPhpTrackerTest;

use PHPUnit_Framework_TestCase;
use PiwikPhpTracker\Tracker;

/**
 * @covers \PiwikPhpTracker\Tracker
 */
class TrackerTest extends PHPUnit_Framework_TestCase
{

    public function testCanCreate()
    {
        $tracker = new Tracker('http://example.com', 1);
        $this->assertInstanceOf('PiwikPhpTracker\Tracker', $tracker);
        $this->assertInstanceOf('PiwikPhpTracker\Parameters', $tracker);
    }

    public function testIdSite()
    {
        $tracker = new Tracker('http://example.com', 1);
        $this->assertEquals(1, $tracker->getIdSite());
        
        $tracker->setIdSite(25);
        $this->assertEquals(25, $tracker->getIdSite());
    }
}
