<?php

namespace TestProject;

/**
 * This is a trivial little class for testing sphec on the command line.
 */
class Accumulator {
  private $value;
  
  function __construct($value = 0) {
    $this->value = $value;
  }
  
  public function add($addend) {
    $this->value += $addend;
  }
  
  public function get_value() {
    return $this->value;
  }
}
