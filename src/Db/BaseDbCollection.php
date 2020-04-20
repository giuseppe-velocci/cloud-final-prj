<?php
declare(strict_types=1);

namespace App\Db;

use App\Helper\ISanitizer;
use MongoDB\Driver\CursorInterface;
use App\Helper\Validator;


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
     * @access public
     * 
     * Constructor MUST be implemented in child classes because $collection must be provided
     * 
     * @param BaseMapObject $mapObj Object that maps the single document
     * @param MongoWQuery $wQuery Enquees and executes mongodb queries
     * @param ISanitizer $sanitizer Sanitizer class
     */
	public function __construct(
        BaseMapObject $mapObj,
        MongoWQuery $wQuery,
        ISanitizer $sanitizer
	){
        $this->mapObj = $mapObj;
		$this->wQuery = $wQuery;
        $this->sanitizer = $sanitizer;
	}

    /**
     * Return an array with data from the object as it would be used in mongo queries.
     * 
     * @param bool $withId If '_id' key must be kept in returned data. Default false
     * @return ?array = nullable array. If some data is missing it will return null
     */
    protected function setupDoc(bool $withId = false) :array{
        Validator::validate($this->mapObj); // throws exceptions

        $data = $this->mapObj->toArray(); // throws error
        unset($data['required']); // rm required and dataTypes params
        unset($data['dataTypes']);

        foreach ($this->mapObj->getRequired() AS $k) {
             if (empty($data[$k]))
                throw new \InvalidArgumentException(sprintf("Missing argument for %s", $k));
        }

        foreach($data AS $k => $v) {
            $data[$k] = $this->sanitizer->clean($v);
        }

        if (! $withId && array_key_exists('_id', $data)) {
            unset($data['_id']);
        }
        
        return $data;
    }


    /**
     * Add all queries (insert, update, delete) to the bulkWriter
     * 
     * @param string $cmd   query to setyp. Must be either insert, update or delete
     * @param ?array $filter    array with filter mongodb arguments. mandatory for update and delete
     * @return void
     */
    public function setupQuery(string $cmd, ?array $filter = null) :void {
        $doc = [];
        if ($cmd != 'delete') {
            $doc = $this->setupDoc();
        }
        
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
        if ($this->wQuery->getBulkCount() < 1) {
            throw new \InvalidArgumentException('No queries were enqueed.');
        }

        if (empty($this->wQuery->execute($this->collection)))
            return false;

        return true;
    }


    /**
     * Selection queries manager
     * 
     * @param array $filter array with filter arguments for the query
     * @param array $options array with options for the query
     * @return MongoDB\Driver\CursorInterface Mongo cursor object
     */
    public function select(array $filter, array $options=[]) :CursorInterface {
        $query = new \MongoDB\Driver\Query($filter, $options);
        return $this->wQuery->select($query, $this->collection); 
    }
}