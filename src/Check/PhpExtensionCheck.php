<?php
namespace BretRZaun\StatusPage\Check;

use BretRZaun\StatusPage\Result;

class PhpExtensionCheck extends AbstractCheck
{

    /**
     * @var string
     */
    protected $extension;

    /**
     *
     * @param string $label Label
     * @param string $extension extension to be tested
     */
    public function __construct($label, $extension)
    {
        parent::__construct($label);
        $this->extension = $extension;
    }

    /**
     * Check URL
     *
     * @return Result
     */
    public function check(): Result
    {
        $result = new Result($this->label);
        if (!\extension_loaded($this->extension)) {
            $result->setSuccess(false);
            $result->setError('Extension '.$this->extension.' is missing');
        }
        return $result;
    }
}
