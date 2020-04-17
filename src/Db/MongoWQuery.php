<?php
declare(strict_types=1);

namespace App\Db;

use App\Config\Env;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\WriteConcern;
use MongoDB\Driver\Query;
use MongoDB\Driver\CursorInterface;

class MongoWQuery {
    protected $connection;
    protected $bulk;
    protected $wConcern;
    protected $db;


    public function __construct(
        MongoConnection $connection, 
        BulkWrite $bulk, 
        WriteConcern $wConcern,
        ?string $db=null
    ) {
        $this->connection = $connection->getConnection();
        $this->bulk = $bulk;
        $this->wConcern = $wConcern;
        try {
            $this->db = is_null($db) ? Env::get('DB_NAME') : $db;
            
        } catch (\InvalidArgumentException $e)  {
            die($e->getMessage());
        }
    }

    public function getDb() {
        return $this->db;
    }


    public function addQuery(string $cmd, array $data, ?array $filter=null) {
        if ($cmd != 'insert' && $cmd != 'update' && $cmd != 'delete') {
            throw new \InvalidArgumentException('Invalid query operation. Must be one among insert, update, delete.');
        }

        if ($cmd != 'insert') {
            if (is_null($filter))
                throw new \InvalidArgumentException(sprintf("Missing argument. Cannot %s without a filter clause.", $cmd ));
        }

        if ($cmd == 'insert') {
            $this->bulk->insert($data);

        }  elseif ($cmd == 'update')  {
            $this->bulk->update($filter, $data);

        } else {
            $this->bulk->delete($filter);
        }
    }


    public function execute(string $collection) {
        try  {
            $result = $this->connection->executeBulkWrite("$this->db.$collection", $this->bulk, $this->wConcern);
        
        } catch (MongoDB\Driver\Exception\BulkWriteException $e) {
            $result = $e->getWriteResult();
        
            // Check if the write concern could not be fulfilled
            if ($writeConcernError = $result->getWriteConcernError()) {
                printf("%s (%d): %s\n",
                    $writeConcernError->getMessage(),
                    $writeConcernError->getCode(),
                    var_export($writeConcernError->getInfo(), true)
                );
            }
        
            // Check if any write operations did not complete at all
            foreach ($result->getWriteErrors() as $writeError) {
                printf("Operation#%d: %s (%d)\n",
                    $writeError->getIndex(),
                    $writeError->getMessage(),
                    $writeError->getCode()
                );
            }
        } catch (\MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
            printf("Connection error: %s\n", $e->getMessage());
            exit;

        } catch (MongoDB\Driver\Exception\Exception $e) {
            printf("Other error: %s\n", $e->getMessage());
            exit;
        }
        return $result;
    }
    

    public function select(Query $query, string $collection) :CursorInterface  {
        try {
            return $this->connection->executeQuery(
                sprintf('%s.%s', $this->db, $collection),
                $query
            );
        } catch (\MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
            printf("Connection error: %s\n", $e->getMessage());
            exit;

        } catch (MongoDB\Driver\Exception\Exception $e) {
            printf("Driver error: %s\n", $e->getMessage());
            exit;
        }
        
    }
}