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
  
  public function process_specify_command($matches) {
    return "Sphec\\Sphec::specify('" . str_replace("'", "\\'", $matches[1]) . 
      "', function(\$spec) {";
  }
  
  public function process_describe_command($matches) {
    return "\$spec->describe('" . str_replace("'", "\\'", $matches[1]) . 
      "', function(\$spec) {";
  }
  
  public function process_context_command($matches) {
    return "\$spec->context('" . str_replace("'", "\\'", $matches[1]) . 
      "', function(\$spec) {";
  }
  
  public function process_it_command($matches) {
    return "\$spec->it('" . str_replace("'", "\\'", $matches[1]) . 
      "', function(\$spec) {";
  }
  
  public function process_before_command($matches) {
    return "\$spec->before(function(\$spec) {";
  }
  
  public function process_after_command($matches) {
    return "\$spec->after(function(\$spec) {";
  }
  
  public function process_local_vars($line) {
    return preg_replace('/@(\w+)/', "\$spec->$1", $line);
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
    // echo "$indent_len $current_len\n";
    if ($indent_len < $current_len) {
      return -1;
    } else if ($indent_len > $current_len) {
      return 1;
    } else {
      return 0;
    }
  }
  
  private function handle_indent($indent, $is_command = true) {
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
      if ($this->new_scope) {
        if ($compare <= 0) {
          $this->throw_bad_indent();
        } else {
          $this->current_indent = $indent;        
        }
      } else {
        if ($compare != 0) {
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
          $this->result .= $this_indent . $this->process_specify_command($matches) . "\n";
          $this->advance_indent($this_indent);
        } else if ($matches = $this->matches_command('describe', $command)) {
          $this->handle_indent($this_indent);
          $this->result .= $this_indent . $this->process_describe_command($matches) . "\n";
        } else if ($matches = $this->matches_command('context', $command)) {
          $this->handle_indent($this_indent);
          $this->result .= $this_indent . $this->process_context_command($matches) . "\n";
        } else if ($matches = $this->matches_command('it', $command)) {
          $this->handle_indent($this_indent);
          $this->result .= $this_indent . $this->process_it_command($matches) . "\n";
        } else if ($matches = $this->matches_simple_command('before', $command)) {
          $this->handle_indent($this_indent);
          $this->result .= $this_indent . $this->process_before_command($matches) . "\n";
        } else if ($matches = $this->matches_simple_command('after', $command)) {
          $this->handle_indent($this_indent);
          $this->result .= $this_indent . $this->process_after_command($matches) . "\n";
        } else {
          $this->handle_indent($this_indent, false);
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
