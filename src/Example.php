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
      // if (Sphec::get_reporter()->output && Sphec::get_reporter()->output->isVerbose()) {
      //   Sphec::get_reporter()->output->writeln($this->_indent . $this->_label);
      //   Sphec::get_reporter()->output->write($this->_indent);
      // }
      $this->_block->__invoke($this);
      // if (Sphec::get_reporter()->output && Sphec::get_reporter()->output->isVerbose()) {
      //   Sphec::get_reporter()->output->writeln('');
      // }
      Sphec::get_reporter()->pass($this->_label);
    } catch (\Sphec\FailedMatchException $e) {
      Sphec::get_reporter()->fail($this->_label, $this->get_full_name(), $e->getMessage());
    } catch (\Sphec\Mocks\UnstubbedMethodException $e) {
      throw $e;
    } catch (\Exception $e) {
      // TODO:
      // Report it as an error
      throw $e;
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
