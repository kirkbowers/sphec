<?php


namespace Sphec\Matchers;

class FailedMatch {
  private $_failing_matcher;

  public function __construct($failing_matcher = null) {
    $this->_failing_matcher = $failing_matcher;
  }

  public function __invoke() {
    return false;
  }

  public function __toString() {
    return "failed match object";
  }

  public function __call($function, $args) {
    if ($this->_failing_matcher) {
      $this->_failing_matcher->$function(...$args);
    }
    return $this;
  }
}
