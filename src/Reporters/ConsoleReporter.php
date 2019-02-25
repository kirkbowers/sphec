<?php
namespace Sphec\Reporters;

class ConsoleReporter extends AbstractReporter {
  private $output;

  /**
   * @param $output An optional Symfony OutputInterface object for reporting to the
   *    console.
   */
  function __construct($output) {
    $this->output = $output;
  }

  protected function get_output() {
    return $this->output;
  }

  public function report_final_summary() {
    $this->output->writeln("");

    $this->report_failures();

    $this->output->writeln("Successes: " . $this->passed . ", Failures: " . $this->failed);

    if ($this->failed == 0) {
      $this->output->writeln("<fg=green>Success!</fg=green>");
    } else {
      $this->output->writeln("<fg=red>Failure!</fg=red>");
    }
  }

  public function report_failures() {
    if (count($this->failures) > 0) {
      $this->output->writeln("");
    }

    foreach ($this->failures as $failure) {
      $this->output->writeln("<fg=red>Failed:</fg=red> " . $failure['label']);
      $this->output->writeln($failure['message']);
    }
  }
}
