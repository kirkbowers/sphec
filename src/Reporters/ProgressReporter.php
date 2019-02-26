<?php
namespace Sphec\Reporters;

/**
 * Collects the reported test passes and fails.
 *
 * @author Kirk Bowers
 */
class ProgressReporter extends ConsoleReporter {
  protected function report_pass($label) {
    $this->get_output()->write(".");
  }

  protected function report_fail($label) {
    $this->get_output()->write("<fg=red>F</fg=red>");
  }
}
