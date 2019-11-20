<?php
declare(strict_types=1);

namespace App\Db;

class GenericDbCollection {

    /**
     * For some reason php will have troubles getting values declared inside the __construct
     * method with Reflection, instead it would give back an array with null values for the 
     * class properties and another set of key / value pairs with the values taken from 
     * the object instantiation.
     * 
     * Please DO use getters/setters and protected properties in children classes to prevent
     * this beheaviuor. 
     */

    /**
     * function that returns an array with the object's properties and their values
     */
    public function toArray() :array {
        return get_object_vars($this);
    }
}