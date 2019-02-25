<?php

namespace Sphec\Matchers;

class ThrowsMatcher extends Matcher {
  const ALIASES = ['throw'];

  private $but_threw = '';

  public function matches(...$args) {
    $expected = null;
    if (sizeof($args) > 0) {
      $expected = $args[0];
      $this->expected = $expected;
    } else {
      $this->expected = 'any exception';
    }
    $result = false;

    if (is_callable($this->actual)) {
      try {
        $this->actual->__invoke();
      } catch (\Exception $e) {
        if (!$expected) {
          $result = true;
        } else if (is_a($e, $expected)) {
          $result = true;
        } else {
          $this->but_threw = get_class($e);
        }
      }
    }

    return $result;
  }

  public function failure_message() {
    return "  expected to throw $this->expected" . $this->but_threw;
  }

  public function failure_message_when_negated() {
    return "  expected not to throw $this->expected" . $this->but_threw;
  }
}