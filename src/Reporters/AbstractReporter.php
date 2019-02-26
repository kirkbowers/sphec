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
  protected function report_pass($label) { }
  protected function report_fail($label) { }
  public function report_final_summary() { }

  /**
   * Increments the number of passed tests.
   */
  public function pass($label) {
    $this->passed += 1;
    $this->report_pass($label);
  }

  /**
   * Increments the number of failed tests.
   */
  public function fail($label, $full_name, $message) {
    $this->failed += 1;

    $this->failures[] = array(
      'label' => $full_name,
      'message' => $message
    );

    $this->report_fail($label);
  }
}