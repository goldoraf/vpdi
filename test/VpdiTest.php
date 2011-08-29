<?php

require_once dirname(__FILE__) . '/TestsHelper.php';
 
class VpdiTest extends PHPUnit_Framework_TestCase
{
  public function testTextListDecoding() {
    $this->assertEquals(Vpdi::decodeTextList('aaa,bbb,ccc'), array('aaa', 'bbb', 'ccc'));
    $this->assertEquals(Vpdi::decodeTextList('a\,aa,bbb,ccc'), array('a,aa', 'bbb', 'ccc'));
    $this->assertEquals(Vpdi::decodeTextList('a\,aa,bb\,b,ccc'), array('a,aa', 'bb,b', 'ccc'));
    $this->assertEquals(Vpdi::decodeTextList('a\,aa,bb\,b,\,ccc'), array('a,aa', 'bb,b', ',ccc'));
  }
  
  public function testTextListEncoding() {
    $this->assertEquals(Vpdi::encodeTextList(array('aaa', 'bbb', 'ccc')), 'aaa,bbb,ccc');
    $this->assertEquals(Vpdi::encodeTextList(array('a,aa', 'bbb', 'ccc')), 'a\,aa,bbb,ccc');
    $this->assertEquals(Vpdi::encodeTextList(array('a,aa', 'bb,b', 'ccc')), 'a\,aa,bb\,b,ccc');
    $this->assertEquals(Vpdi::encodeTextList(array('a,aa', 'bb,b', ',ccc')), 'a\,aa,bb\,b,\,ccc');
  }
  
  public function testParamTextEncoding() {
    $this->assertEquals('aaa', Vpdi::encodeParamText('aaa'));
    $this->setExpectedException('Vpdi_UnencodableException');
    Vpdi::encodeParamText('a;a:a');
  }
  
  public function testParamValueEncoding() {
    $this->assertEquals('aaa', Vpdi::encodeParamValue('aaa'));
    $this->assertEquals('"a;a:a"', Vpdi::encodeParamValue('a;a:a'));
    $this->setExpectedException('Vpdi_UnencodableException');
    Vpdi::encodeParamValue('"a;a:a');
  }
  
  public function testDateDecoding() {
    $this->assertTrue(Vpdi::decodeDate('1996-04-15') instanceof DateTime);
    $this->assertEquals('1996-04-15', Vpdi::decodeDate('1996-04-15')->format('Y-m-d'));
    $this->assertTrue(Vpdi::decodeDate('19960415') instanceof DateTime);
    $this->assertEquals('1996-04-15', Vpdi::decodeDate('19960415')->format('Y-m-d'));
  }
  
  public function testDateEncoding() {
    
  }
  
  public function testDateTimeDecoding() {
    $this->assertTrue(Vpdi::decodeDateTime('1953-10-15T23:10:00Z') instanceof DateTime);
    $this->assertEquals('1953-10-15T23:10:00+0000', Vpdi::decodeDateTime('1953-10-15T23:10:00Z')->format(DateTime::ISO8601));
    $this->assertTrue(Vpdi::decodeDateTime('19531015T231000Z') instanceof DateTime);
    $this->assertEquals('1953-10-15T23:10:00+0000', Vpdi::decodeDateTime('19531015T231000Z')->format(DateTime::ISO8601));
    $this->assertTrue(Vpdi::decodeDateTime('1987-09-27T08:30:00-06:00') instanceof DateTime);
    $this->assertEquals('1987-09-27T08:30:00-0600', Vpdi::decodeDateTime('1987-09-27T08:30:00-06:00')->format(DateTime::ISO8601));
  }
  
  public function testDateTimeEncoding() {
    date_default_timezone_set('UTC');
    $this->assertEquals('20101010T200000Z', Vpdi::encodeDateTime(new DateTime('2010-10-10 20:00:00')));
    date_default_timezone_set('Europe/Paris');
    $this->assertEquals('20101010T180000Z', Vpdi::encodeDateTime(new DateTime('2010-10-10 20:00:00')));
  }
  
  public function testTimezoneDecoding() {
    $this->assertTrue(Vpdi::decodeTimezone('US-Eastern') instanceof DateTimezone);
    $this->assertEquals('US/Eastern', Vpdi::decodeTimezone('US-Eastern')->getName());
  }
  
  public function testTimezoneEncoding() {
    $this->assertEquals('US-Eastern', Vpdi::encodeTimezone(new DateTimeZone('US/Eastern')));
  }
  
  public function testPeriodDecoding() {
    list($start, $end) = Vpdi::decodePeriod('19970308T230000Z/19970309T000000Z');
    $this->assertEquals('1997-03-08T23:00:00+0000', $start->format(DateTime::ISO8601));
    $this->assertEquals('1997-03-09T00:00:00+0000', $end->format(DateTime::ISO8601));
  }
  
  public function testPeriodEncoding() {
    date_default_timezone_set('UTC');
    $this->assertEquals('19970308T230000Z/19970309T000000Z', Vpdi::encodePeriod(
      new DateTime('1997-03-08 23:00:00'), new DateTime('1997-03-09 00:00:00')
    ));
  }
  
  public function testBooleanDecoding() {
    $this->assertTrue(Vpdi::decodeBoolean('TRUE'));
    $this->assertFalse(Vpdi::decodeBoolean('FALSE'));
  }
  
  public function testBooleanEncoding() {
    $this->assertEquals('TRUE', Vpdi::encodeBoolean(true));
    $this->assertEquals('TRUE', Vpdi::encodeBoolean('true'));
    $this->assertEquals('FALSE', Vpdi::encodeBoolean(false));
  }
  
  public function testRFC2425LineParser() {
    $this->assertEquals(Vpdi::decodeLine('BEGIN:VCARD'), 
      array('group' => null, 'name' => 'BEGIN', 'value' => 'VCARD', 'params' => array()));
    $this->assertEquals(Vpdi::decodeLine('FN:John Doe'), 
      array('group' => null, 'name' => 'FN', 'value' => 'John Doe', 'params' => array()));
    $this->assertEquals(Vpdi::decodeLine('ORG:Example.com Inc.;'), 
      array('group' => null, 'name' => 'ORG', 'value' => 'Example.com Inc.;', 'params' => array()));
    $this->assertEquals(Vpdi::decodeLine('N:Doe;John;;;'), 
      array('group' => null, 'name' => 'N', 'value' => 'Doe;John;;;', 'params' => array()));
    $this->assertEquals(Vpdi::decodeLine('TEL;type=CELL:+1 781 555 1212'), 
      array('group' => null, 'name' => 'TEL', 'value' => '+1 781 555 1212', 'params' => array('type' => 'CELL')));
    $this->assertEquals(Vpdi::decodeLine('TEL;type=work,voice,msg:+1 313 747-4454'), 
      array('group' => null, 'name' => 'TEL', 'value' => '+1 313 747-4454', 'params' => array('type' => array('work', 'voice', 'msg'))));
    $this->assertEquals(Vpdi::decodeLine('TEL;type=WORK;type=pref:+1 617 555 1212'), 
      array('group' => null, 'name' => 'TEL', 'value' => '+1 617 555 1212', 'params' => array('type' => array('WORK', 'pref'))));
    $this->assertEquals(Vpdi::decodeLine('item1.ADR;type=WORK:;;2 Example Avenue;Anytown;NY;01111;USA'), 
      array('group' => 'item1', 'name' => 'ADR', 'value' => ';;2 Example Avenue;Anytown;NY;01111;USA', 'params' => array('type' => 'WORK')));
  }
  
  public function testRFC2425Sample1() {
    $sample = <<<EOF
cn:  
cn:Babs Jensen
cn:Barbara J Jensen
sn:Jensen
email:babs@umich.edu
phone:+1 313 747-4454
x-id:1234567890
EOF;
    $props = Vpdi::decodeProperties($sample);
    
    $this->assertEquals('', $props[0]->value());
    $this->assertEquals('cn', $props[0]->name());
    $this->assertEquals('Babs Jensen', $props[1]->value());
    $this->assertEquals('cn', $props[1]->name());
    $this->assertEquals('Barbara J Jensen', $props[2]->value());
    $this->assertEquals('cn', $props[2]->name());
    $this->assertEquals('Jensen', $props[3]->value());
    $this->assertEquals('sn', $props[3]->name());
    $this->assertEquals('babs@umich.edu', $props[4]->value());
    $this->assertEquals('email', $props[4]->name());
    $this->assertEquals('+1 313 747-4454', $props[5]->value());
    $this->assertEquals('phone', $props[5]->name());
    $this->assertEquals('1234567890', $props[6]->value());
    $this->assertEquals('x-id', $props[6]->name());
  }
  
  public function testVpdiExpand() {
$sample = <<<EOF
BEGIN:a
k1:v1
BEGIN:b
BEGIN:c
k2:v2
k3:v3
END:c
k4:v4
k5:v5
END:b
k6:v6
END:a
BEGIN:d
k7:v7
END:d
BEGIN:e
k8:v8
BEGIN:f
k9:v9
END:f
END:e
EOF;
    $tree = Vpdi::decode($sample);
    
    $this->assertEquals('k1', $tree[0][0]->name());
    $this->assertEquals('v1', $tree[0][0]->value());
    $this->assertEquals('k2', $tree[0][1][0][0]->name());
    $this->assertEquals('v2', $tree[0][1][0][0]->value());
    $this->assertEquals('k3', $tree[0][1][0][1]->name());
    $this->assertEquals('v3', $tree[0][1][0][1]->value());
    $this->assertEquals('k4', $tree[0][1][1]->name());
    $this->assertEquals('v4', $tree[0][1][1]->value());
    $this->assertEquals('k5', $tree[0][1][2]->name());
    $this->assertEquals('v5', $tree[0][1][2]->value());
    $this->assertEquals('k6', $tree[0][2]->name());
    $this->assertEquals('v6', $tree[0][2]->value());
    $this->assertEquals('k7', $tree[1][0]->name());
    $this->assertEquals('v7', $tree[1][0]->value());
    $this->assertEquals('k8', $tree[2][0]->name());
    $this->assertEquals('v8', $tree[2][0]->value());
    $this->assertEquals('k9', $tree[2][1][0]->name());
    $this->assertEquals('v9', $tree[2][1][0]->value());
    
    $this->assertEquals('Vpdi_Entity', get_class($tree[0]));
    $this->assertEquals('Vpdi_Entity', get_class($tree[0][1]));
    $this->assertEquals('Vpdi_Entity', get_class($tree[0][1][0]));
    $this->assertEquals('Vpdi_Entity', get_class($tree[1]));
    $this->assertEquals('Vpdi_Entity', get_class($tree[2]));
    $this->assertEquals('Vpdi_Entity', get_class($tree[2][1]));
    
    $this->assertEquals('A', $tree[0]->profile());
    $this->assertEquals('B', $tree[0][1]->profile());
    $this->assertEquals('C', $tree[0][1][0]->profile());
    $this->assertEquals('D', $tree[1]->profile());
    $this->assertEquals('E', $tree[2]->profile());
    $this->assertEquals('F', $tree[2][1]->profile());
  }
  
  public function testVpdiExpandFailure() {
$sample = <<<EOF
BEGIN:a
k1:v1
BEGIN:b
k2:v2
END:c
END:a
EOF;
    $this->setExpectedException('Vpdi_BeginEndMismatchException');
    $tree = Vpdi::decode($sample);
  }
  
  public function testVpdiStrictDecodingFailure() {
    $sample = <<<EOF
BEGIN:a
k1:v1
END:a
EOF;
    $this->setExpectedException('Vpdi_UnexpectedEntityException');
    $tree = Vpdi::decode($sample, Vpdi::VCARD);
  }
  
  public function testVpdiSplit() {
$sample = <<<EOF
BEGIN:a
k1:v1
BEGIN:b
k2:v2
END:b
END:a
EOF;
    $tree = Vpdi::decode($sample);
    list($props, $entities) = Vpdi::split($tree[0]->getProperties());
    
    $this->assertEquals(1, count($props));
    $this->assertEquals('k1', $props[0]->name());
    $this->assertEquals('v1', $props[0]->value());
    $this->assertEquals(1, count($entities));
    $this->assertEquals('B', $entities[0]->profile());
  }
}
