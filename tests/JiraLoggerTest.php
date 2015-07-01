<?php
/**
 * Created by PhpStorm.
 * User: Ryan Noelk
 * Date: 6/26/2015
 *
 * 5 test cases for the logging class:
 *      testEcho - test the basic console output
 *      testEchoHeader - testing printing the header to the terminal
 *      testFileFailEcho - test that a file will fail gracefully and then print ot the terminal
 *      testFileEcho - tests that output will be saved to a file
 *      testFileEchoHeader - tests that a header will be saved to a file
 */

define('__SCRIPT_ROOT', dirname(__FILE__));
require_once(__SCRIPT_ROOT . "/../JIRA/JiraLogger.class.php");

class JiraLogger_test extends PHPUnit_Framework_TestCase
{
    // test the basic console output
    public function testEcho()
    {
        $this->expectOutputString(
           "\n\n\n" .
           "Hello World\n" .
           "----------" .
           "Hello Test"
        );

        $log = new \JIRA\JiraLogger();
        $log->print_line("Hello World");
        $log->print_break(10);
        $log->print_str("Hello Test");
    }

    // testing printing the header to the terminal
    public function testEchoHeader()
    {
        $this->expectOutputString(
            "\n\n\n" .
            "\n\n\n" .
            "===============\n" .
            "= Heading 1   =\n" .
            "===============" .
            "\n\n"
        );

        $log = new \JIRA\JiraLogger();
        $log->print_header("Heading 1", $lines=15);
    }

    // test that a file will fail gracefully and then print ot the terminal
    public function testFileFailEcho()
    {
        $this->expectOutputString(
            "\n\nFailed to open file. Will use echo.\n\n\n" .
            "Hello World\n" .
            "----------" .
            "Hello Test"
        );

        $log = new \JIRA\JiraLogger('/ryan/home/tmp.txt');
        $log->print_line("Hello World");
        $log->print_break(10);
        $log->print_str("Hello Test");
    }

    // tests that output will be saved to a file
    public function testFileEcho()
    {
        $output =
            "Hello World\n" .
            "----------" .
            "Hello Test"
        ;

        $log = new \JIRA\JiraLogger('/tmp/testFileEcho.txt');
        $log->print_line("Hello World");
        $log->print_break(10);
        $log->print_str("Hello Test");

        $this->assertEquals($output, file_get_contents('/tmp/testFileEcho.txt'));
    }

    // tests that a header will be saved to a file
    public function testFileEchoHeader()
    {
        $output =
            "\n\n\n" .
            "===============\n" .
            "= Heading 1   =\n" .
            "===============" .
            "\n\n"
        ;

        $log = new \JIRA\JiraLogger('/tmp/testFileEchoHeader.txt');
        $log->print_header("Heading 1", $lines=15);

        $this->assertEquals($output, file_get_contents('/tmp/testFileEchoHeader.txt'));
    }

}
