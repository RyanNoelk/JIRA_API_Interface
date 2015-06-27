<?php
/**
 * Created by PhpStorm.
 * User: v610091
 * Date: 6/26/2015
 * Time: 11:42 AM
 */

define('__SCRIPT_ROOT', dirname(__FILE__));
require_once(__SCRIPT_ROOT . "/../JiraLogger.class.php");

class JiraLogger_test extends PHPUnit_Framework_TestCase
{

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