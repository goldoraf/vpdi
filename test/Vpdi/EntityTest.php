<?php

require_once dirname(__FILE__) . '/../TestsHelper.php';
 
class Vpdi_EntityTest extends PHPUnit_Framework_TestCase
{
  private $e;
  
  private $entity = 
"BEGIN:a
k1:
k1:v1
k1:v11
k2;encoding=b:aGVsbG8gd29ybGQ=
END:a";
  
  public function setup() {
    $entities = Vpdi::decode($this->entity);
    $this->e = $entities[0];
  }
  
  public function testGetPropertiesByName() {
    $k1 = $this->e->getPropertiesByName('k1');
    $this->assertEquals('', $k1[0]->value());
    $this->assertEquals('v1', $k1[1]->value());
    $this->assertEquals('v11', $k1[2]->value());
  }
  
  public function testGetProperty() {
    $k1 = $this->e->getProperty('k1');
    $this->assertEquals('v1', $k1->value());
  }
  
  public function testGetRawValue() {
    $this->assertEquals('v1', $this->e->getRawValue('k1'));
    $this->assertEquals('aGVsbG8gd29ybGQ=', $this->e->getRawValue('k2'));
  }
  
  public function testGetValue() {
    $this->assertEquals('v1', $this->e->getValue('k1'));
    $this->assertEquals('hello world', $this->e->getValue('k2'));
  }
  
  public function testMagicGetter() {
    $this->assertEquals('v1', $this->e->k1);
    $this->assertEquals('hello world', $this->e->k2);
  }
  
  public function testMagicSetter() {
    $this->e->k3 = 'test';
    $this->assertEquals('test', $this->e->k3);
  }
}