<?php
namespace Sphec;

/**
 * The class that converts a big string in the shorthand Sphec notation into runnable PHP.
 *
 * @author Kirk Bowers
 */
class DSLifier {
  private $php = "";

  function __construct($input, $filename = "") {
    $this->input = $input;
    $this->filename = $filename;
    $this->dirname = dirname($this->filename);
    $this->current_indent = '';
    $this->heredoc = false;
  }

  public function is_blank_or_comment($line) {
    if (preg_match('/^\s*$/', $line)) {
      return true;
    }
    if (preg_match('/^\s*\#/', $line)) {
      return true;
    }
    if (preg_match('/^\s*\/\//', $line)) {
      return true;
    }
    return false;
  }

  public function matches_command($command, $line) {
    if (preg_match('/^' . $command . '\s+(.*)$/', $line, $matches)) {
      return $matches;
    }
    return false;
  }

  public function matches_simple_command($command, $line) {
    if (preg_match('/^' . $command . '\s*$/', $line, $matches)) {
      return $matches;
    }
    return false;
  }

  public function process_command($argument, $replacement) {
    return $replacement . "('" . str_replace("'", "\\'", $argument) .
      "', function(\$spec) {";
  }

  public function process_simple_command($matches, $replacement) {
    return $replacement . "(function(\$spec) {";
  }

  public function process_let($line) {
    if (preg_match('/^let\s+\@?(\w+)\s*=\s*(.*)$/', $line, $matches)) {
      if ($matches[2] == '') {
        return "\$spec->let('" . $matches[1] . "', function(\$spec) {";
      } else {
        $right_of_equals = $this->process_local_vars($matches[2]);
        return "\$spec->let('" . $matches[1] . "', function(\$spec) { return " . $right_of_equals;
      }
    } else {
      return false;
    }
  }

  public function process_allow($line) {
    if (preg_match('/^allow\s*\((.*)$/', $line, $matches)) {
      $result = "\$spec->allow(" . $matches[1];
      return $this->process_local_vars($result);
    } else {
      return false;
    }
  }

  public function process_local_vars($line) {
    $result = preg_replace('/@(\w+)/', "\$spec->$1", $line);
    $result = preg_replace('/__DIR__/', "'$this->dirname'", $result);
    $result = preg_replace('/__FILE__/', "'$this->filename'", $result);

    return $result;
  }

  public function matches_expect($line) {
    if (preg_match('/^expect\s*\((.*)$/', $line, $matches)) {
      return $matches;
    }
    return false;
  }

  public function process_expect($matches) {
    $result = "\$spec->expect(" . $matches[1];
    return $this->process_local_vars($result);
  }

  public function matches_expect_function($line) {
    if (preg_match('/^expect\s*\(\s*\{(.*)$/', $line, $matches)) {
      return $matches;
    }
    return false;
  }

  public function process_expect_function($matches) {
    $result = "\$spec->expect(function() use (\$spec) {" . $matches[1];
    return $this->process_local_vars($result);
  }


  private function advance_indent($this_indent) {
    array_push($this->stack, $this_indent);
    $this->new_scope = true;
    $this->current_indent = $this_indent;
  }

  private function close_indents($indent) {
    do {
      $this_indent = array_pop($this->stack);
      $this->result .= $this_indent . "});\n";

      $this->current_indent = array_slice($this->stack, -1)[0];
      $compare = $this->compare_indent($indent);
    } while (($compare <= 0) && !empty($this->stack));
  }

  private function current_indent() {
    return $this->current_indent;
  }

  private function compare_indent($indent) {
    $current_indent = $this->current_indent();
    $indent_len = strlen($indent);
    $current_len = strlen($current_indent);
    if ($indent_len < $current_len) {
      return -1;
    } else if ($indent_len > $current_len) {
      return 1;
    } else {
      return 0;
    }
  }

  private function handle_indent($indent, $is_command = true, $line = "") {
    $compare = $this->compare_indent($indent);

    if ($is_command) {
      if ($compare > 0) {
        if ($this->new_scope) {
          $this->advance_indent($indent);
        } else {
          $this->throw_bad_indent();
        }
      } else {
        $this->close_indents($indent);
        $this->advance_indent($indent);
      }

      $this->new_scope = true;
    } else {
      if (!$this->heredoc && preg_match('/<<<(\w+)\s*$/', $line, $matches)) {
        $this->heredoc = $matches[1];
      }

      if ($this->new_scope) {
        if ($compare <= 0) {
          $this->throw_bad_indent();
        } else {
          $this->current_indent = $indent;
        }
      } else {
        if ($this->heredoc) {
          if (preg_match('/^' . $this->heredoc . ';/', $line, $matches)) {
            $this->heredoc = false;
          }
        } else if ($compare < 0) {
          $this->throw_bad_indent();
        }
      }

      $this->new_scope = false;
    }
  }

  private function throw_bad_indent() {
    throw new BadIndentException();
  }

  public function get_php() {
    $this->new_scope = false;
    $this->stack = array();
    $this->result = '';

    $lines = explode(PHP_EOL, $this->input);

    $this->line_num = 0;
    foreach ($lines as $line) {
      $this->line_num += 1;
      if ($this->is_blank_or_comment($line)) {
        $this->result .= $line . "\n";
      } else {
        // Split for indentation
        preg_match('/^(\s*)(\S.*)$/', $line, $matches);
        $this_indent = $matches[1];
        $command = $matches[2];

        if ($matches = $this->matches_command('specify', $command)) {
          $this->result .= $this_indent .
            $this->process_command($matches[1], "Sphec\\Sphec::specify") . "\n";
          $this->advance_indent($this_indent);
        } else if ($matches = $this->matches_command('describe', $command)) {
          $this->handle_indent($this_indent);
          $this->result .= $this_indent .
            $this->process_command($matches[1], "\$spec->describe") . "\n";
        } else if ($matches = $this->matches_command('context', $command)) {
          $this->handle_indent($this_indent);
          $this->result .= $this_indent .
            $this->process_command($matches[1], "\$spec->context") . "\n";
        } else if ($matches = $this->matches_command('when', $command)) {
          $this->handle_indent($this_indent);
          $this->result .= $this_indent .
            $this->process_command('when ' . $matches[1], "\$spec->context") . "\n";
        } else if ($matches = $this->matches_command('it', $command)) {
          $this->handle_indent($this_indent);
          $this->result .= $this_indent .
            $this->process_command($matches[1], "\$spec->it") . "\n";
        } else if ($matches = $this->matches_simple_command('before', $command)) {
          $this->handle_indent($this_indent);
          $this->result .= $this_indent .
            $this->process_simple_command($matches, "\$spec->before") . "\n";
        } else if ($matches = $this->matches_simple_command('after', $command)) {
          $this->handle_indent($this_indent);
          $this->result .= $this_indent .
            $this->process_simple_command($matches, "\$spec->after") . "\n";
        } else if ($matches = $this->matches_expect_function($command)) {
          $this->handle_indent($this_indent, false, $command);
          $this->result .= $this_indent . $this->process_expect_function($matches) . "\n";
        } else if ($matches = $this->matches_expect($command)) {
          $this->handle_indent($this_indent, false, $command);
          $this->result .= $this_indent . $this->process_expect($matches) . "\n";
        } else if ($result = $this->process_let($command)) {
          $this->handle_indent($this_indent);
          $this->result .= $this_indent . $result . "\n";
        } else if ($result = $this->process_allow($command)) {
          $this->handle_indent($this_indent, false, $command);
          $this->result .= $this_indent . $result . "\n";
        } else {
          $this->handle_indent($this_indent, false, $command);
          $this->result .= $this_indent . $this->process_local_vars($command) . "\n";
        }

      }
    }

    while (!empty($this->stack)) {
      $this_indent = array_pop($this->stack);
      $this->result .= $this_indent . "});\n";
    }

    return $this->result;
  }
}
