<?php
/*
* GettextScanner.php - Request file
*
* This file is part common support.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Support;

/**
 * Refactored & Rewritten by Vinod Raut (vinod@livelyworks.net)
 */

/* Original Developer
 * Developer: Eslam Mahmoud contact@eslam.me
 * URL: http://eslam.me - https://github.com/eslam-mahmoud/gettext-php-scanner
 * Description: PHP class to scan files/project and create or update .po file, used for localization. Could be used to scan any type of files, It will extract all strings like __('Hello World') Or _e("Hello again.")
 */

class GettextScanner
{
    //Default scan the curnt directory, accept string as directory path or array or directories
    //Directory path mast end with '/'
    public $directory = './';

    //Pattern to match
    //(__('pattern should get me :)'),'pattern should not get me !!') and if there another __('text need translation') in the same line it will be there
    //	public $pattern = '/(__|_e|__tr)\((\'|\")(.+?)(\'|\")\)/';
    // public $pattern = '/(__|_e|__tr)\((\'|\")(.+?)(\'|\")(,.*)?\)/'; // issue: doesn't accept parameters based strings - vinod 29 jan 2020
    public $pattern = '/(__|_e|__tr|gettext)\((\'|\")(.+?)(\'|\")/';

    //Files extensions to scan, accept Array()
    public $fileExtensions = false;

    //Default output file name will
    public $fileName = 'default.po';

    //Scan the directory and sub directories
    //Try to match every line in each file with the pattern
    public function scanDir($directory = false)
    {
        if (! $directory) {
            $directory = $this->directory;
        }

        $lines = [];

        if (is_array($directory)) {
            foreach ($directory as $k => $dir) {
                $sub_lines = $this->scanDir($dir);
                $lines = array_merge($lines, $sub_lines);
            }

            return $lines;
        }

        if (! is_dir($directory)) {
            return false;
        }

        $handle = opendir($directory);
        if ($handle) {
            // Get every file or sub directory in the defined directory
            while (false !== ($file = readdir($handle))) {
                if ($file == '.' || $file == '..') {
                    continue;
                }

                $file = $directory.$file;
                // If sub directory call this function recursively
                if (is_dir($file)) {
                    $sub_lines = $this->scanDir($file.'/');
                    $lines = array_merge($lines, $sub_lines);
                } else {
                    $file_lines = $this->parseFile($file);

                    if ($file_lines) {
                        $lines = array_merge($lines, $file_lines);
                    }
                }
            }
            closedir($handle);
        }

        //Removes duplicate values from an array
        return array_unique($lines);
    }

    //Create the .po file if not exists
    //If file exist will be updated with the new lines only
    public function createPoFile($lines = [])
    {
        if (count($lines) < 1) {
            return false;
        }

        //Get the old content
        $oldContent = '';
        if (file_exists($this->fileName)) {
            $oldContent = file_get_contents($this->fileName);

            $oldEntries = [];
            $read = fopen($this->fileName, 'r') or exit("can't open the file");
            while (! feof($read)) {
                $getEntry = trim(trim(\str_replace('msgid ', '', \str_replace('msgid ', '', fgets($read)))), '"');
                if ($getEntry) {
                    $oldEntries[] = $getEntry;
                }
            }
            fclose($read);
        }
        //Open the file and append on it or create it if not there
        $file = fopen($this->fileName, 'a+') or exit('Could bot open file '.$this->fileName);
        $headMetadata = 'msgid ""
msgstr ""
"Project-Id-Version: LivelyCart PRO\n"
"Language-Team: \n"
"Language: fr\n"
"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset=UTF-8\n"
"Content-Transfer-Encoding: 8bit\n"
"X-Generator: LivelyWorks Translator System\n"';

        if (! \str_contains($oldContent, 'Project-Id-Version')) {
            fwrite($file, $headMetadata."\n\n");
        }

        foreach ($lines as $k => $line) {
            //Check to see if the line was in the file
            /* if (preg_match('/' . preg_quote($line, "/") . '/', $oldContent, $matches)) {
                continue;
            } */

            if (in_array($line, $oldEntries)) {
                continue;
            }

            fwrite($file, 'msgid "'.$line.'"'."\n".'msgstr ""'."\n\n");
        }
        fclose($file);

        if ($readFile = fopen($this->fileName, 'r')) {
            while (! feof($readFile)) {
                $fileLine = fgets($readFile);
                if (substr($fileLine, 0, 6) === 'msgid ') {
                    $msgStringId = trim(trim(\str_replace('msgid ', '', $fileLine)), '"');
                    if (! in_array($msgStringId, $lines) and ($msgStringId !== '')) {
                        $this->deleteLineInFile($this->fileName, $fileLine);
                    }
                }
            }
            fclose($readFile);
        }

        return true;
    }

    public function deleteLineInFile($file, $string)
    {
        $i = 0;
        $array = [];

        $read = fopen($file, 'r') or exit("can't open the file");
        while (! feof($read)) {
            $array[$i] = fgets($read);
            $i++;
        }
        fclose($read);

        $write = fopen($file, 'w') or exit("can't open the file");

        $skipNextOne = 0;
        foreach ($array as $a) {
            if ($skipNextOne > 0) {
                $skipNextOne--;

                continue;
            }

            // if(!strstr($a,$string)) {
            if (($string != $a)) {
                fwrite($write, $a);
            } else {
                $skipNextOne = 2;
            }
        }
        fclose($write);
    }

    //parse file to get lines
    public function parseFile($file = false)
    {
        if (! $file || ! is_file($file)) {
            return false;
        }

        //check the file extension, if there and not the same as file extension skip the file
        if ($this->fileExtensions && is_array($this->fileExtensions)) {
            $pathinfo = pathinfo($file);
            if (! in_array($pathinfo['extension'], $this->fileExtensions)) {
                return false;
            }
        }

        $lines = [];
        //Open the file
        $fh = fopen($file, 'r') or exit('Could not open file '.$file);
        $i = 1;
        while (! feof($fh)) {
            // read each line and trim off leading/trailing whitespace
            if ($s = trim(fgets($fh, 16384))) {
                // match the line to the pattern
                if (preg_match_all($this->pattern, $s, $matches)) {
                    //$matches[0] -> full pattern
                    //$matches[1] -> method __ OR _e
                    //$matches[2] -> ' OR "
                    //$matches[3] -> array ('text1', 'text2')
                    //$matches[4] -> ' OR "
                    if (! isset($matches[3])) {
                        continue;
                    }
                    //Add the lines without duplicate values
                    foreach ($matches[3] as $k => $text) {
                        if (! in_array($text, $lines)) {
                            $lines[] = $text;
                        }
                    }
                } else {
                    // complain if the line didn't match the pattern
                    error_log("Can't parse $file line $i: $s");
                }
            }
            $i++;
        }
        fclose($fh) or exit('Could not close file '.$file);

        return $lines;
    }
}
