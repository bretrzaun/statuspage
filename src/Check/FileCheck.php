<?php
namespace BretRZaun\StatusPage\Check;

use BretRZaun\StatusPage\Result;

class FileCheck extends AbstractCheck
{

    /**
     * @var string
     */
    protected $filename;

    /**
     * @var boolean
     */
    protected $writable;

    /**
     * @var integer
     */
    protected $maxage;

    /**
     * @var string
     */
    protected $unwantedRegex;

    /**
     * FileCheck constructor.
     * @param $label
     * @param $filename
     */
    public function __construct($label, $filename)
    {
        parent::__construct($label);
        $this->filename = $filename;
    }

    /**
     * @param string $filename
     * @return FileCheck
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return FileCheck
     */
    public function setWritable()
    {
        $this->writable = true;
        return $this;
    }

    /**
     * @param int $maxage
     * @return FileCheck
     */
    public function setMaxage($maxage)
    {
        $this->maxage = $maxage;
        return $this;
    }

    /**
     * @param string $unwantedRegex
     * @return FileCheck
     */
    public function setUnwantedRegex($unwantedRegex)
    {
        $this->unwantedRegex = $unwantedRegex;
        return $this;
    }


    /**
     * @return Result
     */
    public function checkStatus(): Result
    {
        $result = new Result($this->label);

        if (!file_exists($this->filename)) {
            $result->setSuccess(false);
            $result->setError($this->filename." does not exist!");
            return $result;
        }

        if (null !== $this->maxage) {
            $mtime = filemtime($this->filename);
            if ($mtime === false) {
                $result->setError("mtime() returns error");
                return $result;
            }
            $age = (time() - $mtime);
            $age = round($age / 60); // sec-to-min
            if ($age > (int) $this->maxage) {
                $result->setError($this->filename." is to old!");
                return $result;
            }
        }

        if (null !== $this->writable && !is_writable($this->filename)) {
            $result->setError($this->filename." is not writable!");
            return $result;
        }

        if (null !== $this->unwantedRegex) {
            $fp = fopen($this->filename, 'r');
            if ($fp === false) {
                $result->setError("fopen() returns error");
                return $result;
            }
            $linenr = 0;
            while ($line = fgets($fp)) {
                $linenr++;
                if (preg_match('~'.$this->unwantedRegex.'~i', $line)) {
                    $result->setError("Found '".$this->unwantedRegex."' in '".$line."' [".$this->filename.":".$linenr."]");
                    fclose($fp);
                    return $result;
                }
            }
            fclose($fp);
        }

        return $result;
    }

}
