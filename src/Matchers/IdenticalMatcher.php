<?php

namespace Sphec\Matchers;

class IdenticalMatcher extends Matcher {
  const ALIASES = ['be', 'be_equivalent', 'be_equivalent_to', 'be_identical', 'be_identical_to'];

  public function matches(...$args) {
    $this->expected = $args[0];
    return $this->actual === $this->expected;
  }

  public function failure_message() {
    return "  expected\n$this->actual\n  to be identical to\n$this->expected";
  }

  public function failure_message_when_negated() {
    return "  expected\n$this->actual\n  not to be identical to\n$this->expected";
  }
}