<?php
namespace PiwikPhpTrackerTest;

use PHPUnit_Framework_TestCase;
use PiwikPhpTracker\Tracker;

/**
 * @covers \PiwikPhpTracker\Tracker
 */
class TrackerTest extends PHPUnit_Framework_TestCase
{
    public function testCanCreate(){
        $tracker = new Tracker(1);
        
        $this->assertInstanceOf('PiwikPhpTracker\Tracker', $tracker);
    }
}
