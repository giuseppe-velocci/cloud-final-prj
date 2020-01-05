<?php

declare(strict_types=1);

namespace App\Db;

use MongoDB\Driver\Query;
use MongoDB\Driver\BulkWrite;

class User extends GenericDbCollection{

	private $connection;
	private $collection = "User";

	// obj properties
	protected $id;
	protected $firstname;
	protected $lastname;
	protected $email;
	protected $password;

	// constructor
	public function __construct($dbConn){
    	$this->connection = $dbConn;
	}

	// getters and setters
	public function getConnection() {
		return $this->connection ;
    	}
	public function setConnection($connection) {
		$this->connection = $connection;
    	}

	public function getCollection(): string {
		return $this->collection ;
    	}
	public function setCollection($collection) {
		$this->collection = $collection;
    	}

	public function getId(): string {
		return $this->id ;
    	}
	public function setId($id) {
		$this->id = $id;
    	}

	public function getFirstname(): string {
		return $this->firstname ;
    	}
	public function setFirstname($firstname) {
		$this->firstname = $firstname;
    	}

	public function getLastname(): string {
		return $this->lastname ;
    	}
	public function setLastname($lastname) {
		$this->lastname = $lastname;
    	}

	public function getEmail(): string {
		return $this->email ;
    	}
	public function setEmail($email) {
		$this->email = $email;
    	}

	public function getPassword(): string {
		return $this->password ;
    	}
	public function setPassword($password) {
		$this->password = $password;
    	}

	public function AddUser(): bool {

		$bulk = new BulkWrite;
		if(
		    !empty($this->firstname) &&
		    !empty($this->lastname) &&
		    !empty($this->email) &&
		    !empty($this->password)
		){
			$password_hash = password_hash($this->password, PASSWORD_BCRYPT);

			$doc = [

			'firstname' => $this->firstname,
			'lastname' => $this->lastname,
			'email' => $this->email,
			'password' => $password_hash

			];

			$this->id = $bulk->insert($doc);
			$result = $this->connection->executeBulkWrite('cloudPrj.'.$collection, $bulk);

			if(!empty($result->getInsertedCount())){
				return true;
			}
		}
		return false;
	}

	public function EmailExists(string $email): bool{
		
		$filter = ['email'=>$email];
		$options = ['typeMap'=>'User'];
		$query = new Query($filter, $options);

		$cursor = $this->connection->executeQuery('cloudPrj.User', $query);

		if(!empty($cursor)){
			$this->id = $cursor['_id'];
			$this->firstname = $cursor['firstname'];
			$this->lastname = $cursor['lastname'];
			$this->email = $cursor['email'];
			$this->password = $cursor['password'];
			return true;
		}

		return false;
	}

	public function Update(){

		$bulk = new BulkWrite;
		if(
		    !empty($this->firstname) &&
		    !empty($this->lastname) &&
		    !empty($this->email) &&
		    !empty($this->password)
		){
			$this->password=htmlspecialchars(strip_tags($this->password));
			$password_hash = password_hash($this->password, PASSWORD_BCRYPT);

			$doc = [
			'firstname' => htmlspecialchars(strip_tags($this->firstname)),
			'lastname' => htmlspecialchars(strip_tags($this->lastname)),
			'email' => htmlspecialchars(strip_tags($this->email)),
			'password' => htmlspecialchars(strip_tags($password_hash))
			];
			
			$bulk->update(
				['_id' => $this->id],
				['$set' => $doc ]
			);
			$this->connection->executeBulkWrite('cloudPrj.'.$collection, $bulk);
			return true;
		}
		return false;
	}
}
