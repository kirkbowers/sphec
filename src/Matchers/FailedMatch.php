<?php


namespace Sphec\Matchers;

class FailedMatch {
  public function __invoke() {
    return false;
  }

  public function __toString() {
    return "failed match object";
  }

  public function __call($function, $args) {
    return $this;
  }
}
