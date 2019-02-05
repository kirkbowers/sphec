<?php

namespace Sphec\Matchers;

class FalseyMatcher extends Matcher {
  const ALIASES = ['be_falsey', 'be_falsy'];

  public function matches($expected = null) {
    return !$this->actual;
  }

  public function failure_message() {
    return "  expected\n" . $this->to_string($this->actual) . "\n  to be falsey";
  }

  public function failure_message_when_negated() {
    return "  expected\n$this->actual\n  not to be falsey";
  }
}