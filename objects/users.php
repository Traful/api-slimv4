<?php
	namespace objects;

	use objects\Base;

	class Users extends Base {
		private $table_name = "users";

		// constructor
		public function __construct($db) {
			parent::__construct($db);
		}

		public function getUsers() {
			$query = "SELECT * FROM $this->table_name ORDER BY id";
			parent::getAll($query);
			return $this;
		}

		public function getUser($id) {
			$query = "SELECT * FROM $this->table_name WHERE id = :id";
			parent::getOne($query, ["id" => $id]);
			return $this;
		}

		public function setUser($values) {
			$query = "INSERT INTO $this->table_name SET email = :email, firstname = :firstname, lastname = :lastname, password = :password";
			$values["password"] = password_hash($values["password"], PASSWORD_BCRYPT);
			parent::add($query, $values);
			return $this;
		}

		public function updateUser($values) {
			$query = "INSERT INTO $this->table_name SET email = :email, firstname = :firstname, lastname = :lastname, password = :password WHERE id = :id";
			parent::update($query, $values);
			return $this;
		}

		public function deleteUser($id) {
			$query = "DELETE FROM $this->table_name WHERE id = :id";
			parent::delete($query, ["id" => $id]);
			return $this;
		}

		public function userExist($email) {
			$query = "SELECT * FROM $this->table_name WHERE email = :email";
			parent::getOne($query, ["email" => $email]);
			$userE = parent::getResult();
			if($userE->ok) {
				return $userE->data;
			}
			return false;
		}
	}
?>