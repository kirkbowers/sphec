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
}
