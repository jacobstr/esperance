<?php namespace Esperance\Tests;

use Esperance\Assertion;

describe('Assertions', function ($ctx) {
    before(function ($ctx) {
        $ctx->expect = function($subject) {
            return new Assertion($subject);
        };
    });

    describe('basic', function($ctx) {
        it('should be ok if subject is equal to object', function ($ctx) {
            $ctx->expect(1)->to->be(1);
        });

        it('should be error if subject is not equal to object', function($ctx) {
            $ctx->expect(function () use ($ctx) {
                $ctx->expect(1)->to->be(2);
            })->to->throw('Esperance\Error', 'expected 1 to equal 2');
        });

        it('should be ok if subject is loosely equal to object', function($ctx) {
            $ctx->expect("1")->to->eql(1);
        });

        it("eql should be error if subject is not equal to object", function ($ctx) {
            $ctx->expect(function () use ($ctx) {
                $ctx->expect(1)->to->eql(2);
            })->to->throw('Esperance\Error', 'expected 1 to sort of equal 2');
        });

        it("not be should be ok if subject is not equal to object", function ($ctx) {
            $ctx->expect(1)->to->not->be(2);
        });

        it("not be should be error if subject is equal to object", function ($ctx) {
            $ctx->expect(function () use ($ctx) {
                $ctx->expect(1)->to->not->be(1);
            })->to->throw('Esperance\Error', 'expected 1 to not equal 1');
        });
    });

    it("throw should be ok if expected exception is thrown", function ($ctx) {
        $ctx->expect(function () {
            throw new \RuntimeException;
        })->to->throw('RuntimeException');
    });

    describe("within", function($ctx) {
        it("should be okay at lower open bound", function ($ctx) {
            $ctx->expect(5)->to->be->within(5,6);
        });

        it("should be okay at higher open bound", function ($ctx) {
            $ctx->expect(5)->to->be->within(4,5);
        });

        it("should not be okay above higher open bound", function ($ctx) {
            $ctx->expect(5)->to->not->be->within(3,4);
        });

        it("should not be okay below lower open bound", function ($ctx) {
            $ctx->expect(5)->to->not->be->within(6,7);
        });
    });

    describe("resemble", function($ctx) {
        it("should be okay if target is a subset of source", function ($ctx) {
            $ctx->expect(array(
                "cats" => array("sam"),
                "dogs" => array("pugly")
            ))->to->resemble(array(
                "cats" => array("sam")
            ));
        });

        it("should not be okay if source is not contained in target", function ($ctx) {
            $ctx->expect(array(
                "cats" => array("sam"),
            ))->to->not->resemble(array(
                "cats" => array("sam"),
                "dogs" => array("pugly")
            ));
        });

        it("should tolerate type differences", function ($ctx) {
            $ctx->expect(array(
                "cats" => 5,
            ))->to->resemble(array(
                "cats" => "5",
            ));
        });

        it("compare deeply", function($ctx) {
            $ctx->expect(array(
                "haxors" => array(
                    "alan kay" => array("profession" => "haxor")
                )
            ))->to->resemble(array(
                "haxors" => array(
                    "alan kay" => array("profession" => "haxor"),
                )
            ));
        });

        it("should invert deeper comparison failures", function($ctx) {
            $ctx->expect(array(
                "haxors" => array(
                    "alan kay" => array("profession" => "haxor")
                )
            ))->to->not->resemble(array(
                "haxors" => array(
                    "alan kay" => array("profession" => "turing award sergeant at arms"),
                )
            ));
        });
    });
});

