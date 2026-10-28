<?php
require_once 'PEAR.php';
setlocale(LC_ALL, 'ja_JP.UTF8');


class File_MBCSV extends PEAR
{
    var $_fp;
    var $_bufferSize = 0;

    var $_inputEncoding = 'SJIS-win';
    var $_outputEncoding = '';
    var $_tempFile = '';
    var $_delimiter = ',';
    var $_enclosure = '"';


    function __construct()
    {
        parent::__construct();
        $this->_tempFile = tempnam("/tmp", "CSV");
    }

    function _File_MBCSV()
    {
        @fclose($this->_fp);
        @unlink($this->_tempFile);

    }

    function open($csvFile)
    {
        if (!file_exists($csvFile)) {
            return PEAR::raiseError('CSV File not exists.');
        }
        $contents = file_get_contents($csvFile);

        if (!$fp = @fopen($this->_tempFile, 'wb')) {
            return PEAR::raiseError("Temporary file open error");
        }

        fwrite($fp, mb_convert_encoding($contents, 'UTF-8', $this->_inputEncoding));
        fclose($fp);

        if (!$this->_fp = @fopen($this->_tempFile, 'rb')) {
            return PEAR::raiseError("Temporary file open error");
        }

        if (!$this->_bufferSize) {
            // TODO: php 5.0.4
            $this->_bufferSize = filesize($this->_tempFile);
        }

        if (!$this->_outputEncoding) {
            $this->_outputEncoding = $this->_inputEncoding;
        }

        return true;
    }

    function close()
    {
        fclose($this->_fp);
    }

    function setTempFile($file)
    {
        $this->_tempFile = $file;
    }


    function setOutputEncoding($encoding)
    {
        $this->_outputEncoding = $encoding;
    }


    function fetchRow()
    {
        $line = fgetcsv($this->_fp, $this->_bufferSize, $this->_delimiter, $this->_enclosure);
        if ($line) {
            mb_convert_variables($this->_outputEncoding, 'UTF-8', $line);
        }
        return $line;
    }


    function fetchAll($skipRow = 0)
    {
        $skipRow = (int)$skipRow;

        $rows = array();
        while ($row = $this->fetchRow()) {
            if ($skipRow) {
                $skipRow--;
                continue;
            }
            $rows[] = $row;
        }
        return $rows;
    }


    function setBufferSize($bufferSize)
    {
        $this->_bufferSize = $bufferSize;
    }


    function setDelimiter($delimiter)
    {
        $this->_delimiter = $delimiter;
    }

    function setEnclosure($enclosure)
    {
        $this->_enclosure = $enclosure;
    }


}
