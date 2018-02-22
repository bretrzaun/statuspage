<?php
namespace BretRZaun\StatusPage\Check;

use BretRZaun\StatusPage\Result;

class PhpVersionCheck extends AbstractCheck
{
    /** @var string */
    protected $greaterEquals;

    /** @var string */
    protected $lessThan;

    /**
     *
     * @param string $label Label
     * @param string $greaterEquals minimum PHP version (optional)
     * @param string $lessThan PHP version has to be smaller than this (optional)
     */
    public function __construct($label, $greaterEquals = null, $lessThan = null)
    {
        parent::__construct($label);
        $this->greaterEquals = $greaterEquals;
        $this->lessThan = $lessThan;
    }

    public function getPhpVersion()
    {
        return PHP_VERSION;
    }

    /**
     * Check URL
     *
     * @return Result
     */
    public function check(): Result
    {
        $result = new Result($this->label);

        $phpVersion = $this->getPhpVersion();
        if (null !== $this->greaterEquals && !version_compare($this->greaterEquals, $phpVersion, '<=')) {
            $result->setSuccess(false);
            $result->setError("PHP version must be >= {$this->greaterEquals}; is " . $phpVersion);
        }

        if (null !== $this->lessThan && !version_compare($this->lessThan, $phpVersion, '>')) {
            $result->setSuccess(false);
            $result->setError("PHP version must be < {$this->lessThan}; is " . $phpVersion);
        }

        return $result;
    }
}
