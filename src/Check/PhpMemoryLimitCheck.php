<?php
namespace BretRZaun\StatusPage\Check;

use BretRZaun\StatusPage\Result;

class PhpMemoryLimitCheck extends AbstractCheck
{

    /**
     * @var string
     */
    protected $memoryRequired;

    /**
     *
     * @param string $label Label
     * @param string $memoryRequired amount of memory that is (at least) required in megabytes
     */
    public function __construct($label, $memoryRequired)
    {
        parent::__construct($label);
        $this->memoryRequired = $memoryRequired;
    }

    /**
     * Returns size in megabytes from a PHP size string like '1024M'.
     *
     * @param $sizeStr
     * @return float|int
     */
    public function getMegabytesFromSizeString($sizeStr)
    {
        switch (substr($sizeStr, -1)) {
            case 'M':
            case 'm':
                return (int)$sizeStr;

            case 'K':
            case 'k':
                return (int)$sizeStr / 1024;

            case 'G':
            case 'g':
                return (int)$sizeStr * 1024;

            default:
                return $sizeStr;
        }
    }

    /**
     * Check URL
     *
     * @return Result
     */
    public function check(): Result
    {
        $result = new Result($this->label);
        $memoryLimitString = ini_get('memory_limit');
        $memoryLimit = $this->getMegabytesFromSizeString($memoryLimitString);

        if ($this->memoryRequired > $memoryLimit) {
            $result->setSuccess(false);
            $result->setError("Memory required: {$this->memoryRequired}M; limit: {$memoryLimitString}");
        }

        return $result;
    }
}
