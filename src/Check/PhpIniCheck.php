<?php
namespace BretRZaun\StatusPage\Check;

use BretRZaun\StatusPage\Result;

class PhpIniCheck extends AbstractCheck
{

    const TypeBoolean = 'boolean';
    const TypeMemory  = 'memory';
    const TypeNumber  = 'number';
    const TypeRegex   = 'regex';
    const TypeString  = 'string';

    protected $varName;
    protected $varType;
    protected $varValue;
    protected $maxValue;
    protected $iniValue;

    /**
     * PhpIniCheck constructor.
     *
     * @param string $label
     * @param string $varName <b>Name</b> of ini value - used for ini_get(...)
     * @param string $varType <b>Type</b> of ini value - see class constant
     * @param mixed $varValue <b>Value</b> which is expected
     * @param null $maxValue <b>maximum</b> value is optional used for numbers
     *
     * @return PhpIniCheck
     */
    public function __construct(string $label, string $varName, string $varType, $varValue, $maxValue = null)
    {
        parent::__construct($label);
        $this->varName = $varName;
        $this->varType = $varType;
        $this->varValue = $varValue;
        $this->maxValue = $maxValue;
        $this->iniValue = ini_get($varName);
    }

    /**
     * @param string $size
     * @return int|bool(false)
     */
    protected function stringToMegabyte(string $size)
    {
        $value = preg_replace('~[^0-9]*~', '', $size);
        switch (substr(strtolower($size), -1)) {
            case 'm':
                return (int) $value;
            case 'k':
                return (int) round((int) $value / 1024);
            case 'g':
                return ((int) $value * 1024);
            default:
                return false;
        }
    }


    /**
     * @return Result
     */
    public function checkStatus(): Result
    {
        switch ($this->varType) {
            case self::TypeBoolean:
                return $this->checkBoolean();
            case self::TypeMemory:
                return $this->checkMemory();
            case self::TypeNumber:
                return $this->checkNumber();
            case self::TypeRegex:
                return $this->checkRegex();
            case self::TypeString:
                return $this->checkString();
            default:
                $result = new Result($this->label);
                $result->setError("Invalid Type: ".$this->varType);
                return $result;
        }
    }

    /**
     * @return Result
     */
    protected function checkBoolean()
    {
        $result = new Result($this->label);
        // some boolval advance
        switch (strtolower($this->iniValue)) {
            case 'on':
                $this->iniValue = true;
                break;
            case 'off':
                $this->iniValue = false;
                break;
            case 'yes':
                $this->iniValue = true;
                break;
            case 'no':
                $this->iniValue = false;
                break;
        }

        if (boolval($this->iniValue) !== boolval($this->varValue)) {
            $result->setError("php.ini value of '".$this->varName."' is set to '".strval(boolval($this->iniValue))."' instead of expected '".strval(boolval($this->varValue))."'");
        }
        return $result;
    }

    /**
     * @return Result
     */
    protected function checkMemory()
    {
        $result = new Result($this->label);
        if ($this->iniValue != -1) {
            $value = $this->stringToMegabyte($this->iniValue);
            if ($value < $this->varValue) {
                $result->setError(
                    "php.ini value of '".$this->varName."' is set to '".
                    strval($this->iniValue)."', minimum expected is '".
                    strval($this->varValue)."'"
                );
            }
        }
        return $result;
    }

    /**
     * @return Result
     */
    protected function checkNumber()
    {
        $result = new Result($this->label);
        if (!is_null($this->varValue)) {
            if ($this->iniValue < $this->varValue) {
                $result->setError("php.ini value of '".$this->varName."' is set to '".strval($this->iniValue)."', minimum expected is '".strval($this->varValue)."'");
                return $result;
            }
        }
        if (!is_null($this->maxValue)) {
            if ($this->iniValue > $this->maxValue) {
                $result->setError("php.ini value of '".$this->varName."' is set to '".strval($this->iniValue)."', maximum expected is '".strval($this->maxValue)."'");
                return $result;
            }
        }
        $result->setDetails("php.ini value of '".$this->varName."' is set to '".strval($this->iniValue)."'");
        return $result;
    }

    /**
     * @return Result
     */
    protected function checkRegex()
    {
        $result = new Result($this->label);
        if (!preg_match('~'.$this->varValue.'~', $this->iniValue)) {
            $result->setError(
                "php.ini value of '".$this->varName."' is set to '".
                strval($this->iniValue)."', expected is '".
                strval($this->varValue)."'"
            );
        }
        return $result;
    }

    /**
     * @return Result
     */
    protected function checkString()
    {
        $result = new Result($this->label);
        if (strval($this->iniValue) != strval($this->varValue)) {
            $result->setError("php.ini value of '".$this->varName."' is set to '".strval($this->iniValue)."', expected is '".strval($this->varValue)."'");
        }
        return $result;
    }
}
