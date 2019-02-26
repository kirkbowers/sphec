<?php

namespace Sphec;

class Allower {
  private $_double;
  private $_with = null;

  public function __construct($object) {
    // TODO:
    // Throw an exception if this isn't a mock?
    $this->_double = $object;
  }

  public function to_receive($name) {
    $this->_function_name = $name;
    $this->_double->__sphec_add_legal_function($name);
    return $this;
  }

  public function with(...$params) {
    $this->_with = $params;
    return $this;
  }

  public function and_return($value) {
    if (is_array($this->_with)) {
      $this->_double->__sphec_add_legal_function($this->_function_name, $this->_with, $value);
    } else {
      $this->_double->__sphec_add_legal_function($this->_function_name, $value);
    }
  }
}