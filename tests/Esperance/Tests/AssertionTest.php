<?php
namespace Esperance\Tests;

use \Esperance\Assertion;

class AssertionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function be_should_be_ok_if_subject_is_equal_to_object()
    {
        $this->expect(1)->to->be(1);
    }

    /**
     * @test
     */
    public function be_should_be_error_if_subject_is_not_equal_to_object()
    {
        $self = $this;
        $this->expect(function () use ($self) {
            $self->expect(1)->to->be(2);
        })->to->throw('Esperance\Error', 'expected 1 to equal 2');
    }

    /**
     * @test
     */
    public function eql_should_be_ok_if_subject_is_loosely_equal_to_object()
    {
        $this->expect("1")->to->eql(1);
    }

    /**
     * @test
     */
    public function eql_should_be_error_if_subject_is_not_equal_to_object()
    {
        $self = $this;
        $this->expect(function () use ($self) {
            $self->expect(1)->to->eql(2);
        })->to->throw('Esperance\Error', 'expected 1 to sort of equal 2');
    }

    /**
     * @test
     */
    public function not_be_should_be_ok_if_subject_is_not_equal_to_object()
    {
        $this->expect(1)->to->not->be(2);
    }

    /**
     * @test
     */
    public function not_be_should_be_error_if_subject_is_equal_to_object()
    {
        $self = $this;
        $this->expect(function () use ($self) {
            $self->expect(1)->to->not->be(1);
        })->to->throw('Esperance\Error', 'expected 1 to not equal 1');
    }

    /**
     * @test
     */
    public function throw_should_be_ok_if_expected_exception_is_thrown()
    {
        $this->expect(function () {
            throw new \RuntimeException;
        })->to->throw('RuntimeException');
    }

    /**
     * @test
     */
    public function throw_should_be_ok_if_expected_exception_with_message_is_thrown()
    {
        $this->expect(function () {
            throw new \RuntimeException('expected exception message');
        })->to->throw('RuntimeException', 'expected exception message');
    }

    /**
     * @test
     */
    public function throw_should_be_error_if_expected_exception_is_not_thrown()
    {
        $self = $this;
        $this->expect(function () use ($self) {
            $self->expect(function () {
                // Do nothing.
            })->to->throw('RuntimeException');
        })->to->throw('Esperance\Error');
    }

    /**
     * @test
     */
    public function throw_should_be_error_if_exception_not_expected_is_thrown()
    {
        $self = $this;
        $this->expect(function () use ($self) {
            $self->expect(function () {
                throw new \LogicException;
            })->to->throw('RuntimeException');
        })->to->throw('Esperance\Error');
    }

    /**
     * @test
     */
    public function ok_should_be_ok_if_subject_is_truthy()
    {
        $this->expect(1)->to->be->ok();
    }

    /**
     * @test
     */
    public function ok_should_be_error_if_subject_is_falsy()
    {
        $self = $this;
        $this->expect(function () use ($self) {
            $self->expect(0)->to->be->ok();
        })->to->throw('Esperance\Error', 'expected 0 to be truthy');
    }

    /**
     * @test
     */
    public function within_should_be_ok_if_subject_is_within_arguments()
    {
        $this->expect(3)->to->be->within(2, 4);
    }

    /**
     * @test
     */
    public function within_should_be_error_if_subject_is_not_within_arguments()
    {
        $self = $this;
        $this->expect(function () use ($self) {
            $self->expect(5)->to->be->within(2, 4);
        })->to->throw('Esperance\Error', 'expected 5 to be within 2..4');
    }

    /**
     * @test
     */
    public function and_should_do_more_assertion()
    {
        $this->expect(1)->not->to->be(2)->and->to->be(1);
    }

    /**
     * @test
     */
    public function and_should_create_another_Assertion_object()
    {
        $a = $this->expect(1);
        $b = $a->to->be(1)->and;
        $this->expect($b)->to->not->be($a);
    }

    /**
     * @test
     */
    public function a_should_be_ok_if_the_subject_is_expected_type()
    {
        $this->expect(new \SplObjectStorage)->to->be->a('SplObjectStorage');
    }

    /**
     * @test
     */
    public function a_should_be_ok_it_the_subject_is_an_instance_of_object()
    {
        $this->expect(new \SplObjectStorage)->to->be->a(new \SplObjectStorage);
    }

    /**
     * @test
     */
    public function a_should_be_ok_if_the_subject_is_subclass_of_expected_type()
    {
        $this->expect(new \SplObjectStorage)->to->be->a('Traversable');
    }

    /**
     * @test
     */
    public function a_should_be_error_if_the_subject_is_not_expected_type()
    {
        $self = $this;
        $this->expect(function () use ($self) {
            $self->expect(new \SplObjectStorage)->to->be->an('ArrayObject');
        })->to->throw('Esperance\Error');
    }

    /**
     * @test
     */
    public function a_should_be_error_if_the_subject_is_not_an_intance_of_object()
    {
        $self = $this;
        $this->expect(function () use ($self) {
            $self->expect(new \SplObjectStorage)->to->be->an(new \ArrayObject);
        })->to->throw('Esperance\Error');
    }

    /**
     * @test
     */
    public function empty_should_be_ok_if_the_subject_is_empty_array()
    {
        $this->expect(array())->to->be->empty();
    }

    /**
     * @test
     */
    public function empty_should_be_ok_if_the_subject_is_NULL()
    {
        $this->expect(NULL)->to->be->empty();
    }

    /**
     * @test
     */
    public function empty_should_be_ok_if_the_subject_is_empty_string()
    {
        $this->expect('')->to->be->empty();
    }

    /**
     * @test
     */
    public function empty_should_be_error_if_the_subject_array_has_an_element()
    {
        $self = $this;
        $this->expect(function () use ($self) {
            $self->expect(array(1))->to->be->empty();
        })->to->throw('Esperance\Error');
    }

    /**
     * @test
     */
    public function empty_should_be_error_if_the_subject_string_has_a_character()
    {
        $self = $this;
        $this->expect(function () use ($self) {
            $self->expect('a')->to->be->empty();
        })->to->throw('Esperance\Error');
    }

    /**
     * @test
     */
    public function above_should_be_ok_if_the_subject_is_greater_than_argument()
    {
        $this->expect(1)->to->be->above(0);
    }

    /**
     * @test
     */
    public function above_should_be_error_if_the_subject_is_equal_to_argument()
    {
        $self = $this;
        $this->expect(function () use ($self) {
            $self->expect(1)->to->be->above(1);
        })->to->throw('Esperance\Error', 'expected 1 to be above 1');
    }

    /**
     * @test
     */
    public function above_should_be_error_if_the_subject_is_less_than_argument()
    {
        $self = $this;
        $this->expect(function () use ($self) {
            $self->expect(1)->to->be->above(2);
        })->to->throw('Esperance\Error', 'expected 1 to be above 2');
    }

    /**
     * @test
     */
    public function below_should_be_ok_if_the_subject_is_less_than_argument()
    {
        $this->expect(1)->to->be->below(2);
    }

    /**
     * @test
     */
    public function below_should_be_error_if_the_subject_is_equal_to_argument()
    {
        $self = $this;
        $this->expect(function () use ($self) {
            $self->expect(1)->to->be->below(1);
        })->to->throw('Esperance\Error', 'expected 1 to be below 1');
    }

    /**
     * @test
     */
    public function below_should_be_error_if_the_subject_is_greater_than_argument()
    {
        $self = $this;
        $this->expect(function () use ($self) {
            $self->expect(1)->to->be->below(0);
        })->to->throw('Esperance\Error', 'expected 1 to be below 0');
    }

    /**
     * @test
     */
    public function match_should_be_ok_if_the_subject_matches_regexp()
    {
        $this->expect("abc")->to->match('/^a/');
    }

    /**
     * @test
     */
    public function match_should_be_ok_if_the_subject_does_not_match_regexp()
    {
        $self = $this;
        $this->expect(function () use ($self) {
            $self->expect("abc")->to->match('/^b/');
        })->to->throw('Esperance\Error');
    }

    /**
     * @test
     */
    public function length_should_be_ok_if_the_number_of_elements_is_equal()
    {
        $this->expect(array(1))->to->have->length(1);
    }

    /**
     * @test
     */
    public function length_should_be_ok_if_the_number_of_elements_is_not_equal()
    {
        $self = $this;
        $this->expect(function () use ($self) {
            $self->expect(array(1))->to->have->length(2);
        })->to->throw('Esperance\Error');
    }

    /**
     * @test
     */
    public function length_should_be_ok_if_the_length_of_string_is_equal()
    {
        $this->expect('abc')->to->have->length(3);
    }

    /**
     * @test
     */
    public function length_should_be_error_if_the_length_of_string_is_not_equal()
    {
        $self = $this;
        $this->expect(function () use ($self) {
            $self->expect('abc')->to->have->length(1);
        })->to->throw('Esperance\Error');
    }

    /**
     * @test
     */
    public function length_should_be_ok_if_the_number_of_Countable_elements_is_equal()
    {
        $this->expect(new \ArrayObject(array(1)))->to->have->length(1);
    }

    /**
     * @test
     */
    public function length_should_be_ok_if_the_number_of_Countable_elements_is_not_equal()
    {
        $self = $this;
        $this->expect(function () use ($self) {
            $self->expect(new \ArrayObject(array(1)))->to->have->length(2);
        })->to->throw('Esperance\Error');
    }

    /**
     * @test
     */
    public function events_should_be_emitted_around_assertion()
    {
        $emittedEvents = array();
        $assertion = new Assertion(NULL);
        $assertion->beforeAssertion(function () use (&$emittedEvents) {
            $emittedEvents[] = 'before_assertion';
        });
        $assertion->onAssertionSuccess(function () use (&$emittedEvents) {
            $emittedEvents[] = 'assertion_success';
        });
        $assertion->to->be(NULL);

        $this->expect($emittedEvents)->to->be(array('before_assertion', 'assertion_success'));
    }

    /**
     * @test
     */
    public function emitter_should_be_extended()
    {
        $emittedEvents = array();
        $assertion = new Assertion(0);
        $assertion->beforeAssertion(function () use (&$emittedEvents) {
            $emittedEvents[] = 'before_assertion';
        });
        $assertion->to->be(0)->and->not->to->be(1);

        $this->expect($emittedEvents)->to->be(array('before_assertion', 'before_assertion'));
    }

    /**
     * @test
     */
    public function before_throw_error_event_should_be_emitted_if_assertion_error_thrown()
    {
        $emittedEvents = array();
        $assertion = new Assertion(1);
        $assertion->beforeAssertion(function () use (&$emittedEvents) {
            $emittedEvents[] = 'before_assertion';
        });
        $assertion->onAssertionSuccess(function () use (&$emittedEvents) {
            $emittedEvents[] = 'assertion_success'; // should not be emitted
        });
        $assertion->onAssertionFailure(function () use (&$emittedEvents) {
            $emittedEvents[] = 'assertion_failure';
        });
        $this->expect(function () use ($assertion) {
            $assertion->to->be(0);
        })->to->throw('Esperance\Error');

        $this->expect($emittedEvents)->to->be(array('before_assertion', 'assertion_failure'));
    }

    /**
     * @test
     */
    public function messages_should_include_optional_descriptions()
    {
        try {
            $this->expect('abc')->explain('Deliberately incorrect length')->to->have->length(1);
        } catch (\Esperance\Error $e) {
            $this->expect($e->getMessage())->to->match('/^Deliberately incorrect length :.*/');
        }
    }

    public function expect($subject)
    {
        $assertion = new Assertion($subject);
        return $assertion;
    }
}
