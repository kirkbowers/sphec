<?php

Sphec\Sphec::specify('DSLifier', function($spec) {
  $spec->describe('is_blank_or_comment', function($spec) {
    $spec->it('returns true on an empty string', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $result = $dsl->is_blank_or_comment('');
      $spec->expect($result)->to_be_truthy();
    });

    $spec->it('returns true on a bunch of spaces', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $result = $dsl->is_blank_or_comment('           ');
      $spec->expect($result)->to_be_truthy();
    });

    $spec->it('returns true starting with a hash', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $result = $dsl->is_blank_or_comment('# This is a comment');
      $spec->expect($result)->to_be_truthy();
    });

    $spec->it('returns true starting with two slashes', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $result = $dsl->is_blank_or_comment('// This is a comment');
      $spec->expect($result)->to_be_truthy();
    });

    $spec->it('returns false otherwise', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $result = $dsl->is_blank_or_comment('specify something');
      $spec->expect($result)->to_be_falsy();
    });
  });


  $spec->describe('matches_command', function($spec) {
    $spec->it('returns true when starting with specify', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $result = $dsl->matches_command('specify', 'specify MyClass');
      $spec->expect($result)->to_be_truthy();
    });

    $spec->it('returns false when starting with anything else', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $result = $dsl->matches_command('specify', '$spec->context');
      $spec->expect($result)->to_be_falsy();
    });
  });


  $spec->describe('process_command', function($spec) {
    $spec->it('converts a specify with a simple string', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $matches = $dsl->matches_command('specify', 'specify MyClass');
      $result = $dsl->process_command($matches[1], "Sphec\\Sphec::specify");
      $expected = "Sphec\\Sphec::specify('MyClass', function(\$spec) {";
      $spec->expect($result)->to_be($expected);
    });

    $spec->it('converts a specify with a single quote', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $matches = $dsl->matches_command('specify', "specify can't touch this");
      $result = $dsl->process_command($matches[1], "Sphec\\Sphec::specify");
      $expected = "Sphec\\Sphec::specify('can\\'t touch this', function(\$spec) {";
      $spec->expect($result)->to_be($expected);
    });

    $spec->it('converts a describe with a simple string', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $matches = $dsl->matches_command('describe', 'describe my_method');
      $result = $dsl->process_command($matches[1], "\$spec->describe", 1);
      $expected = "\$spec->describe(1, 'my_method', function(\$spec) {";
      $spec->expect($result)->to_be($expected);
    });
  });


  $spec->describe('process_simple_command', function($spec) {
    $spec->it('converts a before', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $matches = $dsl->matches_simple_command('before', 'before');
      $result = $dsl->process_simple_command($matches, "\$spec->before");
      $expected = "\$spec->before(function(\$spec) {";
      $spec->expect($result)->to_be($expected);
    });
  });


  $spec->describe('process_local_vars', function($spec) {
    $spec->it('converts one local var', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $result = $dsl->process_local_vars('@foo = 1;');
      $expected = "\$spec->foo = 1;";
      $spec->expect($result)->to_be($expected);
    });

    $spec->it('converts two local vars', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $result = $dsl->process_local_vars('@foo = @blech + 1;');
      $expected = "\$spec->foo = \$spec->blech + 1;";
      $spec->expect($result)->to_be($expected);
    });
  });


  $spec->describe('process_expect', function($spec) {
    $spec->it('converts simple expect', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $matches = $dsl->matches_expect('expect($foo)->to_be(1);');
      $result = $dsl->process_expect($matches);
      $expected = "\$spec->expect(\$foo)->to_be(1);";
      $spec->expect($result)->to_be($expected);
    });

    $spec->it('converts expect with local vars', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $matches = $dsl->matches_expect('expect(@foo + 1)->to_be(@blech);');
      $result = $dsl->process_expect($matches);
      $expected = "\$spec->expect(\$spec->foo + 1)->to_be(\$spec->blech);";
      $spec->expect($result)->to_be($expected);
    });
  });

  $spec->describe('process_expect_function', function($spec) {
    $spec->it('converts expect to braces to an anonymous function', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $matches = $dsl->matches_expect_function('expect({ @foo + 1; })->to_be(@blech);');
      $result = $dsl->process_expect_function($matches);
      $expected = "\$spec->expect(function() use (\$spec) { \$spec->foo + 1; })->to_be(\$spec->blech);";
      $spec->expect($result)->to_be($expected);
    });
  });

  $spec->describe('process_let', function($spec) {
    $spec->it('returns false when it does not match a let', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $result = $dsl->process_let('before');
      $expected = false;
      $spec->expect($result)->to_be($expected);
    });

    $spec->it('returns the line converted with the stuff right of the equals in a function', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $result = $dsl->process_let('let foo = 42;');
      $expected = "\$spec->let('foo', function(\$spec) { return 42;";
      $spec->expect($result)->to_be($expected);
    });

    $spec->it('strips off an optional at sign', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $result = $dsl->process_let('let @foo = 42;');
      $expected = "\$spec->let('foo', function(\$spec) { return 42;";
      $spec->expect($result)->to_be($expected);
    });

    $spec->it('interpolates at signs right of the equals', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $result = $dsl->process_let('let foo = @blah + 2;');
      $expected = "\$spec->let('foo', function(\$spec) { return \$spec->blah + 2;";
      $spec->expect($result)->to_be($expected);
    });

    $spec->it('does not insert "return" or close the function when nothing is right of the equals', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $result = $dsl->process_let('let @foo =');
      $expected = "\$spec->let('foo', function(\$spec) {";
      $spec->expect($result)->to_be($expected);
    });
  });

  $spec->describe('process_allow', function($spec) {
    $spec->it('returns false when it does not match an allow', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $result = $dsl->process_allow('before');
      $expected = false;
      $spec->expect($result)->to_be($expected);
    });

    $spec->it('returns the line with the allow converted and the subject converted to a mock', function($spec) {
      $dsl = new Sphec\DSLifier('');
      $result = $dsl->process_allow('allow(@double)->to_receive("blah");');
      $expected = "\$spec->allow(\$spec->double = \\Sphec\\Mocks\\SpyDouble::factory(\$spec->double))->to_receive(\"blah\");";
      $spec->expect($result)->to_be($expected);
    });
  });


  $spec->describe('get_php', function($spec) {
    $spec->it('closes a bare specify', function($spec) {
      $input = <<<EOF
specify MyClass
EOF;

      $output = <<<EOF
Sphec\\Sphec::specify('MyClass', function(\$spec) {
});

EOF;

      $dsl = new Sphec\DSLifier($input);
      $spec->expect($dsl->get_php())->to_be($output);
    });

    $spec->it('closes an indented specify', function($spec) {
      $input = <<<EOF
  specify MyClass
EOF;

      $output = <<<EOF
  Sphec\\Sphec::specify('MyClass', function(\$spec) {
  });

EOF;

      $dsl = new Sphec\DSLifier($input);
      $spec->expect($dsl->get_php())->to_be($output);
    });

    $spec->it('closes a specify and a describe', function($spec) {
      $input = <<<EOF
specify MyClass
  describe my_method
EOF;

      $output = <<<EOF
Sphec\\Sphec::specify('MyClass', function(\$spec) {
  \$spec->describe(2, 'my_method', function(\$spec) {
  });
});

EOF;

      $dsl = new Sphec\DSLifier($input);
      $spec->expect($dsl->get_php())->to_be($output);
    });

    $spec->it('closes a specify and two sibling describes', function($spec) {
      $input = <<<EOF
specify MyClass
  describe my_method
  describe other_method
EOF;

      $output = <<<EOF
Sphec\\Sphec::specify('MyClass', function(\$spec) {
  \$spec->describe(2, 'my_method', function(\$spec) {
  });
  \$spec->describe(3, 'other_method', function(\$spec) {
  });
});

EOF;

      $dsl = new Sphec\DSLifier($input);
      $spec->expect($dsl->get_php())->to_be($output);
    });

    $spec->it('closes a specify and embedded describes', function($spec) {
      $input = <<<EOF
specify MyClass
  describe my_method
    describe other_method
EOF;

      $output = <<<EOF
Sphec\\Sphec::specify('MyClass', function(\$spec) {
  \$spec->describe(2, 'my_method', function(\$spec) {
    \$spec->describe(3, 'other_method', function(\$spec) {
    });
  });
});

EOF;

      $dsl = new Sphec\DSLifier($input);
      $spec->expect($dsl->get_php())->to_be($output);
    });

    $spec->it('closes a specify and embedded describes and contexts', function($spec) {
      $input = <<<EOF
specify MyClass
  describe my_method
    context interesting situation
    context other situation
  describe other_method
EOF;

      $output = <<<EOF
Sphec\\Sphec::specify('MyClass', function(\$spec) {
  \$spec->describe(2, 'my_method', function(\$spec) {
    \$spec->context(3, 'interesting situation', function(\$spec) {
    });
    \$spec->context(4, 'other situation', function(\$spec) {
    });
  });
  \$spec->describe(5, 'other_method', function(\$spec) {
  });
});

EOF;

      $dsl = new Sphec\DSLifier($input);
      $spec->expect($dsl->get_php())->to_be($output);
    });

    $spec->it('closes a specify and embedded whens, converting whens to contexts', function($spec) {
      $input = <<<EOF
specify MyClass
  when this is the scenario
EOF;

      $output = <<<EOF
Sphec\\Sphec::specify('MyClass', function(\$spec) {
  \$spec->context(2, 'when this is the scenario', function(\$spec) {
  });
});

EOF;

      $dsl = new Sphec\DSLifier($input);
      $spec->expect($dsl->get_php())->to_be($output);
    });

    $spec->it('closes a specify and an it', function($spec) {
      $input = <<<EOF
specify MyClass
  it does that thing
    \$foo = 1;
    \$blech = 2;
EOF;

      $output = <<<EOF
Sphec\\Sphec::specify('MyClass', function(\$spec) {
  \$spec->it(2, 'does that thing', function(\$spec) {
    \$foo = 1;
    \$blech = 2;
  });
});

EOF;

      $dsl = new Sphec\DSLifier($input);
      $spec->expect($dsl->get_php())->to_be($output);
    });

    $spec->it('throws with a bad indent after an it', function($spec) {
      $input = <<<EOF
specify MyClass
  it does that thing
  \$foo = 1;
    \$blech = 2;
EOF;

      $dsl = new Sphec\DSLifier($input);
      $block = function() use ($dsl) { $dsl->get_php(); };
      $spec->expect($block)->to_throw('Sphec\BadIndentException');
    });

    $spec->it('closes a specify and a before and it', function($spec) {
      $input = <<<EOF
specify MyClass
  before
    @foo = 3;
  it does that thing
    @foo = 1;
    \$blech = 2;
EOF;

      $output = <<<EOF
Sphec\\Sphec::specify('MyClass', function(\$spec) {
  \$spec->before(function(\$spec) {
    \$spec->foo = 3;
  });
  \$spec->it(4, 'does that thing', function(\$spec) {
    \$spec->foo = 1;
    \$blech = 2;
  });
});

EOF;

      $dsl = new Sphec\DSLifier($input);
      $spec->expect($dsl->get_php())->to_be($output);
    });

    $spec->it('closes a specify and an after and it', function($spec) {
      $input = <<<EOF
specify MyClass
  after
    @foo = 3;
  it does that thing
    @foo = 1;
    \$blech = 2;
    expect(@foo + \$blech)->to_be(3);
EOF;

      $output = <<<EOF
Sphec\\Sphec::specify('MyClass', function(\$spec) {
  \$spec->after(function(\$spec) {
    \$spec->foo = 3;
  });
  \$spec->it(4, 'does that thing', function(\$spec) {
    \$spec->foo = 1;
    \$blech = 2;
    \$spec->expect(\$spec->foo + \$blech)->to_be(3);
  });
});

EOF;

      $dsl = new Sphec\DSLifier($input);
      $spec->expect($dsl->get_php())->to_be($output);
    });

    $spec->it('allows uneven indents inside before and it blocks', function($spec) {
      $input = <<<EOF
specify MyClass
  before
    @foo =
      3;
    @blech = 2;
  it does that thing
    expect(function() {
      throw new Exception;
    })->to_throw('Exception');

    expect(@foo - 1)->to_be(@blech);
EOF;

      $output = <<<EOF
Sphec\\Sphec::specify('MyClass', function(\$spec) {
  \$spec->before(function(\$spec) {
    \$spec->foo =
      3;
    \$spec->blech = 2;
  });
  \$spec->it(6, 'does that thing', function(\$spec) {
    \$spec->expect(function() {
      throw new Exception;
    })->to_throw('Exception');

    \$spec->expect(\$spec->foo - 1)->to_be(\$spec->blech);
  });
});

EOF;

      $dsl = new Sphec\DSLifier($input);
      $spec->expect($dsl->get_php())->to_be($output);
    });

    $spec->it('allows heredocs inside before and it blocks', function($spec) {
      $input = <<<EOF
specify MyClass
  before
    @foo = <<<END
Some stuff
Some more stuff
END;
  it does that thing
    \$blech = <<<EOS
Some stuff
Some more stuff
EOS;
    expect(@foo)->to_be(\$blech);
EOF;

      $output = <<<EOF
Sphec\\Sphec::specify('MyClass', function(\$spec) {
  \$spec->before(function(\$spec) {
    \$spec->foo = <<<END
Some stuff
Some more stuff
END;
  });
  \$spec->it(7, 'does that thing', function(\$spec) {
    \$blech = <<<EOS
Some stuff
Some more stuff
EOS;
    \$spec->expect(\$spec->foo)->to_be(\$blech);
  });
});

EOF;

      $dsl = new Sphec\DSLifier($input);
      $spec->expect($dsl->get_php())->to_be($output);
    });

    $spec->it('allows comments at the beginning of the line', function($spec) {
      $input = <<<EOF
specify MyClass
  before
    @foo = 3;
    @blech = 2;
  it does that thing
//     expect(function() {
//       throw new Exception;
//     })->to_throw('Exception');

    expect(@foo - 1)->to_be(@blech);
EOF;

      $output = <<<EOF
Sphec\\Sphec::specify('MyClass', function(\$spec) {
  \$spec->before(function(\$spec) {
    \$spec->foo = 3;
    \$spec->blech = 2;
  });
  \$spec->it(5, 'does that thing', function(\$spec) {
//     expect(function() {
//       throw new Exception;
//     })->to_throw('Exception');

    \$spec->expect(\$spec->foo - 1)->to_be(\$spec->blech);
  });
});

EOF;

      $dsl = new Sphec\DSLifier($input);
      $spec->expect($dsl->get_php())->to_be($output);
    });

    $spec->it('handles let commands that are on a single line', function($spec) {
      $input = <<<EOF
specify MyClass
  let blah = 42;
  before
    @foo = 3;
EOF;

      $output = <<<EOF
Sphec\\Sphec::specify('MyClass', function(\$spec) {
  \$spec->let('blah', function(\$spec) { return 42;
  });
  \$spec->before(function(\$spec) {
    \$spec->foo = 3;
  });
});

EOF;

      $dsl = new Sphec\DSLifier($input);
      $spec->expect($dsl->get_php())->to_be($output);
    });

    $spec->it('handles let commands that are on multiple lines', function($spec) {
      $input = <<<EOF
specify MyClass
  let blech = 40;
  let blah =
    \$result = @blech + 2;
    return \$result;
  before
    @foo = 3;
EOF;

      $output = <<<EOF
Sphec\\Sphec::specify('MyClass', function(\$spec) {
  \$spec->let('blech', function(\$spec) { return 40;
  });
  \$spec->let('blah', function(\$spec) {
    \$result = \$spec->blech + 2;
    return \$result;
  });
  \$spec->before(function(\$spec) {
    \$spec->foo = 3;
  });
});

EOF;

      $dsl = new Sphec\DSLifier($input);
      $spec->expect($dsl->get_php())->to_be($output);
    });

    $spec->it('handles allow commands', function($spec) {
      $input = <<<EOF
specify MyClass
  before
    @foo = test_double();
    allow(@foo)->to_receive('blah');
EOF;

      $output = <<<EOF
Sphec\\Sphec::specify('MyClass', function(\$spec) {
  \$spec->before(function(\$spec) {
    \$spec->foo = test_double();
    \$spec->allow(\$spec->foo = \\Sphec\\Mocks\\SpyDouble::factory(\$spec->foo))->to_receive('blah');
  });
});

EOF;

      $dsl = new Sphec\DSLifier($input);
      $spec->expect($dsl->get_php())->to_be($output);
    });
  });
});
