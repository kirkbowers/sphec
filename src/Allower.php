<?php

namespace Sphec;

class Allower {
  private $_double;

  public function __construct($object) {
    if ($object instanceof \Sphec\Mocks\Double) {
      $this->_double = $object;
    } else {
      // TODO:
      // Create a spy double
    }
  }

  public function to_receive($name) {
    $this->_function_name = $name;
    $this->_double->__sphec_add_legal_function($name);
    return $this;
  }

  public function and_return($value) {
    $this->_double->__sphec_add_legal_function($this->_function_name, $value);
  }
}