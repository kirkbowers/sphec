# Sphec
A Behavior-Driven Development (BDD) toolkit in PHP.

## Introduction

Sphec is a Behavior-Driven Development (BDD) toolkit for PHP that is largely inspired by [RSpec](http://rspec.info/), [Jasmine](https://jasmine.github.io/), and to a degree by [Cucumber](https://cucumber.io/).  The name is somewhat of a contraction of "spec" and "PHP".  It provides a domain specific language (DSL) that is a mixture of natural language shorthand and quasi-PHP for describing how a part of your software should behave and writing tests to ensure it does, in fact, behave that way.

Some features are:

- Hierarchical contexts for grouping related tests and setting up different pre-test conditions.
- `before` and `after` actions to set up and tear down pre-test conditions.
- "Local" variables that propagate from the outer context up to each contained example.
- Tests that are phrase as expectations.

Here's what an example Sphec file looks like, with comments to point out some of the features:

    require_once __DIR__ . '/../vendor/autoload.php';

    // Specify the behavior of a class
    specify TestProject\Accumulator

      // Describe how a method of that class is expected to behave
      describe add

        // Set up a subcontext with pre-test conditions shared by multiple examples.
        context with default starting value

          // Set up those pre-test conditions in a before action
          before
            // "Local" variables to be shared by blocks within a context are declared
            // using the @ symbol
            @accumulator = new TestProject\Accumulator;

          // Provide examples of what the expected behavior is.
          it should start with a zero value
            // Note, the "local" member variable accumulator that was create in `before` is
            // available inside the examples.

            // Write tests as expectations.
            expect(@accumulator->get_value())->to_be(0);

          it should accumulate a single value
            @accumulator->add(3);
            expect(@accumulator->get_value())->to_be(3);

          it should accumulate more than one value
            @accumulator->add(3);
            @accumulator->add(5);
            expect(@accumulator->get_value())->to_be(8);

        // Set up a different context with different pre-test conditions.
        context with supplied starting value
          before
            @accumulator = new TestProject\Accumulator(2);

          it should start with a zero value
            expect(@accumulator->get_value())->to_be(2);

          it should accumulate a single value
            @accumulator->add(3);
            expect(@accumulator->get_value())->to_be(5);

          it should accumulate more than one value
            @accumulator->add(3);
            @accumulator->add(5);
            expect(@accumulator->get_value())->to_be(10);

Under the hood, this gets converted to PHP and then eval'd.  Since PHP does not have the notion of blocks like Ruby does, a lot has to be done with anonymous functions that are passed the working scope as a parameter.  If you don't mind working with that bit of extra verbosity, you can write your Sphec specs in straight PHP.

Here's what the above sphec shorthand gets converted into in actual PHP:

    <?php

    require_once __DIR__ . '/../vendor/autoload.php';

    // Specify the behavior of a class
    Sphec\Sphec::specify('TestProject\Accumulator', function($spec) {

      // Describe how a method of that class is expected to behave
      $spec->describe('add', function($spec) {

        // Set up a subcontext with pre-test conditions shared by multiple examples.
        $spec->context('with default starting value', function($spec) {

          // Set up those pre-test conditions in a before action
          $spec->before(function($spec) {
            $spec->accumulator = new TestProject\Accumulator;
          });

          // Provide examples of what the expected behavior is.
          $spec->it('should start with a zero value', function($spec) {
            // Note, the "local" member variable accumulator that was create in `before` is
            // available inside the examples.

            // Write tests as expectations.
            $spec->expect($spec->accumulator->get_value())->to_be(0);
          });

          $spec->it('should accumulate a single value', function($spec) {
            $spec->accumulator->add(3);
            $spec->expect($spec->accumulator->get_value())->to_be(3);
          });

          $spec->it('should accumulate more than one value', function($spec) {
            $spec->accumulator->add(3);
            $spec->accumulator->add(5);
            $spec->expect($spec->accumulator->get_value())->to_be(8);
          });
        });

        // Set up a different context with different pre-test conditions.
        $spec->context('with supplied starting value', function($spec) {
          $spec->before(function($spec) {
            $spec->accumulator = new TestProject\Accumulator(2);
          });

          $spec->it('should start with a zero value', function($spec) {
            $spec->expect($spec->accumulator->get_value())->to_be(2);
          });

          $spec->it('should accumulate a single value', function($spec) {
            $spec->accumulator->add(3);
            $spec->expect($spec->accumulator->get_value())->to_be(5);
          });

          $spec->it('should accumulate more than one value', function($spec) {
            $spec->accumulator->add(3);
            $spec->accumulator->add(5);
            $spec->expect($spec->accumulator->get_value())->to_be(10);
          });
        });
      });
    });

Note that every anonymous function takes a parameter named `$spec`.  It can be named anything you like (except `$this`), but using `$spec` by convention is recommended.  The scope provided by the `$spec` variable will give access to the various commands that are available in that scope as well as any "local" member variables.

## Installing Sphec

The preferred way to install Sphec is with [Composer](https://getcomposer.org).  Assuming you have Composer installed, run:

    composer global require kirkbowers/sphec

Then, assuming you have `$COMPOSER_HOME/vendor/bin` in your executable path, you should be good to go.

## Running Sphec

Sphec is a command line tool.  By default, it expects to be run in the root directory of a project, it expects all specs to be in a directory called `spec`, and it expects all spec files to end in either the suffix `_spec.sphec` or `_spec.php`.

Files ending with the suffix `.sphec` will be treated as Sphec shorthand and will be converted to PHP before being run.  Files ending with `.php` will be run as is.

If those assumptions hold, you can simply run it on the command line:

    sphec

This will show the output as a string of dots and/or F's for each test that passed or failed, followed by a summary:

    $ sphec

    ..............................................................................................................
    Successes: 110, Failures: 0
    Success!

To see more verbose output showing the hierarchy of all contexts and examples run, use the `-v` flag.

If you want to run only one particular spec file, pass the path to the file as an argument to `sphec`.  Likewise, if you want to only run the tests in a particular directory, pass the path to the directory and it will run all specs named `*_spec.sphec` and `*_spec.php` in that and all subdirectories.

## Different scopes:  Context, Setup, and Example

While inside a Sphec specification, there are three different types of scopes that you can be inside of:  context, setup and example.

### Context scope

You are in a context scope whenever you are just inside a `specify`, `describe`, or `context` block.  Context blocks are executed immediately in order to build the tree of
subcontexts and examples.  The available commands inside of a context scope are:

- `describe $label`
- `context $label`
- `before`
- `after`
- `it $label`

Local member variables are not available inside of a context scope block.

### Setup scope

You are in a setup scope whenever you are inside a `before` or `after`.  Setup blocks are executed lazily, only at the time that tests are actually run.  There are no special commands available inside of a setup block.  Local member variables are available, as this is where they should be set up or torn down.

### Example scope

You are in an example scope whenever you are inside an `it` block.  Example blocks are executed lazily, only at the time that tests are actually run.  The one special command available is `expect`.  Local member variables are available, as this is where they should be set up or torn down.

## Expectations

Tests are performed inside of examples by stating expectations.  An expectation always takes this form:

      expect($computed)->test($expected);

Where `test` is one of the test methods listed below (some tests, like `to_be_falsey`, do not take an `$expected` parameter).  Execution will continue after any expectations that fail, with all the failures gathered up and reported on the console after all tests have been performed.

### Available tests

`to_be_equivalent($expected)` passes if the computed and expected values are both the same value and same type.  The test is performed with `===`.

`to_be($expected)` is a shortcut alias for `to_be_equivalent`.

`to_equal($expected)` passes if the computed and expected values are the same value after type coercion.  The test is performed with `==`.

`to_not_be($expected)` passes if the computed and expected values are either a different value or a different type.  The test is performed with `!==`.

`to_not_equal($expected)` passes if the computed and expected values are not the same value after type coercion.  The test is performed with `!=`.

`to_be_true()` passes if the computed value is strictly true (of type boolean).

`to_be_truthy()` passes if the computed value is true after type coercion.

`to_be_false()` passes if the computed value is strictly false (of type boolean).

`to_be_falsy()` passes if the computed value is false after type coercion.  In PHP a lot of values satisfy this condition, including false, null, 0, "0", array(), and an unset variable.

`to_be_greater_than($expected)` passes if the computed value is strictly greater than the expected value (after type coercion).

`to_be_greater_than_or_equal($expected)` passes if the computed value is greater than or equal to the expected value (after type coercion).

`to_be_less_than($expected)` passes if the computed value is strictly less than the expected value (after type coercion).

`to_be_less_than_or_equal($expected)` passes if the computed value is less than or equal to the expected value (after type coercion).

`to_throw($expected)` passes if the anonymous function passed to `expect` throws the expected exception (or a subclass) when executed.  This one works a little different than the above tests.  `expect` must be passed an anonymous function that takes no parameters.  `$expected` is the name of an expected exception as a string.

`not_to_throw()` passes if the anonymous function passed to `expect` executes without throwing any exceptions.  `expect` must be passed an anonymous function that takes no parameters.

## Local variables

"Local" variables can be declared in `before` and `after` blocks and consumed inside `it` examples.  The shorthand for a local variable the at sign (`@`) followed by a variable name.  So, assuming you are inside a context, this works:

        before
          @foo = 3;
        it uses a local var in an example
          expect(@foo)->to_be(3);

## Scope is defined by indent level

Much like Python and CoffeeScript, the scope of blocks in Sphec shorthand is controlled by the level of indentation.  For example, the two `context` blocks below are peers:

    specify MyClass
      describe some_method
        before
          @foo = 3
        context in a certain situation
          before
            @bar = 0
          it behaves a certain way
            ...
        context in a different situation
          before
            @bar = 42
          it behaves differently

Local variables go in and out of scope depending on the containing context.  In the above example, `@foo` is shared by both `context` blocks, but the two `context` blocks have their own unique copy of `@bar`.

There are three notable exceptions to the rule about indenting.

The inside of `before`, `after`, and `it` blocks is quasi-PHP.  That is, it's PHP with `@` local vars and `expect` commands that will be expanded by the Sphec parser.  If you need further inner scopes inside of these blocks, it should be handled by the usual PHP curly braces.  To make this concrete, if you need to declare an anonymous function to test for a raised exception, you can indent further inside your `it` block to style it nicely:

        it might do something catastrophic
          // Notice, the braces are needed, and the extra indent is legal
          $myfunc = function() {
            // Do something that might throw an exception
          };
          expect($myfunc)->to_throw('CatastrophicException');

Another exception is comments.  The indent level of comments are ignored, so it is legal to comment out sections by prepending the lines of code with double slashes or hashes.

      context under certain circumstances
        it does something
          ...
    //     it won't work!  This is pending.
    //       ...

        // We are still inside the "under certain circumstances" block
        it does something else
          ...

A third exception is heredocs.  Inside of `before`, `after` and `it` blocks it is legal to assign values to variables using the heredoc notation.  In this case, the inside of the heredoc, and especially the marker closing the heredoc, may (must) be left justified.  Normal indenting rules resume once the heredoc is closed.

      content under certain circumstances
        it creates a multiline string
          $blech = some_function_call();
          $foo = <<<EOS
    This left justified string does not break the indentation rules.
    I have a few other things I could say...
    EOS;

          expect($blech)->to_be($foo);

## Future Areas of Development

Sphec is in its early stages of development and, although quite feature rich, has room to grow.  Here are some anticipated, or hoped for, future features.

- Most interesting applications work with a database.  In the Rails ecosystem there are strong tools for setting up and populating a test database.  It would be nice to have similar capabilities in PHP-land, especially when working with WordPress.
- Pending examples (using the `xit` command) would be nice.
- Be able to continue running tests after an error has occurred.  Currently, the program will simply bomb out.  RSpec will keep going and report an "E" for any test that has an error.

## Contributing

Sphec is totally open source and I welcome any help to flesh it out.  If you'd like to contribute, do the usual fork, create a branch off of `development`, do your thing then send me a pull request.

### Testing

Sphec tests itself!  Kinda' meta, eh?

There are three ways to test Sphec if you are working on it.

One, there are tests of Sphec's core functionality.  These tests go through a bit of gymnastics to unit test the inner workings, but they run directly off the root directory.  To run them, simply run:

    bin/sphec

Two, there are tests that all fail so that the failure messages can be visually inspected.  I'm of the strong mindset that any UI element, even if it is just printing to the console, must be visually inspected to make sure it looks as you'd expect.  To inspect the failure messages, run:

    bin/sphec failure_spec

Three, there is a test project that runs the latest `development` branch from github on a more conventional suite of tests (without going through the hoops necessary for Sphec to test itself).  To test them, run these steps from the project root:

    cd test_project/
    composer update
    vendor/bin/sphec

You should see a "Success!" message.

Enjoy!
