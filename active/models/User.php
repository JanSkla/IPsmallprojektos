<?php
    class User
    {
        public ?string $login;
        public ?string $password;
        public ?int $id;
        public static function readPost() : self
        {
            $user = new User();
            $user->login = filter_input(INPUT_POST, 'login');
            $user->password = filter_input(INPUT_POST, 'password');

            return $user;
        }
        public function validate(&$errors) : bool
        {
            if (!isset($this->login) || (!$this->login))
                $errors['login'] = 'Login nesmí být prázdné';
        
            if (!isset($this->password) || (!$this->password))
                $errors['password'] = 'Heslo nesmí být prázdné';

            $query = 'SELECT employee_id FROM employee WHERE login = "'.$this->login.'" AND password = "'.$this->password.'"';
            $stmt = PDOProvider::get()->prepare($query);
            $stmt->execute([]);
            $result = $stmt->fetch();

            $this->id = $result->employee_id;

            if(!isset($this->id)){
                $errors['isvalid'] = 'Uživatel s těmito údaji neexistuje';
            }

            return count($errors) === 0;
        }
        public function login()
        {
            $_SESSION['loggedin'] = true;
            $_SESSION['userId'] = $this->id;
            var_dump($_SESSION['userId']);
        }
    }
?>