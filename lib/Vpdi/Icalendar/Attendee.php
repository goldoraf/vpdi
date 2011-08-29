<?php

/**
 * Represents an attendee of a calendar event
 * 
 * It is a property containing a CAL-ADDRESS value, with additional parameters
 * regarding the organizer property
 * 
 * Example : ATTENDEE;ROLE=REQ-PARTICIPANT;PARTSTAT=NEEDS-ACTION;
 *              CN="J. Doe";RSVP=TRUE:mailto:jdoe@example.com
 * 
 * @package Vpdi
 * @version $Id:$
 * @author Raphaël Rougeron <raphael.rougeron@gmail.com> 
 * @license GPL 2.0
 */
  
class Vpdi_Icalendar_Attendee extends Vpdi_Icalendar_Organizer {
  /**
   * Status of the attendee's participation
   * 
   * @var string
   */
  public $partstat;
  
  /**
   * Indicates whether the favor of a reply is requested
   * 
   * @var boolean
   */
  public $rsvp;
  
  /**
   * Type of calendar user
   * 
   * @var string
   */
  public $cutype;
  
  /**
   * Groups that the attendee belongs to
   * 
   * @var string
   */
  public $member;
  
  /**
   * Indicates that the original request was delegated to
   * 
   * @var string
   */
  public $delegatedTo;
  
  /**
   * Indicates whom the original request was delegated from
   * 
   * @var string
   */
  public $delegatedFrom;
  
  protected $propName = 'ATTENDEE';
  
  public static function decode(Vpdi_Property $ATTENDEE) {
    $org = new Vpdi_Icalendar_Attendee($ATTENDEE->value());
    $org->decodeParameters($ATTENDEE);
    return $org;
  }
  
  public function __construct($uri = '', $cn = null) {
    parent::__construct($uri, $cn);
    $this->paramMapping+= array(
      'partstat' => array('PARTSTAT', 'ParamText'),
      'rsvp' => array('RSVP', 'Boolean'),
      'cutype' => array('CUTYPE', 'ParamText'),
      'member' => array('MEMBER', 'TextList'), // a ParamValueList could be better...
      'delegatedTo' => array('DELEGATED-TO', 'TextList'),
      'delegatedFrom' => array('DELEGATED-FROM', 'TextList')
    );
  }
}