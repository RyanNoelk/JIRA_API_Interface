<?php
/**
 * Created by PhpStorm.
 * User: Ryan Noelk
 * Date: 6/22/2015
 *
 * This class will assist in the logging process for JIRA queries
 * There are two ways to record the log:
 *      1. To the command line
 *      2. To a specific path/file
 * The constructor takes 2 optional parameters:
 *      1. The pathname+filename if the log is to be saved to a file
 *      2. bool if the script should continue if the file cannot be created
 * Public Functions:
 *      1. print_line -> Prints $str + a newline to the log
 *      2. print_str -> Prints $str to the log
 *      3. print_break -> Prints a line to the log of length $lines (default=100)
 *      4. print_header -> Prints a header to the log with formatting
 */

namespace JIRA;


class JiraLogger {

    private $command_line_output = True;
    private $file = '';

    function __construct($filename='', $require_file_logging = False)
    {
        if ('' != $filename)
        {
            try
            {
                $this->file = fopen($filename, "w");
                $this->command_line_output = False;
            }
            catch (\Exception $e)
            {
                if ($require_file_logging)
                {
                    echo 'Caught exception: ', $e->getMessage(), "\n";
                    exit("Log file is required, please try again");
                }
                else
                    echo "\n\nFailed to open file. Will use echo.\n\n\n";

                $this->command_line_output = True;
            }
        }
        else
            echo "\n\n\n";
    }

    // Prints $str + a newline to the log
    public function print_line ($str)
    {
        if ($this->command_line_output)
            echo $str . "\n";
        else
            fwrite($this->file, $str . "\n");
    }

    // Prints $str to the log
    public function print_str ($str)
    {
        if ($this->command_line_output)
            echo $str;
        else
            fwrite($this->file, $str);
    }

    // Prints a line to the log of length $lines (default=100)
    public function print_break ($lines=100)
    {
        if ($this->command_line_output)
            for ($i=0;$i<$lines;$i++)
                echo "-";
        else
            for ($i=0;$i<$lines;$i++)
                fwrite($this->file, "-");
    }

    // Prints a header to the log with formatting
    public function print_header ($str, $lines=100)
    {
        if ($this->command_line_output)
        {
            echo "\n\n\n";
            for ($i = 0; $i < $lines; $i++)
                echo "=";
            echo "\n";
            echo "= ";
            echo "$str";
            if (strlen($str) <= $lines-1)
            {
                for ($i = strlen($str)+2; $i < $lines-1; $i++)
                    echo " ";
                echo "=";
            }
            echo "\n";
            for ($i = 0; $i < $lines; $i++)
                echo "=";
            echo "\n\n";
        }
        else
        {
            fwrite($this->file, "\n\n\n");
            for ($i = 0; $i < $lines; $i++)
                fwrite($this->file, "=");
            fwrite($this->file, "\n");
            fwrite($this->file, "= ");
            fwrite($this->file, $str);
            if (strlen($str) <= $lines-1)
            {
                for ($i = strlen($str)+2; $i < $lines-1; $i++)
                    fwrite($this->file, " ");
                fwrite($this->file, "=");
            }
            fwrite($this->file, "\n");
            for ($i = 0; $i < $lines; $i++)
                fwrite($this->file, "=");
            fwrite($this->file, "\n\n");
        }
    }

}