<?php

/**
 * Represents a freebusy period
 * 
 * @package Vpdi
 * @version $Id:$
 * @author Raphaël Rougeron <raphael.rougeron@gmail.com> 
 * @license GPL 2.0
 */
  
class Vpdi_Icalendar_Freebusy {
  
  const FREE = 'FREE';
  
  const BUSY = 'BUSY';
  
  const BUSY_UNAVAILABLE = 'BUSY-UNAVAILABLE';
  
  const BUSY_TENTATIVE = 'BUSY-TENTATIVE';
  
  public $start;
  
  public $end;
  
  public $duration;
  
  public $type;
  
  public static function decode(Vpdi_Property $FREEBUSY) {
    $fbs = array();
    $periods = Vpdi::decodeTextList($FREEBUSY->value());
    foreach ($periods as $p) {
      list($start, $end) = Vpdi::decodePeriod($p);
      $fbs[] = new Vpdi_Icalendar_Freebusy($start, $end);
    }
    if (count($fbs) == 1) return $fbs[0];
    return $fbs;
  }
  
  public function __construct(DateTime $start, DateTime $end) {
    $this->start = $start;
    $this->end = $end;
    $this->duration = $this->end->format('U') - $this->start->format('U');
    $this->type = self::BUSY;
  }
  
  public function __toString() {
    return $this->encode()->__toString();
  }
  
  public function encode() {
    return new Vpdi_Property('FREEBUSY', Vpdi::encodePeriod($this->start, $this->end), array('FBTYPE' => $this->type));
  }
}