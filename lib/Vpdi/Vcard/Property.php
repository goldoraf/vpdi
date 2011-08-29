<?php

/**
 * vCard base property
 * 
 * Base class for properties with complex types, ie : Email, Phone, Address, Impp
 * 
 * @package Vpdi
 * @version $Id:$
 * @author Raphaël Rougeron <raphael.rougeron@gmail.com> 
 * @license GPL 2.0
 */
abstract class Vpdi_Vcard_Property {
  
  abstract public function addType($type);
  
  public function addTypes($types) {
    if (!is_array($types)) {
      $types = array($types);
    }
    foreach ($types as $type) {
      $this->addType($type);
    }
  }
}