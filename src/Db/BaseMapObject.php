<?php
declare(strict_types=1);

namespace App\Db;

use App\Helper\Validator;

/**
 * For some reason php will have troubles getting values declared inside the __construct
 * method with Reflection, instead it would give back an array with null values for the 
 * class properties and another set of key / value pairs with the values taken from 
 * the object instantiation.
 * 
 * Please DO use getters/setters and protected properties in children classes to prevent
 * this beheaviuor. 
 */

class BaseMapObject {
    
    /**
     * @access protected
     * @var array $required List of required parameters for object creation.
     */
    protected $required;

    /**
     * Returns the list of required params for this object to be valid.
     * @access public
     * @return array List of required parameters for object creation.
     */
    public function getRequired() {
        return $this->required;
    }

    /**
     * @access protected
     * @var array $dataTypes List of types for validation. Format: [property => type]
     */
    protected $dataTypes;

    /**
     * Returns the list of required params for this object to be valid.
     * @access public
     * @return array List of data types for validation.
     */
    public function getDataTypes() {
        return $this->dataTypes;
    }

    /**
     * Returns an array with the object's properties and their values
     * @access public
     * @return array = all properties as key values pair
     */
    public function toArray() :array {
        return get_object_vars($this);
    }

}