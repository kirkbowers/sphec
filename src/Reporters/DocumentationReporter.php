<?php
namespace Sphec\Reporters;

class DocumentationReporter extends ConsoleReporter {
  private $indent = '';

  public function report_context_start($name) {
    $this->get_output()->writeln($this->indent . $name);
    $this->indent .= '  ';
  }

  public function report_context_end($name) {
    if (strlen($this->indent) >= 2) {
      $this->indent = substr($this->indent, 2);
    }
  }

  protected function report_pass($label) {
    $this->get_output()->writeln("$this->indent<fg=green>$label</fg=green>");
  }

  protected function report_fail($label) {
    $this->get_output()->writeln("$this->indent<fg=red>$label</fg=red>");
  }
}
