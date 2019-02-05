<?php

namespace Sphec\Matchers;

abstract class Matcher {
  public function __construct($actual) {
    $this->actual = $actual;
  }

  abstract public function matches($expected);
  abstract public function failure_message();
  abstract public function failure_message_when_negated();

  protected function to_string($arg) {
    if (is_array($arg)) {
      return json_encode($arg);
    } else {
      return (string) $arg;
    }
  }
}