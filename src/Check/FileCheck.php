<?php
namespace BretRZaun\StatusPage\Check;

use BretRZaun\StatusPage\Result;

/**
 * checks if the given file exists
 *
 * Options:
 * - *writable*: checks if the file is writable
 *
 * @package BretRZaun\StatusPage\Check
 */
class FileCheck extends AbstractCheck
{

    /**
     * @var string
     */
    protected $filename;

    /**
     * @var array
     */
    protected $options;

    /**
     * FileCheck constructor.
     * @param $label
     * @param $filename
     * @param array $options
     */
    public function __construct(string $label, string $filename, array $options = [])
    {
        parent::__construct($label);
        $this->filename = $filename;
        $this->options = $options;
    }

    public function isWritable()
    {
        $this->options['writable'] = true;
        return $this;
    }

    public function check(): Result
    {
        $result = new Result($this->label);

        if (!file_exists($this->filename)) {
            $result->setSuccess(false);
            $result->setError("$this->filename does not exist!");
            return $result;
        }

        if (array_key_exists('writable', $this->options)) {
            if (!is_writable($this->filename)) {
                $result->setSuccess(false);
                $result->setError("$this->filename is not writable!");
            }
        }

        return $result;
    }
}
