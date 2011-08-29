<?php

/**
 * Defines overall status possible values for a calendar component
 * 
 * @package Vpdi
 * @version $Id:$
 * @author Raphaël Rougeron <raphael.rougeron@gmail.com> 
 * @license GPL 2.0
 */
  
class Vpdi_Icalendar_Status {
  
  /**
   * For "VEVENT"
   */
  const TENTATIVE = 'TENTATIVE';
  
  const CONFIRMED = 'CONFIRMED';
  
  /**
   * For "VTODO"
   */
  const NEEDS_ACTION = 'NEEDS-ACTION';
  
  const COMPLETED = 'COMPLETED';
  
  const IN_PROCESS = 'IN-PROCESS';
  
  /**
   * For "VJOURNAL"
   */
  const DRAFT = 'DRAFT';
  
  const _FINAL = 'FINAL'; // ....
  
  /**
   * Shared
   */
  const CANCELLED = 'CANCELLED';
}