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
    private $_legal_functions_with_params = array();
    private $_name = '<Anonymous>';

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

    public function __sphec_add_legal_function($name, $result_or_args = null, $result = null) {
      if ($result) {
        if (!isset($this->_legal_functions[$name])) {
          $this->_legal_functions[$name] = null;
        }
        if (!isset($this->_legal_functions_with_params[$name])) {
          $this->_legal_functions_with_params[$name] = [];
        }
        $this->_legal_functions_with_params[$name][] = [
          'params' => $result_or_args,
          'result' => $result,
          'call_count' => 0
        ];
      } else {
        $this->_legal_functions[$name] = $result_or_args;
      }
      $this->_function_call_counts[$name] = 0;
    }

    public function __sphec_function_call_count($name, $params = null) {
      if ($this->__sphec_is_legal_function($name)) {
        if ($params && $tuple = & $this->__sphec_find_function_with_params($name, $params)) {
          return $tuple['call_count'];
        }
        return $this->_function_call_counts[$name];
      } else {
        // TODO:
        // Should this throw an exception?
        return 0;
      }
    }

    public function __call($name, $arguments) {
      if ($this->__sphec_is_legal_function($name)) {
        $this->_function_call_counts[$name]++;
        if ($tuple = & $this->__sphec_find_function_with_params($name, $arguments)) {
          $tuple['call_count'] += 1;
          return $tuple['result'];
        }
        return $this->_legal_functions[$name];
      } else {
        throw new UnstubbedMethodException("Call of unstubbed method $name on test double $this->_name");
      }
    }

    public function __toString() {
      return "test_double($this->_name)";
    }

    private function & __sphec_find_function_with_params($name, $params) {
      if (isset($this->_legal_functions_with_params[$name])) {
        foreach($this->_legal_functions_with_params[$name] as & $tuple) {
          if ($params == $tuple['params']) {
            return $tuple;
          }
        }
      }
      $null = null;
      return $null;
    }
  }
}
