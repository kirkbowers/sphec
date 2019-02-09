<?php

namespace Sphec\Matchers;

class HaveReceivedMatcher extends Matcher {
  const ALIASES = ['have_received'];

  private $expected;
  private $times_message;
  private $call_count;

  public function matches(...$args) {
    $this->expected = $args[0];
    $this->expected_times_message = "at least once";
    $this->call_count = $this->actual->__sphec_function_call_count($this->expected);
    if ($this->call_count > 0) {
      return $this;
    } else {
      return new FailedMatch($this);
    }
  }

  public function failure_message() {
    $times = $this->call_count == 1 ? "time" : "times";
    return "Expected " . $this->to_string($this->actual) . " to have received " . $this->expected . "() "
      . $this->expected_times_message . " but was called $this->call_count $times.";
  }

  public function failure_message_when_negated() {
    return "Expected $this->actual not to have received " . $this->expected . "().";
  }

  //-------------------------------------------------------------------------------------------
  // Chainable methods

  public function once() {
    $this->expected_times_message = "once";
    if ($this->call_count == 1) {
      return $this;
    } else {
      return new FailedMatch($this);
    }
  }

  public function twice() {
    $this->expected_times_message = "twice";
    if ($this->call_count == 2) {
      return $this;
    } else {
      return new FailedMatch($this);
    }
  }

  public function exactly($times) {
    $this->expected_times_message = "exactly $times times";
    if ($this->call_count == $times) {
      return $this;
    } else {
      return new FailedMatch($this);
    }
  }

  public function times() {
    // No-op for syntactic sugar
    return $this;
  }
}
