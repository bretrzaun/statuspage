<?php

namespace BretRZaun\StatusPage\Check;

use BretRZaun\StatusPage\Result;

class PhpExtensionCheck extends AbstractCheck
{
    /** @var string */
    protected $extension;

    /** @var string */
    protected $greaterEquals;

    /** @var string */
    protected $lessThan;

    /**
     * @param string $label         Label
     * @param string $extension     extension to be tested
     * @param string $greaterEquals minimum version (optional)
     * @param string $lessThan      version has to be smaller than this (optional)
     */
    public function __construct(string $label, string $extension, string $greaterEquals = null, string $lessThan = null)
    {
        parent::__construct($label);
        $this->extension = $extension;
        $this->greaterEquals = $greaterEquals;
        $this->lessThan = $lessThan;
    }

    /**
     * Gets version constraint string for display within the error message.
     */
    protected function getVersionConstraintString(): string
    {
        $strings = [];
        if (null !== $this->greaterEquals) {
            $strings[] = '>=' . $this->greaterEquals;
        }
        if (null !== $this->lessThan) {
            $strings[] = '<' . $this->lessThan;
        }

        return implode(' and ', $strings);
    }

    /**
     * Finds out whether the extension is loaded.
     */
    protected function isExtensionLoaded(): bool
    {
        return \extension_loaded($this->extension);
    }

    /**
     * Gets the installed extension version.
     */
    protected function getExtensionVersion(): string
    {
        return phpversion($this->extension);
    }

    /**
     * Check URL
     */
    public function checkStatus(): Result
    {
        $result = new Result($this->label);
        if ($this->isExtensionLoaded()) {
            $version = $this->getExtensionVersion();
            if ((null !== $this->greaterEquals && !version_compare($this->greaterEquals, $version, '<=')) ||
                (null !== $this->lessThan && !version_compare($this->lessThan, $version, '>'))) {
                $result->setError('Extension ' . $this->extension . ' loaded, but version ' . $version . ' not supported<br/>Version must be ' . $this->getVersionConstraintString());
            }
        } else {
            $result->setError('Extension ' . $this->extension . ' is missing');
        }

        return $result;
    }
}
