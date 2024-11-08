<?php
namespace BretRZaun\StatusPage\Check;

use Throwable;
use BretRZaun\StatusPage\Result;

class CallbackCheck extends AbstractCheck
{

    /**
     * @var callable
     */
    protected $callback;

    /**
     * Constructor
     */
    public function __construct(string $label, callable $callback)
    {
        parent::__construct($label);
        $this->callback = $callback;
    }

    /**
     * Check callback
     */
    public function checkStatus(): Result
    {
        $result = new Result($this->label);
        try {
            \call_user_func($this->callback, $result);
        } catch (Throwable $e) {
            $result->setError($e->getMessage());
        }
        return $result;
    }
}
