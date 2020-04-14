<?php
namespace BretRZaun\StatusPage\Check;

use BretRZaun\StatusPage\Result;

class CallbackCheck extends AbstractCheck
{

    /**
     * @var callable
     */
    protected $callback;

    /**
     * Constructor
     *
     * @param $label
     * @param callable $callback
     */
    public function __construct(string $label, callable $callback)
    {
        parent::__construct($label);
        $this->callback = $callback;
    }

    /**
     * Check callback
     *
     * @return Result
     */
    public function check(): Result
    {
        $result = new Result($this->label);
        try {
            $return = \call_user_func($this->callback, $this->label);
            if ($return instanceof Result) {
                $result = $return;
            } else if ($return !== true) {
                $result->setSuccess(false);
                $result->setError($return);
            }
        } catch (\Throwable $e) {
            $result->setSuccess(false);
            $result->setError($e->getMessage());
        }
        return $result;
    }
}
