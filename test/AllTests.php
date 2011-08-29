<?php

require_once dirname(__FILE__) . '/TestsHelper.php';
 
class AllTests {
  public static function suite() {
    $suite = new PHPUnit_Framework_TestSuite('Vpdi');
    $suite->addTestSuite('VpdiTest');
    $suite->addTestSuite('Vpdi_EntityTest');
    $suite->addTestSuite('Vpdi_VcardTest');
    $suite->addTestSuite('Vpdi_Icalendar_VeventTest');
    $suite->addTestSuite('Vpdi_Icalendar_VfreebusyTest');
    $suite->addTestSuite('Vpdi_IcalendarTest');
    return $suite;
  }
}