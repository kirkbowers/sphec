<?php

namespace Sphec\Matchers;

class IdenticalMatcher extends Matcher {
  const ALIASES = ['be', 'be_equivalent', 'be_equivalent_to', 'be_identical', 'be_identical_to'];

  public function matches($expected) {
    $this->expected = $expected;
    return $this->actual === $expected;
  }

  public function failure_message() {
    return "  expected\n$this->actual\n  to be identical to\n$this->expected";
  }

  public function failure_message_when_negated() {
    return "expected $this->actual not to be identical to $this->expected";
  }
}