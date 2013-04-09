<?php
/**
 * File: Person.php
 * Created by Vladimir Gerasimov (freelancervip
 * @gmail.com)
 * 14.03.12 15:29
 */
/***
 * Implements Persons attached to questionnaire
 * Persons maybe either Investigators or Data collectors
 */
abstract class Person
{
  /**
   * @var int Unique ID of person
   */
  private $id;
  /**
   * Name
   *
   * @var array
   */
  private $name = array(
    'salutation',
    'firstName',
    'lastName'
  );
  /**
   * @var string Organisation
   */
  private $organisation;
  /**
   * @var array Address
   */
  private $address = array(
    'street',
    'suburb',
    'postcode',
    'country'
  );
  /**
   * @var string Phone number
   */
  private $phoneNumber;
  /**
   * @var string Fax number
   */
  private $faxNumber;
  /**
   * @var string e-mail address
   */
  private $emailAddress;
  /**
   * @var string website
   */
  private $website;
  /**
   * @var array __get allowed members
   */
  private $_getters = array('name', 'organisation', 'address', 'phoneNumber', 'faxNumber', 'emailAddress', 'website');
  /**
   * @var array __set allowed setters
   */
  private $_setters = array('name', 'organisation', 'address', 'phoneNumber', 'faxNumber', 'emailAddress', 'website');

  /**
   * Provides access to allowed members
   *
   * @param string $method 'set' or 'get'
   * @param array $property properties
   * @param mixed $value value to be set
   * @return mixed Value of the member
   * @throws Exception
   */
  private function getAccess($method = 'get', $property, $value = null)
  {
    switch ($method) {
      case 'get':
        if (in_array($property, $this->_getters)) {
          return $this->$property;
        }
        else if (method_exists($this, '_get_' . $property))
          return call_user_func(array($this, '_get_' . $property));
        else if (in_array($property, $this->_getters) OR method_exists($this, '_set_' . $property))
          throw new Exception('Property "' . $property . '" is write-only.');
        else
          throw new Exception('Property "' . $property . '" is not accessible.');
        break;
      case 'set':
        if (in_array($property, $this->_setters) && isset($value)) {
          $this->$property = $value;
        }
        else if (method_exists($this, '_set_' . $property))
          call_user_func(array($this, '_set_' . $property), $value);
        else if (in_array($property, $this->_setters) OR method_exists($this, '_get_' . $property))
          throw new Exception('Property "' . $property . '" is read-only.');
        else
          throw new Exception('Property "' . $property . '" is not accessible.');
        break;
      default:
        throw new Exception('Unknown method');
    }
  }

  /**
   * __get implementation
   *
   * @param $property
   * @return mixed
   */
  public function __get($property)
  {
    return $this->getAccess('get', $property);
  }

  /**
   * __set implementation
   *
   * @param $property
   * @param $value
   */
  public function __set($property, $value)
  {
    $this->getAccess('set', $property, $value);
  }
  /**
   * @return int ID of person
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Default constructor
   *
   * @param array $name Name array
   * @param string|null $organisation Organisation
   * @param array|null $address Address array. If set, all address fields are mandatory
   * @param string|null $phoneNumber Phone number
   * @param string|null $faxNumber Fax number
   * @param string|null $emailAddress E-mail
   * @param string|null $website Web-site
   * @param int|null $id Unique ID
   */
  public function __construct(array $name, $organisation = null, array $address = null, $phoneNumber = null, $faxNumber = null, $emailAddress = null,
                              $website = null, $id = null)
  {
    if (!isset($name['firstName']) or !isset($name['lastName']))
      trigger_error("First and Last name must be filled", E_USER_ERROR);
    $this->name = $name;
    $this->organisation = $organisation;
    if (isset($address)) {
      if (!isset($address['street']) or !isset($address['suburb']) or !isset($address['postcode']) or !isset($address['country']))
        trigger_error("All address fields are mandatory", E_USER_ERROR);
    }
    $this->address = $address;
    $this->phoneNumber = $phoneNumber;
    $this->faxNumber = $faxNumber;
    $this->emailAddress = $emailAddress;
    $this->website = $website;
    $this->id = $id;
  }

  /**
   * Returns well-formatted queXML
   * @return string Well-formatted queXML string
   */
  public function getXML()
  {
    $person = '<name>';
    foreach ($this->name as $key => $value) {
      $person .= '<' . $key . '>' . $value . '</' . $key . '>';
    }
    $person .= '</name>';
    if (!empty($this->organisation))
      $person .= '<organisation>' . $this->organisation . '</organisation>';
    if (!empty($this->address)) {
      $person .= '<address>';
      foreach ($this->address as $key => $value) {
        $person .= '<' . $key . '>' . $value . '</' . $key . '>';
      }
      $person .= '</address>';
    }
    if (!empty($this->phoneNumber))
      $person .= '<phoneNumber>' . $this->phoneNumber . '</phoneNumber>';
    if (!empty($this->faxNumber))
      $person .= '<faxNumber>' . $this->faxNumber . '</faxNumber>';
    if (!empty($this->emailAddress))
      $person .= '<emailAddress>' . $this->emailAddress . '</emailAddress>';
    if (!empty($this->website))
      $person .= '<website>' . $this->emailAddress . '</website>';
    return $person;
  }
}
/**
 * Wrapper class for investigators
 */
class Investigator extends Person
{
  /**
   * Investigator constructor
   *
   * @param array $name Name array
   * @param string|null $organisation Organisation
   * @param array|null $address Address array. If set, all address fields are mandatory
   * @param string|null $phoneNumber Phone number
   * @param string|null $faxNumber Fax number
   * @param string|null $emailAddress E-mail
   * @param string|null $website Web-site
   * @param int|null $id Unique ID
   */
  public function __construct(array $name, $organisation = null, array $address = null, $phoneNumber = null, $faxNumber = null, $emailAddress = null,
                              $website = null, $id = null){
    parent::__construct($name, $organisation, $address, $phoneNumber, $faxNumber, $emailAddress, $website, $id);
  }
  /**
   * @return string Well-formatted queXML string
   */
  public function getXml()
  {
    $id = parent::getId();
    if ($id)
      $str = '<investigator id="' . $id . '">';
    else
      $str = '<investigator>';
    $str .= parent::getXML();
    $str .= '</investigator>';
    return $str;
  }
}
/**
 * Wrapper class for data collectors
 */
class DataCollector extends Person
{
  /**
   * DataCollector constructor
   *
   * @param array $name Name array
   * @param string|null $organisation Organisation
   * @param array|null $address Address array. If set, all address fields are mandatory
   * @param string|null $phoneNumber Phone number
   * @param string|null $faxNumber Fax number
   * @param string|null $emailAddress E-mail
   * @param string|null $website Web-site
   * @param null|int $id Unique ID
   */
  public function __construct(array $name, $organisation = null, array $address = null, $phoneNumber = null, $faxNumber = null, $emailAddress = null,
                              $website = null, $id = null){
    parent::__construct($name, $organisation, $address, $phoneNumber, $faxNumber, $emailAddress, $website, $id);
  }
  /**
   * @return string Well-formatted queXML string
   */
  public function getXml()
  {
    $id = parent::getId();
    if ($id)
      $str = '<dataCollector id="' . $id . '">';
    else
      $str = '<dataCollector>';
    $str .= parent::getXML();
    $str .= '</dataCollector>';
    return $str;
  }
}
