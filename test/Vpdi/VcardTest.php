<?php

require_once dirname(__FILE__) . '/../TestsHelper.php';
 
class Vpdi_VcardTest extends PHPUnit_Framework_TestCase
{  
  public function testRFC2425Sample2() {
    $sample = <<<EOF
BEGIN:VCARD
source:ldap://cn=bjorn%20Jensen, o=university%20of%20Michigan, c=US
name:Bjorn Jensen
fn:Bj=F8rn Jensen
n:Jensen;Bj=F8rn
email;TYPE=internet:bjorn@umich.edu
tel;TYPE=work,voice,msg:+1 313 747-4454
key;TYPE=x509;ENCODING=B:dGhpcyBjb3VsZCBiZSAKbXkgY2VydGlmaWNhdGUK
END:VCARD
EOF;
    $card = Vpdi::decodeOne($sample);
    
    Vpdi::setConfig('always_encode_in_upper_case', false);
    $this->assertEquals($sample, $card->__toString());
    Vpdi::setConfig('always_encode_in_upper_case', true);
    
    $this->assertEquals('bjorn@umich.edu', $card->getValue('email'));
    $this->assertEquals('bjorn@umich.edu', $card->getValue('eMaiL'));
    $this->assertEquals('+1 313 747-4454', $card->getValue('tel'));
    $this->assertEquals('+1 313 747-4454', $card->getValue('tel', 'voice'));
    $this->assertEquals('+1 313 747-4454', $card->getValue('tEl', 'vOicE'));
    
    $tel_entries = $card->getPropertiesByName('tel');
    $this->assertEquals(null, $tel_entries[0]->encoding());
    $key_entries = $card->getPropertiesByName('key');
    $this->assertEquals('B', $key_entries[0]->encoding());
    
    $this->assertEquals('dGhpcyBjb3VsZCBiZSAKbXkgY2VydGlmaWNhdGUK', $card->getRawValue('key'));
    $this->assertEquals("this could be \nmy certificate\n", $card->getValue('key'));
    
    $this->assertEquals('Bj=F8rn Jensen', $card->name->fullname);
    $this->assertEquals('Bj=F8rn Jensen', $card->getValue('FN'));
    $this->assertEquals('Jensen', $card->name->family);
    $this->assertEquals('Bj=F8rn', $card->name->given);
    $this->assertEquals('', $card->name->prefixes);
    $this->assertEquals('+1 313 747-4454', $card->tel);
    $this->assertEquals('+1 313 747-4454', $card->phone->value);
    $this->assertEquals(array('work'), $card->phone->location);
    $this->assertEquals(array('voice', 'msg'), $card->phone->capability);
    $this->assertEquals('bjorn@umich.edu', $card->email->value);
    
    $this->assertNull($card->bday);
    $this->assertNull($card->address);
    $this->assertNull($card->impp);
  }
  
  public function testW3CSample() {
    $sample = <<<EOF
BEGIN:VCARD
VERSION:3.0
N:Doe;John;;;
FN:John Doe
ORG:Example.com Inc.;
TITLE:Imaginary test person
EMAIL;TYPE=INTERNET;TYPE=WORK;TYPE=pref:johnDoe@example.org
TEL;TYPE=WORK;TYPE=pref:+1 617 555 1212
TEL;TYPE=CELL:+1 781 555 1212
TEL;TYPE=HOME:+1 202 555 1212
TEL;TYPE=WORK:+1 (617) 555-1234
item1.ADR;TYPE=WORK:;;2 Example Avenue;Anytown;NY;01111;USA
item1.X-ABADR:us
item2.ADR;TYPE=HOME;TYPE=pref:;;3 Acacia Avenue;Newtown;MA;02222;USA
item2.X-ABADR:us
NOTE:John Doe has a long and varied history\, being documented on more police files that anyone else. Reports of his death are alas numerous.
item3.URL;TYPE=pref:http\://www.example/com/doe
item3.X-ABLabel:_$!<HomePage>!\$_
item4.URL:http\://www.example.com/Joe/foaf.df
item4.X-ABLABEL:FOAF
item5.X-ABRELATEDNAMES;TYPE=pref:Jane Doe
item5.X-ABLabel:_$!<Friend>!\$_
CATEGORIES:Work,Test group
X-ABUID:5AD380FD-B2DE-4261-BA99-DE1D1DB52FBE\:ABPerson
END:VCARD
EOF;
    $card = Vpdi::decodeOne($sample);
    
    Vpdi::setConfig('type_values_as_a_parameter_list', true);
    Vpdi::setConfig('always_encode_in_upper_case', false);
    $this->assertEquals($sample, $card->__toString());
    Vpdi::setConfig('always_encode_in_upper_case', true);
    
    $this->assertEquals('+1 781 555 1212', $card->getValue('tel', 'cell'));
    $this->assertEquals('+1 202 555 1212', $card->getValue('tel', 'home'));
    $this->assertEquals('+1 617 555 1212', $card->getValue('tel', 'work'));
    
    $this->assertEquals('John Doe', $card->name->fullname);
    $this->assertEquals('Doe', $card->name->family);
    $this->assertEquals('John', $card->name->given);
    $this->assertEquals('', $card->name->prefixes);
    
    $this->assertEquals('', $card->addresses[0]->pobox);
    $this->assertEquals('', $card->addresses[0]->extended);
    $this->assertEquals('2 Example Avenue', $card->addresses[0]->street);
    $this->assertEquals('Anytown', $card->addresses[0]->locality);
    $this->assertEquals('NY', $card->addresses[0]->region);
    $this->assertEquals('01111', $card->addresses[0]->postalcode);
    $this->assertEquals('USA', $card->addresses[0]->country);
    $this->assertFalse($card->addresses[0]->preferred);
    $this->assertEquals(array('work'), $card->addresses[0]->location);
    $this->assertEquals('+1 617 555 1212', $card->phone->value);
    $this->assertEquals('+1 617 555 1212', $card->getPhone('work')->value);
    $this->assertEquals('johnDoe@example.org', $card->email->value);
    $this->assertEquals(array('work'), $card->email->location);
    $this->assertEquals('johnDoe@example.org', $card->emails[0]->value);
    $this->assertEquals(array('work'), $card->emails[0]->location);
  }
  
  public function testDates() {
    $sample = <<<EOF
BEGIN:VCARD
VERSION:3.0
N:Doe;John;;;
FN:John Doe
BDAY:1996-04-15
END:VCARD
EOF;
    $card = Vpdi::decodeOne($sample);
    $this->assertEquals('15/04/1996', $card->bday->format('d/m/Y'));
  }
  
  public function testImpp() {
    $sample = <<<EOF
BEGIN:VCARD
VERSION:3.0
N:Doe;John;;;
FN:John Doe
IMPP;TYPE=personal,pref:ymsgr:john@example.com
END:VCARD
EOF;
    $card = Vpdi::decodeOne($sample);
    $this->assertEquals('John Doe', $card->name->fullname);
    $this->assertEquals('ymsgr:john@example.com', $card->impp->value);
    $this->assertEquals('personal', $card->impp->purpose[0]);
  }
  
  public function testBasicVcardCreation() {
    $sample = <<<EOF
BEGIN:VCARD
N:Rougeron;Raphaël;;;
FN:Rougeron Raphaël
EMAIL;TYPE=home;TYPE=pref:raphael@myhost.com
TEL;TYPE=home:+336666666
END:VCARD
EOF;
    
    $card = new Vpdi_Vcard();
    $card[] = new Vpdi_Property('n', 'Rougeron;Raphaël;;;');
    $card[] = new Vpdi_Property('fn', 'Rougeron Raphaël');
    $card[] = new Vpdi_Property('email', 'raphael@myhost.com', array('type' => array('home', 'pref')));
    $card[] = new Vpdi_Property('tel', '+336666666', array('type' => 'home'));
    
    $this->assertEquals($sample, $card->__toString());
  }
  
  public function testAdvancedVcardCreation() {
    $sample = <<<EOF
BEGIN:VCARD
N:Rougeron;Raphaël;;;
FN:Rougeron Raphaël
EMAIL;TYPE=home;TYPE=internet;TYPE=pref:raphael@myhost.com
TEL;TYPE=home:+336666666
ADR;TYPE=work:;;4 rue Giotto;Toulouse;Haute-Garonne;31000;FRANCE
END:VCARD
EOF;
    
    Vpdi::setConfig('type_values_as_a_parameter_list', true);
    
    $name = new Vpdi_Vcard_Name();
    $name->family = 'Rougeron';
    $name->given  = 'Raphaël';
    
    $phone = new Vpdi_Vcard_Phone('+336666666');
    $phone->location[] = 'home';
    
    $email = new Vpdi_Vcard_Email('raphael@myhost.com');
    $email->location[] = 'home';
    $email->preferred = true;
    
    $add = new Vpdi_Vcard_Address();
    $add->street = '4 rue Giotto';
    $add->locality = 'Toulouse';
    $add->region = 'Haute-Garonne';
    $add->postalcode = '31000';
    $add->country = 'FRANCE';
    $add->location[] = 'work';
    
    $card = new Vpdi_Vcard();
    $card->setName($name);
    $card->addEmail($email);
    $card->addPhone($phone);
    $card->addAddress($add);
    
    $this->assertEquals($sample, $card->__toString());
  }
  
  public function testQuickVcardCreation() {
    $sample = <<<EOF
BEGIN:VCARD
N:Rougeron;Raphaël;;;
FN:Raphaël Rougeron
EMAIL;TYPE=internet:raphael@myhost.com
TEL:+336666666
ADR:;;4 rue Giotto;Toulouse;;31000;FRANCE
END:VCARD
EOF;
    
    Vpdi::setConfig('type_values_as_a_parameter_list', true);
    
    $card = new Vpdi_Vcard();
    $card->name = new Vpdi_Vcard_Name('Raphaël Rougeron');
    $card->email = new Vpdi_Vcard_Email('raphael@myhost.com');
    $card->phone = new Vpdi_Vcard_Phone('+336666666');
    $card->address = new Vpdi_Vcard_Address(array('street' => '4 rue Giotto', 'locality' => 'Toulouse', 
                                                  'postalcode' => '31000', 'country' => 'FRANCE'));
    
    $this->assertEquals($sample, $card->__toString());
  }
}