<?php

/**
 * Defines participation status possible values
 * 
 * @package Vpdi
 * @version $Id:$
 * @author Raphaël Rougeron <raphael.rougeron@gmail.com> 
 * @license GPL 2.0
 */
  
class Vpdi_Icalendar_Partstat {
  
  const NEEDS_ACTION = 'NEEDS-ACTION';
  
  const ACCEPTED = 'ACCEPTED';
  
  const DECLINED = 'DECLINED';
  
  const TENTATIVE = 'TENTATIVE';
  
  const DELEGATED = 'DELEGATED';
  
  const COMPLETED = 'COMPLETED'; // only for VTodos
  
  const IN_PROCESS = 'IN-PROCESS'; // only for VTodos
}