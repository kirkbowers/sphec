<?php

namespace Sphec\Matchers;

class HaveReceivedMatcher extends Matcher {
  const ALIASES = ['have_received'];

  private $expected;
  private $times_message;
  private $call_count;

  public function matches(...$args) {
    $this->expected = $args[0];
    $this->times_message = "at least once but it was never called.";
    $this->call_count = $this->actual->__sphec_function_call_count($this->expected);
    if ($this->call_count > 0) {
      return $this;
    } else {
      return new FailedMatch;
    }
  }

  public function failure_message() {
    return "expected " . $this->to_string($this->actual) . " to have received " . $this->expected . "() "
      . $this->times_message;
  }

  public function failure_message_when_negated() {
    return "  expected\n$this->actual\n  not to have received " . $this->expected . "()";
  }

  //-------------------------------------------------------------------------------------------
  // Chainable methods

  public function once() {
    if ($this->call_count == 1) {
      return $this;
    } else {
      return new FailedMatch;
    }
  }
}
