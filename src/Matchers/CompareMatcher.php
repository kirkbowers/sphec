<?php

namespace Sphec\Matchers;

class CompareMatcher extends Matcher {
  const ALIASES = ['compare'];

  public function matches(...$args) {
    $this->comparitor = $args[0];
    $this->expected = $args[1];
    if ($this->comparitor == '>') {
      return $this->actual > $this->expected;
    } else if ($this->comparitor == '>=') {
      return $this->actual >= $this->expected;
    } else if ($this->comparitor == '<') {
      return $this->actual < $this->expected;
    } else if ($this->comparitor == '<=') {
      return $this->actual <= $this->expected;
    }

    return false;
  }

  public function failure_message() {
    return "  expected\n$this->actual\n  to be $this->comparitor\n$this->expected";
  }

  public function failure_message_when_negated() {
    return "  expected\n$this->actual\n  not to be $this->comparitor\n$this->expected";
  }
}