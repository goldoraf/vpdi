<?php

require_once dirname(__FILE__) . '/../../TestsHelper.php';
 
class Vpdi_Icalendar_VfreebusyTest extends PHPUnit_Framework_TestCase
{
  public function testRfcPublishSample() {
    $sample = <<<EOF
BEGIN:VFREEBUSY
ORGANIZER:jsmith@host.com
DTSTART:19980313T141711Z
DTEND:19980410T141711Z
FREEBUSY:19980314T233000Z/19980315T003000Z
FREEBUSY:19980316T153000Z/19980316T163000Z
FREEBUSY:19980318T030000Z/19980318T040000Z
URL:http://www.host.com/calendar/busytime/jsmith.ifb
END:VFREEBUSY
EOF;
    date_default_timezone_set('Europe/Paris');
    $vfb = Vpdi::decodeOne($sample);
    
    $this->assertTrue($vfb instanceof Vpdi_Icalendar_Vfreebusy);
    
    $this->assertEquals('jsmith@host.com', $vfb->organizer->uri);
    $this->assertEquals('1998-03-13T14:17:11+0000', $vfb->dtstart->format(DateTime::ISO8601));
    $this->assertEquals('1998-04-10T14:17:11+0000', $vfb->dtend->format(DateTime::ISO8601));
    
    $this->assertEquals(3, count($vfb->freebusys));
    $this->assertEquals('1998-03-14T23:30:00+0000', $vfb->freebusys[0]->start->format(DateTime::ISO8601));
    $this->assertEquals('1998-03-15T00:30:00+0000', $vfb->freebusys[0]->end->format(DateTime::ISO8601));
    $this->assertEquals('BUSY', $vfb->freebusys[0]->type);
  }
  
  public function testFreebusyListValues() {
    $sample = <<<EOF
BEGIN:VFREEBUSY
DTSTART:19980313T141711Z
DTEND:19980410T141711Z
FREEBUSY:19980314T233000Z/19980315T003000Z
FREEBUSY:19980316T153000Z/19980316T163000Z,19970308T230000Z/19970309T000000Z
END:VFREEBUSY
EOF;
    date_default_timezone_set('Europe/Paris');
    $vfb = Vpdi::decodeOne($sample);
    
    $this->assertEquals(3, count($vfb->freebusys));
  }
  
  public function testCompleteSample() {
    $sample = <<<EOF
BEGIN:VFREEBUSY
DTSTAMP:20100212T124344Z
ORGANIZER:mailto:adrien@test.tlse.lng
DTSTART:20100112T124344Z
DTEND:20100312T124344Z
ATTENDEE;CUTYPE=INDIVIDUAL;RSVP=TRUE;CN=adrien@test.tlse.lng;ROLE=OPT-PARTICIPANT:mailto:adrien@test.tlse.lng
FREEBUSY;FBTYPE=BUSY:20100307T090000Z/20100307T100000Z
FREEBUSY;FBTYPE=BUSY:20100119T083000Z/20100119T093000Z
FREEBUSY;FBTYPE=BUSY:20100129T090000Z/20100129T100000Z
FREEBUSY;FBTYPE=BUSY:20100211T113000Z/20100211T123000Z
FREEBUSY;FBTYPE=BUSY:20100120T100000Z/20100120T110000Z
FREEBUSY;FBTYPE=BUSY:20100127T090000Z/20100127T100000Z
FREEBUSY;FBTYPE=BUSY:20100216T090000Z/20100216T100000Z
FREEBUSY;FBTYPE=BUSY:20100218T090000Z/20100218T100000Z
FREEBUSY;FBTYPE=BUSY:20100207T090000Z/20100207T100000Z
FREEBUSY;FBTYPE=BUSY:20100204T090000Z/20100204T100000Z
FREEBUSY;FBTYPE=BUSY:20100119T130000Z/20100119T140000Z
FREEBUSY;FBTYPE=BUSY:20100201T090000Z/20100201T100000Z
FREEBUSY;FBTYPE=BUSY:20100211T090000Z/20100211T100000Z
FREEBUSY;FBTYPE=BUSY:20100220T090000Z/20100220T100000Z
FREEBUSY;FBTYPE=BUSY:20100121T140000Z/20100121T150000Z
END:VFREEBUSY
EOF;
    
  }
}