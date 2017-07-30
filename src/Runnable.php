<?php
namespace Sphec;

/**
 * The control class for building specifications.
 *
 * @author Kirk Bowers
 */
abstract class Runnable {
  // All member variables variables are prefixed with an underscore to protect against
  // name clashes when a consumer of this utility creates "local" member variables in
  // the scope of an example (during `before` blocks).
  public    $_label;
  protected $_block;
  protected $_indent;
  protected $_parent;
  
  public    $_expector;
  
  function __construct($label, $block, $indent = '', $parent = NULL) {
    $this->_label = $label;
    $this->_block = $block;
    $this->_indent = $indent;
    $this->_parent = $parent;
  }

  public function get_full_name() {
    $result = '';
    if ($this->_parent) {
      $result = $this->_parent->get_full_name() . ' ';
    }
    
    return $result . $this->_label;
  }

  abstract public function run();
}
