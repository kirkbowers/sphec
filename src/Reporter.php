<?php
namespace Sphec;

/**
 * Collects the reported test passes and fails.
 *
 * @author Kirk Bowers
 */
class Reporter {
  public $passed = 0;
  public $failed = 0;
  private $failures = array();
  
  public $output = NULL;
  
  /**
   * @param $output An optional Symfony OutputInterface object for reporting to the 
   *    console.
   */
  function __construct($output = NULL) {
    $this->output = $output;  
  }
  
  /**
   * Resets all reported counts to zero.
   */
  public function reset() {
    $this->passed = 0;
    $this->failed = 0;
    $this->failures = array();
  }
  
  /**
   * Takes the reported values in the incoming Reporter and adds them to this one's 
   *   values.
   *
   * @param $reporter Another Reporter to combine into this one.
   */
  public function combine($reporter) {
    $this->passed += $reporter->passed;
    $this->failed += $reporter->failed;
  }
  
  /**
   * Increments the number of passed tests.
   */
  public function pass() {
    if ($this->output && !$this->output->isQuiet()) {
      $this->output->write(".");
    }
    $this->passed += 1;
  }
  
  /**
   * Increments the number of failed tests.
   */
  public function fail($label, $computed, $test, $expected) {
    if ($this->output && !$this->output->isQuiet()) {
      $this->output->write("<fg=red>F</fg=red>");
    }
    $this->failed += 1;
    
    $this->failures[] = array(
      'label'    => $label,
      'computed' => $computed,
      'test'     => $test,
      'expected' => $expected
    );
  }
  
  public function report_failures() {
    if ($this->output && !$this->output->isQuiet()) {
      if (count($this->failures) > 0) {
        $this->output->writeln("");      
      }
    
      foreach ($this->failures as $failure) {
        $this->output->writeln("<fg=red>Failed:</fg=red> " . $failure['label']);
        $this->output->writeln("Computed:");
        $this->output->writeln(var_export($failure['computed'], true));
        $this->output->write("Expected " . $failure['test']);
        if (isset($failure['expected'])) {
          $this->output->writeln(':');
          $this->output->writeln(var_export($failure['expected'], true));
        } else {
          $this->output->writeln('.');
        }
        $this->output->writeln("");
      }
    }  
  }
}