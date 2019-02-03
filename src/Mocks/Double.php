<?php
namespace Sphec\Mocks {

  /**
   * The control class for building specifications.
   *
   * @author Kirk Bowers
   */
  class Double {
    private $_legal_functions = array();
    private $_function_call_counts = array();
    private $_name = '(Anonymous)';

    public function __construct($id = null, $legal_functions = null) {
      if (is_array($id)) {
        $this->__sphec_add_functions($id);
      } else if ($id) {
        $this->_name = $id;
        if (is_array($legal_functions)) {
          $this->__sphec_add_functions($legal_functions);
        }
      }
    }

    private function __sphec_add_functions($hash) {
      foreach ($hash as $name => $result) {
        $this->__sphec_add_legal_function($name, $result);
      }
    }

    public function __sphec_name() {
      return $this->_name;
    }

    public function __sphec_is_legal_function($name) {
      return array_key_exists($name, $this->_legal_functions);
    }

    public function __sphec_add_legal_function($name, $result = null) {
      $this->_legal_functions[$name] = $result;
      $this->_function_call_counts[$name] = 0;
    }

    public function __sphec_function_call_count($name) {
      return $this->_function_call_counts[$name];
    }

    public function __call($name, $arguments) {
      if ($this->__sphec_is_legal_function($name)) {
        $this->_function_call_counts[$name]++;
        return $this->_legal_functions[$name];
      } else {
        throw new UnstubbedMethodException;
      }
    }
  }
}

namespace {
  function test_double($id = null, $args = null) {
    return new \Sphec\Mocks\Double($id, $args);
  }
}