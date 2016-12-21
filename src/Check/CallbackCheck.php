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
    public function __construct($label, callable $callback)
    {
        parent::__construct($label);
        $this->callback = $callback;
    }

    /**
     * Check callback
     *
     * @return Result
     */
    public function check()
    {
        $result = new Result($this->label);
        try {
            $return = call_user_func($this->callback);
            if ($return !== true) {
                $result->setSuccess(false);
                $result->setError($return);
            }
        } catch (\Exception $e) {
            $result->setSuccess(false);
            $result->setError($e->getMessage());
        }
        return $result;
    }
}
