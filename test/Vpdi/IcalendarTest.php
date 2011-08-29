<?php

require_once dirname(__FILE__) . '/../TestsHelper.php';
 
class Vpdi_IcalendarTest extends PHPUnit_Framework_TestCase
{
  public function testBasicSample() {
    $sample = <<<EOF
BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//hacksw/handcal//NONSGML v1.0//EN
BEGIN:VEVENT
DTSTART:19970714T170000Z
DTEND:19970715T035959Z
SUMMARY:Bastille Day Party
END:VEVENT
END:VCALENDAR
EOF;
    $cal = Vpdi::decodeOne($sample);
    
    $this->assertTrue($cal instanceof Vpdi_Icalendar);
    $this->assertEquals(1, count($cal->getComponents()));
    $this->assertEquals(1, count($cal->getComponents(Vpdi_Icalendar_Component::VEVENT)));
    $this->assertEquals(0, count($cal->getComponents(Vpdi_Icalendar_Component::VFREEBUSY)));
    
    $events = $cal->getComponents(Vpdi_Icalendar_Component::VEVENT);
    $e = $events[0];
    
    $this->assertTrue($e instanceof Vpdi_Icalendar_Vevent);
    $this->assertEquals('Bastille Day Party', $e->summary);
    $this->assertTrue($e->dtstart == new DateTime('1997-07-14T17:00:00+0000'));
    $this->assertTrue($e->dtend == new DateTime('1997-07-15T03:59:59+0000'));
  }
  
  public function testBusyPeriods() {
    $sample = <<<EOF
BEGIN:VCALENDAR
VERSION:2.0
CALSCALE:GREGORIAN
METHOD:REPLY
BEGIN:VFREEBUSY
DTSTAMP:20100212T124344Z
ORGANIZER:mailto:jdoe@test.com
DTSTART:20100112T124344Z
DTEND:20100312T124344Z
FREEBUSY;FBTYPE=BUSY:20100307T090000Z/20100307T100000Z
FREEBUSY;FBTYPE=BUSY:20100119T083000Z/20100119T093000Z
FREEBUSY;FBTYPE=BUSY:20100129T090000Z/20100129T100000Z
FREEBUSY;FBTYPE=BUSY:20100211T113000Z/20100211T123000Z
END:VFREEBUSY
END:VCALENDAR
EOF;
    $cal = Vpdi::decodeOne($sample);
    $busy = $cal->getBusyPeriods();
    $this->assertEquals(4, count($busy));
    $this->assertTrue($busy[0]->start == new DateTime('2010-03-07T09:00:00+0000'));
    $this->assertTrue($busy[0]->end == new DateTime('2010-03-07T10:00:00+0000'));
    $this->assertTrue($busy[3]->start == new DateTime('2010-02-11T11:30:00+0000'));
    $this->assertTrue($busy[3]->end == new DateTime('2010-02-11T12:30:00+0000'));
  }
  
  public function testBusyPeriodsWithGoogleCalendar() {
    $sample = <<<EOF
BEGIN:VCALENDAR
VERSION:2.0
CALSCALE:GREGORIAN
METHOD:REPLY
BEGIN:VFREEBUSY
DTSTART:20100307T090000Z
DTEND:20100307T100000Z
DTSTAMP:20100210T133910Z
UID:xxx@google.com
ATTENDEE;X-NUM-GUESTS=0:mailto:jdoe@gmail.com
SUMMARY:Busy
END:VFREEBUSY
BEGIN:VFREEBUSY
DTSTART:20100119T083000Z
DTEND:20100119T093000Z
DTSTAMP:20100210T133910Z
UID:xxx@google.com
ATTENDEE;X-NUM-GUESTS=0:mailto:jdoe@gmail.com
SUMMARY:Busy
END:VFREEBUSY
BEGIN:VFREEBUSY
DTSTART:20100211T113000Z
DTEND:20100211T123000Z
DTSTAMP:20100210T133910Z
UID:xxx@google.com
ATTENDEE;X-NUM-GUESTS=0:mailto:jdoe@gmail.com
SUMMARY:Busy
END:VFREEBUSY
END:VCALENDAR
EOF;
    $cal = Vpdi::decodeOne($sample);
    $busy = $cal->getBusyPeriods();
    $this->assertEquals(3, count($busy));
    $this->assertTrue($busy[0]->start == new DateTime('2010-03-07T09:00:00+0000'));
    $this->assertTrue($busy[0]->end == new DateTime('2010-03-07T10:00:00+0000'));
    $this->assertTrue($busy[2]->start == new DateTime('2010-02-11T11:30:00+0000'));
    $this->assertTrue($busy[2]->end == new DateTime('2010-02-11T12:30:00+0000'));
  }
  
  public function testBusyPeriodsWithinInterval() {
    $sample = <<<EOF
BEGIN:VCALENDAR
VERSION:2.0
CALSCALE:GREGORIAN
METHOD:REPLY
BEGIN:VFREEBUSY
DTSTAMP:20100212T124344Z
ORGANIZER:mailto:jdoe@test.com
DTSTART:20100112T124344Z
DTEND:20100312T124344Z
FREEBUSY;FBTYPE=BUSY:20100307T090000Z/20100307T110000Z
FREEBUSY;FBTYPE=BUSY:20100119T083000Z/20100119T093000Z
FREEBUSY;FBTYPE=BUSY:20100129T090000Z/20100129T100000Z
FREEBUSY;FBTYPE=BUSY:20100211T113000Z/20100211T123000Z
END:VFREEBUSY
END:VCALENDAR
EOF;
    $cal = Vpdi::decodeOne($sample);
    $busy = $cal->getBusyPeriodsWithinInterval(new DateTime('2010-02-01'), new DateTime('2010-02-28'));
    $this->assertEquals(1, count($busy));
    $this->assertTrue($busy[0]->start == new DateTime('2010-02-11T11:30:00+0000'));
    $this->assertTrue($busy[0]->end == new DateTime('2010-02-11T12:30:00+0000'));
    
    $busy = $cal->getBusyPeriodsWithinInterval(new DateTime('2010-01-19T00:00:00+0000'), new DateTime('2010-01-19T09:00:00+0000'));
    $this->assertEquals(1, count($busy));
    $this->assertTrue($busy[0]->start == new DateTime('2010-01-19T08:30:00+0000'));
    $this->assertTrue($busy[0]->end == new DateTime('2010-01-19T09:30:00+0000'));
    
    $busy = $cal->getBusyPeriodsWithinInterval(new DateTime('2010-01-19T09:00:00+0000'), new DateTime('2010-01-20T00:00:00+0000'));
    $this->assertEquals(1, count($busy));
    $this->assertTrue($busy[0]->start == new DateTime('2010-01-19T08:30:00+0000'));
    $this->assertTrue($busy[0]->end == new DateTime('2010-01-19T09:30:00+0000'));
    
    $busy = $cal->getBusyPeriodsWithinInterval(new DateTime('2010-03-07T09:30:00+0000'), new DateTime('2010-03-07T10:00:00+0000'));
    $this->assertEquals(1, count($busy));
    $this->assertTrue($busy[0]->start == new DateTime('2010-03-07T09:00:00+0000'));
    $this->assertTrue($busy[0]->end == new DateTime('2010-03-07T11:00:00+0000'));
  }
}