<?php
declare(strict_types=1);

namespace App\Db;

class User extends BaseMapObject{
	// obj properties
	protected $_id;
	protected $firstname;
	protected $lastname;
	protected $email;
	protected $password;

	protected $required = ['firstname', 'lastname', 'email', 'password'];

	// constructor
	public function __construct(
		string $id = '', 
		string $firstname = '',
		string $lastname = '',
		string $email = '',
		string $password = ''
	){
		$this->_id = $id; 
		$this->firstname = $firstname;
		$this->lastname = $lastname;
		$this->email = $email;
		$this->password = $password;
	}


	public function getId(): ?string {
		return $this->_id;
	}
	public function setId($id) {
		$this->_id= $id;
	}

	public function getFirstname(): ?string {
		return $this->firstname;
	}
	public function setFirstname($firstname) {
		$this->firstname = $firstname;
	}

	public function getLastname(): ?string {
		return $this->lastname;
	}
	public function setLastname($lastname) {
		$this->lastname = $lastname;
	}

	public function getEmail(): ?string {
		return $this->email;
	}
	public function setEmail($email) {
		$this->email = $email;
	}

	public function getPassword(): ?string {
		return $this->password;
	}
	public function setPassword(string $password) {
		$this->password = $password;
	}

	public function emailExists(string $email): bool{
		$filter = ['email'=>$email];
		$options = ['typeMap'=>'User'];
		$query = new Query($filter, $options);

		$cursor = $this->connection->executeQuery('cloudPrj.User', $query);

		if(!empty($cursor))
		{
			$this->id = $cursor['_id'];
			$this->firstname = $cursor['firstname'];
			$this->lastname = $cursor['lastname'];
			$this->email = $cursor['email'];
			$this->password = $cursor['password'];
			return true;
		}

		return false;
	}
}
