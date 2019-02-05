<?php

namespace Sphec\Matchers;

class TruthyMatcher extends Matcher {
  const ALIASES = ['be_truthy'];

  public function matches(...$args) {
    return !!$this->actual;
  }

  public function failure_message() {
    return "  expected\n" . $this->to_string($this->actual) . "\n  to be truthy";
  }

  public function failure_message_when_negated() {
    return "  expected\n$this->actual\n  not to be truthy";
  }
}