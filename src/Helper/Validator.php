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
    public static function validate(BaseMapObject $mapObject) :void {
        $required = $mapObject->getRequired();
        $types = $mapObject->getDataTypes();
        $data  = $mapObject->toArray();

        foreach ($types AS $name => $type) {
            // check if value is required to handle null and empty values
            $isRequired = in_array($name, $required);
            if (is_null($data[$name]) || empty($data[$name])) {
                if (! $isRequired) {
                    continue;
                } else {
                    throw new \InvalidArgumentException("Parameter $name cannot be empty!");
                }
            }

           // then cycle
            if ($type == self::NAME) {
                self::validName($name, $data[$name]);

            } elseif ($type == self::URL) {
                self::validUrl($name, $data[$name]);

            } elseif ($type == self::TEXT) {
                self::validText($name, $data[$name]);

            } elseif ($type == self::EMAIL) {
                self::validEmail($name, $data[$name]);

            } elseif ($type == self::INT) {
                self::validInt($name, $data[$name]);

            } elseif ($type == self::FLOAT) {
                self::validFloat($name, $data[$name]);

            } else {
                throw new \InvalidArgumentException("Invalid data type declared for $name");
            }
        }
    }


    /**
     * @access protected
     * Helper to return a standard error message
     * @param string $name Parameter name
     * @return string Error message
     */
    protected static function stdErrorMessage(string $name) :string {
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
    protected static function validName(string $name, $value) :void {
        if (is_array($value)) {
            foreach ($value AS $v) {
                self::validName($name, $v);
            }
        }

        if (preg_match('/\W+/i', $value) == 1) {
            throw new \InvalidArgumentException(self::stdErrorMessage($name));
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
    protected static function validUrl(string $name, $value) :void {
        if (! filter_var($value, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException(self::stdErrorMessage($name));
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
    protected static function validText(string $name, $value) :void {
        if (preg_match('/\w+\W+/i', $value) != 1) {
            throw new \InvalidArgumentException(self::stdErrorMessage($name));
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
    protected static function validEmail(string $name, $value) :void {
        if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(self::stdErrorMessage($name));
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
    protected static function validInt(string $name, $value) :void {
        if (! filter_var($value, FILTER_VALIDATE_INT)) {
            throw new \InvalidArgumentException(self::stdErrorMessage($name));
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
    protected static function validFloat(string $name, $value) :void {
        if (! filter_var($value, FILTER_VALIDATE_FLOAT)) {
            throw new \InvalidArgumentException(self::stdErrorMessage($name));
        }
    }
    
}