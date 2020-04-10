<?php
declare(strict_types=1);

namespace App\Helper;

use App\Db\BaseMapObject;

class Validator {
    const URL       = 'url';
    const TEXT      = 'string';
    const NAME      = 'name';
    const EMAIL     = 'email';
    const NUMBER    = 'number';

    /**
     * Name validation
     */
    protected function validName($param) {
        //.. action
        // if wrong:
        // .. throw new \InvalidArgumentExeception('');
    }

    /**
     * @access public
     * Main validation cycle for BaseMapObject instances
     */
    public static function validate(BaseMapObject $mapObject, ServerRequestInterface $request) {
        $types = $mapObject->getDataTypes();
        $data  = $mapObject->toArray();

        foreach ($types AS $param => $type) {
            if ($type == self::NAME) {
                $this->validName($data[$param]);
            }
        }
    }
}