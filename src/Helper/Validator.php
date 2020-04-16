<?php
declare(strict_types=1);

namespace App\Helper;

use App\Db\BaseMapObject;

class Validator {
    const URL       = 'url';
    const TEXT      = 'string';
    const NAME      = 'name';
    const EMAIL     = 'email';
    const INT       = 'int';
    const FLOAT     = 'float';

    /**
     * @access public
     * Main validation cycle for BaseMapObject instances
     * Throws \InvalidArgumentException
     * 
     * @param BaseMapObject $mapObject Object to be mapped
     * @return void
     */
    public function validate(BaseMapObject $mapObject) :void {
        $types = $mapObject->getDataTypes();
        $data  = $mapObject->toArray();

        foreach ($types AS $name => $type) {
            if ($type == self::NAME) {
                $this->validName($name, $data[$name]);

            } elseif ($type == self::URL) {
                $this->validUrl($name, $data[$name]);

            } elseif ($type == self::TEXT) {
                $this->validText($name, $data[$name]);

            } elseif ($type == self::EMAIL) {
                $this->validEmail($name, $data[$name]);

            } elseif ($type == self::INT) {
                $this->validInt($name, $data[$name]);

            } elseif ($type == self::FLOAT) {
                $this->validFloat($name, $data[$name]);

            } else {
                throw new \InvalidArgumentException("Invalid data type for $name");
            }
        }
    }


    /**
     * @access protected
     * Helper to return a standard error message
     * @param string $name Parameter name
     * @return string Error message
     */
    protected function stdErrorMessage(string $name) :string {
        return "Invalid value for $name.";
    }

    /**
     * @access protected
     * Name validation (also takes array as values)
     * Throws \InvalidArgumentException
     * 
     * @param string $name Parameter name
     * @param mixed $value Parameter value to be validated
     * @return void
     */
    protected function validName(string $name, $value) :void {
        if (is_array($value)) {
            foreach ($value AS $v) {
                $this->validName($name, $v);
            }
        }

        if (preg_match('/\W+/i', $value) == 1) {
            throw new \InvalidArgumentException($this->stdErrorMessage($name));
        }
    }

    /**
     * @access protected
     * Validate url params
     * Throws \InvalidArgumentException
     * 
     * @param string $name Parameter name
     * @param mixed $value Parameter value to be validated
     * @return void
     */
    protected function validUrl(string $name, $value) :void {
        if (! filter_var($value, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException($this->stdErrorMessage($name));
        }
    }

    /**
     * @access protected
     * Validate text params
     * Throws \InvalidArgumentException
     * 
     * @param string $name Parameter name
     * @param mixed $value Parameter value to be validated
     * @return void
     */
    protected function validText(string $name, $value) :void {
        if (preg_match('/\w+\W+/i', $value) != 1) {
            throw new \InvalidArgumentException($this->stdErrorMessage($name));
        }
    }


    /**
     * @access protected
     * Validate email params
     * Throws \InvalidArgumentException
     * 
     * @param string $name Parameter name
     * @param mixed $value Parameter value to be validated
     * @return void
     */
    protected function validEmail(string $name, $value) :void {
        if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException($this->stdErrorMessage($name));
        }
    }


    /**
     * @access protected
     * Validate int params
     * Throws \InvalidArgumentException
     * 
     * @param string $name Parameter name
     * @param mixed $value Parameter value to be validated
     * @return void
     */
    protected function validInt(string $name, $value) :void {
        if (! filter_var($value, FILTER_VALIDATE_INT)) {
            throw new \InvalidArgumentException($this->stdErrorMessage($name));
        }
    }

    /**
     * @access protected
     * Validate float params
     * Throws \InvalidArgumentException
     * 
     * @param string $name Parameter name
     * @param mixed $value Parameter value to be validated
     * @return void
     */
    protected function validFloat(string $name, $value) :void {
        if (! filter_var($value, FILTER_VALIDATE_FLOAT)) {
            throw new \InvalidArgumentException($this->stdErrorMessage($name));
        }
    }
    
}