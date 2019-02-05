<?php

namespace Sphec\Matchers;

class DoubleEqualMatcher extends Matcher {
  const ALIASES = ['equal', 'eq'];

  public function matches($expected) {
    $this->expected = $expected;
    return $this->actual == $expected;
  }

  public function failure_message() {
    // return "  expected\n" . $this->to_string($this->actual) . "\n  to be equal to\n" . $this->to_string($this->expected);
    return "  expected\n" . $this->to_string($this->actual);
  }

  public function failure_message_when_negated() {
    return "  expected\n$this->actual\n  not to be equal to\n$this->expected";
  }
}