<?php
namespace BretRZaun\StatusPage\Check;

use BretRZaun\StatusPage\Result;

class PhpMemoryLimitCheck extends AbstractCheck
{

    /**
     *
     * @param string $label Label
     * @param string|int $memoryRequired amount of memory that is (at least) required in megabytes
     */
    public function __construct(string $label, protected $memoryRequired)
    {
        parent::__construct($label);
    }

    /**
     * Returns size in megabytes from a PHP size string like '1024M'.
     *
     * @param string $sizeStr
     * @return float|int
     */
    public function getMegabytesFromSizeString($sizeStr)
    {
        return match (substr($sizeStr, -1)) {
            'M', 'm' => (int) $sizeStr,
            'K', 'k' => (int) $sizeStr / 1024,
            'G', 'g' => (int) $sizeStr * 1024,
            default => (int) $sizeStr,
        };
    }

    /**
     * Check URL
     *
     * @return Result
     */
    public function checkStatus(): Result
    {
        $result = new Result($this->label);
        $memoryLimitString = ini_get('memory_limit');
        $memoryLimit = $this->getMegabytesFromSizeString($memoryLimitString);

        if ($this->memoryRequired > $memoryLimit) {
            $result->setError("Memory required: {$this->memoryRequired}M; limit: {$memoryLimitString}");
        }

        return $result;
    }
}
