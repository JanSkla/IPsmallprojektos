<?php
    class PasswordChange
    {
        public ?string $password;
        public ?string $passwordConfirm;
        public ?string $passwordCurrent;
        public function process() : bool
        {
            $id = $_SESSION['userId'];
            $hash = User::password_hash($this->password);

            $query = 'UPDATE employee SET `password` = :password WHERE employee_id = '.$id;
            $stmt = PDOProvider::get()->prepare($query);
            return $stmt->execute(['password' => $hash]);
        }
        public function validate(&$errors) : bool
        {
            if (!isset($this->passwordCurrent) || (!$this->passwordCurrent))
                $errors['passwordCurrent'] = 'Login nesmí být prázdné';

            $id = $_SESSION['userId'];
            
            $query = 'SELECT `password` FROM employee WHERE employee_id = '.$id;
            $stmt = PDOProvider::get()->prepare($query);
            $stmt->execute([]);
            $result = $stmt->fetch();

            if(!password_verify($this->passwordCurrent, $result->password))
                $errors['passwordCurrent'] = 'Heslo není správně';
        
            if (!isset($this->password) || (!$this->password))
                $errors['password'] = 'Heslo nesmí být prázdné';
            
            if ($this->password != $this->passwordConfirm)
                $errors['passwordConfirm'] = 'Hesla se neshodují';
            
            if ($this->passwordCurrent == $this->password)
            $errors['passwordConfirm'] = 'Heslo nemůže být stejné';

            return count($errors) === 0;
        }
        public static function readPost() : self
        {
            $data = new PasswordChange();
            $data->password = filter_input(INPUT_POST, 'password');
            $data->passwordConfirm = filter_input(INPUT_POST, 'passwordConfirm');
            $data->passwordCurrent = filter_input(INPUT_POST, 'passwordCurrent');

            return $data;
        }
    }
?>