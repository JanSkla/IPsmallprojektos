<?php

//namespace models;

class Staff
{
    public const DB_TABLE = "employee";

    public ?int $employee_id;
    public ?string $name;
    public ?string $surname;
    public ?int $room;
    public ?string $job;
    public ?int $wage;
    public ?string $login;
    public ?string $password;
    public ?bool $admin;

    public ?string $roomname;
    public ?string $phone;

    /**
     * @param int|null $employee_id
     * @param string|null $name
     * @param string|null $surname
     * @param int|null $room
     * @param string|null $job
     * @param int|null $wage
     * @param bool|null $admin
     * 
     * @param string|null $roomname
     * @param string|null $phone
     */

    public function __construct(?int $employee_id = null, ?string $name = null, ?string $surname = null, ?int $room = null, ?string $job = null, ?int $wage = null, ?string $admin = null, ?string $roomname = null, ?string $phone = null)
    {
        $this->employee_id = $employee_id;
        $this->name = $name;
        $this->surname = $surname;
        $this->room = $room;
        $this->job = $job;
        $this->wage = $wage;
        $this->$admin = !!$admin;

        $this->roomname = $roomname;
        $this->phone = $phone;
    }

    public static function findByID(int $id) : ?self
    {
        $pdo = PDOProvider::get();
        $stmt = $pdo->prepare('SELECT * FROM '.self::DB_TABLE.' WHERE employee_id = :employeeId');
        $stmt->execute(['employeeId' => $id]);

        if ($stmt->rowCount() < 1)
            return null;

        $employee = new self();
        $employee->hydrate($stmt->fetch());
        return $employee;
    }

    /**
     * @return Room[]
     */
    public static function getAll($sorting = []) : array
    {
        $sortSQL = "";
        if (count($sorting))
        {
            $SQLchunks = [];
            foreach ($sorting as $field => $direction)
                $SQLchunks[] = "`{$field}` {$direction}";

            $sortSQL = " ORDER BY " . implode(', ', $SQLchunks);
        }

        $pdo = PDOProvider::get();
        $stmt = $pdo->prepare('SELECT e.*, r.name AS roomname, r.phone FROM '.self::DB_TABLE.' e INNER JOIN room r on e.room = r.room_id');
        $stmt->execute([]);

        $employees = [];
        while ($employeeData = $stmt->fetch())
        {
            $employee = new Staff();
            $employee->hydrate($employeeData);
            $employees[] = $employee;
        }

        return $employees;
    }

    private function hydrate(array|object $data)
    {
        $fields = ['employee_id', 'name', 'surname', 'room', 'job', 'wage', 'login', 'password', 'admin', 'roomname', 'phone'];
        if (is_array($data))
        {
            foreach ($fields as $field)
            {
                if (array_key_exists($field, $data))
                    $this->{$field} = $data[$field];
            }
        }
        else
        {
            foreach ($fields as $field)
            {
                if (property_exists($data, $field))
                    $this->{$field} = $data->{$field};
            }
        }
    }

    public function insert() : bool
    {
        //bool to 1/0
        $admin = $this->admin ? 1 : 0;

        $query = "INSERT INTO ".self::DB_TABLE." (`name`, `surname`, `job`, `wage`, `room`, `login`, `password`, `admin`) VALUES (:name, :surname, :job, :wage, :room, :login, :password, :admin)";
        $stmt = PDOProvider::get()->prepare($query);
        $result = $stmt->execute(['name' => $this->name, 'surname' => $this->surname, 'job' => $this->job, 'wage' => $this->wage, 'room' => $this->room, 'login' => $this->login, 'password' => $this->password, 'admin' => $admin]);
        if (!$result)
            return false;

        $this->employee_id = PDOProvider::get()->lastInsertId();
        return true;
    }

    public function update() : bool
    {
        if (!isset($this->employee_id) || !$this->employee_id)
            throw new Exception("Cannot update model without ID");

        //bool to 1/0
        $admin = $this->admin ? 1 : 0;

        $query = "UPDATE ".self::DB_TABLE." SET `name` = :name, `surname` = :surname, `job` = :job, `wage` = :wage, `room` = :room, `login` = :login, `password` = :password, `admin` = :admin WHERE `employee_id` = :employeeId";
        $stmt = PDOProvider::get()->prepare($query);
        return $stmt->execute(['name' => $this->name, 'surname' => $this->surname, 'job' => $this->job, 'wage' => $this->wage, 'room' => $this->room, 'login' => $this->login, 'password' => $this->password, 'admin' => $admin, 'employeeId' => $this->employee_id]);
    }

    public function delete() : bool
    {
        return self::deleteByID($this->employee_id);
    }

    public static function deleteByID(int $employeeId) : bool
    {
        $query = "DELETE FROM `".self::DB_TABLE."` WHERE `employee_id` = :employeeId";
        $stmt = PDOProvider::get()->prepare($query);
        return $stmt->execute(['employeeId'=>$employeeId]);
    }

    public function validate(&$errors = []) : bool
    {
        if (!isset($this->name) || (!$this->name))
            $errors['name'] = 'Jméno nesmí být prázdné';
        
        if (!isset($this->surname) || (!$this->surname))
        $errors['surname'] = 'Příjmení nesmí být prázdné';

        return count($errors) === 0;
    }

    public static function readPost() : self
    {
        $employee = new Staff();
        $employee->employee_id = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);

        $employee->name = filter_input(INPUT_POST, 'name');
        if ($employee->name)
            $employee->name = trim($employee->name);

        $employee->surname = filter_input(INPUT_POST, 'surname');
        if ($employee->surname)
            $employee->surname = trim($employee->surname);

        $employee->job = filter_input(INPUT_POST, 'job');
        if ($employee->job)
            $employee->job = trim($employee->job);

        $employee->wage = filter_input(INPUT_POST, 'wage');

        $employee->room = filter_input(INPUT_POST, 'room');

        $employee->login = filter_input(INPUT_POST, 'login');
        if ($employee->login)
            $employee->login = trim($employee->login);

        $employee->password = filter_input(INPUT_POST, 'password');
        if ($employee->password)
            $employee->password = trim($employee->password);

        $employee->admin = filter_input(INPUT_POST, 'admin', FILTER_VALIDATE_BOOLEAN);

        return $employee;
    }
}

