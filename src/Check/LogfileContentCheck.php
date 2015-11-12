<?php
namespace BretRZaun\StatusPage\Check;

use BretRZaun\StatusPage\Result;

class LogfileContentCheck extends AbstractCheck
{

    protected $filename;
    protected $content;

    public function __construct($label, $filename)
    {
        parent::__construct($label);
        $this->filename = $filename;
    }

    public function setCheckfor($content)
    {
        $this->content = $content;
    }

    public function check()
    {
        $result = new Result($this->label);

        if (!file_exists($this->filename)) {
            $result->setSuccess(false);
            $result->setError("Log file $this->filename does not exist!");
            return $result;
        }

        if (!empty($this->content)) {
            $fileContent = file_get_contents($this->filename);
            if (strstr($fileContent, $this->content) === false) {
                $result->setSuccess(false);
                $result->setError('Log file failure');
                $result->setDetails('Timestamp: '.date('d.m.Y H:i', filemtime($this->filename)));
            }
        }
        return $result;
    }
}
