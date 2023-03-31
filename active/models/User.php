<?php
    class User
    {
        public ?string $login;
        public ?string $password;
        public ?int $id;

        public ?bool $isAdmin;
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

            $query = 'SELECT employee_id, `password`, `admin` FROM employee WHERE login = "'.$this->login.'"';
            $stmt = PDOProvider::get()->prepare($query);
            $stmt->execute([]);
            $result = $stmt->fetch();

            $this->id = $result->employee_id;
            $this->isAdmin = !!$result->admin;

            if(!password_verify($this->password, $result->password)){
                $errors['isvalid'] = 'Uživatel s těmito údaji neexistuje';
            }

            return count($errors) === 0;
        }
        public function login()
        {
            $_SESSION['loggedin'] = true;
            $_SESSION['userId'] = $this->id;
            $_SESSION['isAdmin'] = $this->isAdmin;
        }
        public static function password_hash(string $password) : string
        {
            return password_hash($password, PASSWORD_DEFAULT);
        }
    }
?>