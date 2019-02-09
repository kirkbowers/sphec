<?php

namespace Sphec\Matchers;

class DoubleEqualMatcher extends Matcher {
  const ALIASES = ['equal', 'eq'];

  public function matches(...$args) {
    $this->expected = $args[0];
    return $this->actual == $this->expected;
  }

  public function failure_message() {
    // return "  expected\n" . $this->to_string($this->actual) . "\n  to be equal to\n" . $this->to_string($this->expected);
    return "Expected:\n  " . $this->to_string($this->actual) . "\nTo equal:\n  " . $this->to_string($this->expected) . "\n";
  }

  public function failure_message_when_negated() {
    return "  expected\n$this->actual\n  not to be equal to\n$this->expected";
  }
}