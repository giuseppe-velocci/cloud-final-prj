<?php
declare(strict_types=1);

namespace App\Db;

use App\Helper\ISanitizer;

abstract class BaseDbCollection {
    /**
     * @access protected
     * @var App\Helper\ISanitizer $sanitizer Helper class to sanitize data
     */
    protected $sanitizer;

    /**
     * @access protected
     * @var string $collection Collection name
     */
    protected $collection;

    /**
     * @access protected
     * @var App\Db\MongoWQuery $wQuery Enquees and executes mongodb queries
     */
    protected $wQuery;

    /**
     * @access public
     * @var App\Db\BaseMapObject $mapObj Object that maps the single document
     */
    public $mapObj;


    /**
     * @access protected
     * @var array $requiredMapObjParams Array with a list of the mandatory params for the mapped object
     */
    protected $requiredMapObjParams;

    /**
     * @access public
     * 
     * Constructor MUST be implemented in child classes because $collection must be provided
     * 
     * @param BaseMapObject $mapObj Object that maps the single document
     * @param MongoWQuery $wQuery Enquees and executes mongodb queries
     * @param ISanitizer $sanitizer Sanitizer class
     * @param array $requiredMapObjParams Array of strings with params name that are required for the docs in this collection
     * @param string $collection Mongo Collection name
     */
	public function __construct(
        BaseMapObject $mapObj,
        MongoWQuery $wQuery,
        ISanitizer $sanitizer,
        array $requiredMapObjParams,
        string $collection
	){
        $this->mapObj = $mapObj;
		$this->wQuery = $wQuery;
        $this->sanitizer = $sanitizer;
        $this->requiredMapObjParams = $requiredMapObjParams;
        $this->collection = $collection;
	}

    /**
     * Return an array with data from the object as it would be used in mongo queries.
     * 
     * @param bool $withId If '_id' key must be kept in returned data. Default false
     * @return ?array = nullable array. If some data is missing it will return null
     */
    protected function setupDoc(bool $withId = false) :array{
        $data = $this->mapObj->toArray();

        foreach ($this->requiredMapObjParams AS $k) {
             if (empty($data[$k]))
                throw new \InvalidArgumentException(sprintf("Missing argument for %s", $k));
        }

        array_map($this->sanitizer->clean, $data);

        if (! $withId && array_key_exists('_id', $data))
            unset($data['_id']);
        
        return $data;
    }



    /**
     * Execute a generic mongo write query. Data to write will be taken from setupDoc() method.
     * 
     * @param string $cmd = must be one of insert, update, delete
     * @param ?array $filter = nullable array with filters. Mandatory for update and delete
     * @return bool = true/false for success/failure
     */
/*    protected function executeQuery(string $cmd, ?array $filter=null) :bool {
		try {
            $doc = $this->setupDoc();
			$this->wQuery->addQuery($cmd, $doc, $filter);
		} catch (\InvalidArgumentException $e) {
			die($e->getMessage());
        }
        
        if (empty($this->wQuery->execute($this->collection)))
            return false;

        return true;
    }

    public function insert() :bool {
		return $this->executeQuery('insert');
    }
    
    public function update(array $filter) :bool {
        return $this->executeQuery('update', ['$set' => $doc ], $filter);
    }

    public function delete(array $filter){
        return $this->executeQuery('delete', [], $filter);
    }
*/
    /**
     * Add all queries (insert, update, delete) to the bulkWriter
     * 
     * @param string $cmd   query to setyp. Must be either insert, update or delete
     * @param ?array $filter    array with filter mongodb arguments. mandatory for update and delete
     * @return void
     */
    public function setupQuery(string $cmd, ?array $filter = null): void {
        $doc = $this->setupDoc();
        if ($cmd == 'update') 
            $doc = ['$set' => $doc];

        $this->wQuery->addQuery($cmd, $doc, $filter);
    }

    /**
     * Run all queries stored with setupQuery method to this bulkWriter
     * 
     * @return bool true on success, false otherwise
     */
    public function executeQueries() :bool {
        if (empty($this->wQuery->execute($this->collection)))
            return false;

        return true;
    }

}