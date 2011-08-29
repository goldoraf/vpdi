<?php

require_once dirname(__FILE__) . '/../../TestsHelper.php';
 
class Vpdi_Icalendar_VeventTest extends PHPUnit_Framework_TestCase
{
  public function testBasicSample() {
    $sample = <<<EOF
BEGIN:VEVENT
DTSTAMP:19980309T231000Z
UID:guid-1.host1.com
ORGANIZER;ROLE=CHAIR:MAILTO:mrbig@host.com
ATTENDEE;RSVP=TRUE;ROLE=REQ-PARTICIPANT;CUTYPE=GROUP:MAILTO:employee-A@host.com
DESCRIPTION:Project XYZ Review Meeting
CATEGORIES:MEETING
CLASS:PUBLIC
CREATED:19980309T130000Z
SUMMARY:XYZ Project Review
DTSTART;TZID=US-Eastern:19980312T083000
DTEND;TZID=US-Eastern:19980312T093000
LOCATION:1CP Conference Room 4350
GEO:37.386013;-122.082932
PRIORITY:1
END:VEVENT
EOF;
    date_default_timezone_set('Europe/Paris');
    $event = Vpdi::decodeOne($sample);
    
    $this->assertTrue($event instanceof Vpdi_Icalendar_Vevent);
    
    $dtstart = $event->dtstart;
    $this->assertTrue($dtstart instanceof DateTime);
    $this->assertEquals('US/Eastern', $dtstart->getTimezone()->getName());
    $this->assertEquals('1998-03-12T02:30:00-0500', $dtstart->format(DateTime::ISO8601));
    
    $dtend = $event->dtend;
    $this->assertTrue($dtend instanceof DateTime);
    $this->assertEquals('US/Eastern', $dtend->getTimezone()->getName());
    $this->assertEquals('1998-03-12T03:30:00-0500', $dtend->format(DateTime::ISO8601));
    
    $this->assertEquals('MAILTO:mrbig@host.com', $event->organizer->uri);
    $this->assertEquals(Vpdi_Icalendar_Role::CHAIR, $event->organizer->role);
    
    $this->assertEquals(1, count($event->attendees));
    $this->assertEquals('MAILTO:employee-A@host.com', $event->attendees[0]->uri);
    $this->assertTrue($event->attendees[0]->rsvp);
    $this->assertEquals(Vpdi_Icalendar_Role::REQ_PARTICIPANT, $event->attendees[0]->role);
    
    $this->assertEquals(array('MEETING'), $event->categories);
    $this->assertEquals('Project XYZ Review Meeting', $event->description);
    
    $this->assertEquals('1CP Conference Room 4350', $event->location);
    $this->assertEquals(array(37.386013,-122.082932), $event->geo);
    $this->assertEquals(1, $event->priority);
    $this->assertEquals(0, $event->sequence);
    
    $this->assertEquals($sample, $event->__toString());
  }
  
  public function testBasicEventCreation() {
    $sample = <<<EOF
BEGIN:VEVENT
DTSTART:20100101T190000Z
DTEND:20100101T210000Z
SUMMARY:Dinner with Lucy
DESCRIPTION:Meet at the restaurant
CATEGORIES:MEETING,BUSINESS
STATUS:TENTATIVE
TRANSP:TRANSPARENT
ORGANIZER;CN=John Doe:mailto:jdoe@example.com
GEO:37.386013;-122.082932
END:VEVENT
EOF;
    date_default_timezone_set('Europe/Paris');
    $event = new Vpdi_Icalendar_Vevent;
    $event->dtstart = new DateTime('2010-01-01 20:00:00');
    $event->dtend = new DateTime('2010-01-01 22:00:00');
    $event->summary = 'Dinner with Lucy';
    $event->description = 'Meet at the restaurant';
    $event->categories = array('MEETING', 'BUSINESS');
    $event->status = Vpdi_Icalendar_Status::TENTATIVE;
    
    $this->assertFalse($event->isTransparent());
    $event->addProperty(new Vpdi_Property('TRANSP', 'OPAQUE'));
    $event->setAsTransparent();
    $this->assertTrue($event->isTransparent());
    $this->assertEquals(1, count($event->getPropertiesByName('TRANSP')));
    
    $event->organizer = new Vpdi_Icalendar_Organizer('mailto:jdoe@example.com', 'John Doe');
    
    $event->geo = array(37.386013,-122.082932);
    
    $this->assertEquals($sample, $event->__toString());
  }
}