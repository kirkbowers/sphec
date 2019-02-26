<?php
namespace Sphec;

/**
 * A holder of a lazily runnable block of code that contains `expect` tests.
 *
 * @author Kirk Bowers
 */
class Example extends Runnable {
  function __construct($label, $block, $indent, $parent) {
    parent::__construct($label, $block, $indent, $parent);
  }

  /**
   * Creates an expectation that can be tested.
   *
   * @param $value The calculated value to be compared to an expected value.
   */
  public function expect($value) {
    // return Sphec::get_reporter()->test($value, $this);
    return new Tester($value, Sphec::get_reporter(), $this);
  }

  public function allow($value) {
    return new Allower($value);
  }

  public function run() {
    $this->_parent->run_befores($this);

    try {
      $this->_block->__invoke($this);
      Sphec::get_reporter()->pass($this->_label);
    } catch (\Sphec\FailedMatchException $e) {
      Sphec::get_reporter()->fail($this->_label, $this->get_full_name(), $e->getMessage());
    } catch (\Sphec\Mocks\UnstubbedMethodException $e) {
      Sphec::get_reporter()->fail($this->_label, $this->get_full_name(), $e->getMessage());
    } catch (\Exception $e) {
      Sphec::get_reporter()->fail($this->_label, $this->get_full_name(),
        "Error!! " . get_class($e) . " thrown: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    } finally {
      $this->_parent->run_afters($this);
    }
  }

  public function __get($variable) {
    if (!property_exists($this, $variable)) {
      $this->$variable = $this->_parent->get_lazy_variable($this, $variable);
    }

    return $this->$variable;
  }
}
