<?php
namespace Sphec\Reporters;

/**
 * Collects the reported test passes and fails.
 *
 * @author Kirk Bowers
 */
class AbstractReporter {
  public $passed = 0;
  public $failed = 0;
  protected $failures = array();

  public function report_context_start($name) { }
  public function report_context_end($name) { }
  protected function report_pass() { }
  protected function report_fail() { }
  public function report_final_summary() { }

  /**
   * Increments the number of passed tests.
   */
  public function pass() {
    $this->passed += 1;
    $this->report_pass();
  }

  /**
   * Increments the number of failed tests.
   */
  public function fail($label, $message) {
    $this->failed += 1;

    $this->failures[] = array(
      'label' => $label,
      'message' => $message
    );

    $this->report_fail();
  }
}