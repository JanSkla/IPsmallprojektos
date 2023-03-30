<?php
require_once __DIR__ . "/../../bootstrap/bootstrap.php";

class StaffDetailPage extends BasePage
{
    private $employee;
    private $room;
    private $keys;

    protected function prepare(): void
    {
        BasePage::redirectIfNotLogged();
        
        parent::prepare();
        //získat data z GET
        $employeeId = filter_input(INPUT_GET, 'employeeId', FILTER_VALIDATE_INT);
        if (!$employeeId)
            throw new BadRequestException();

        //najít místnost v databázi
        $this->employee = Staff::findByID($employeeId);
        if (!$this->employee)
            throw new NotFoundException();


        $stmt = PDOProvider::get()->prepare('SELECT r.name, r.room_id FROM room r
        JOIN `key` k ON r.room_id = k.room AND k.employee = :employeeId');
        $stmt->execute(['employeeId' => $employeeId]);
        $this->keys = $stmt->fetchAll();

        $stmt = PDOProvider::get()->prepare('SELECT `name`, phone FROM room WHERE room_id = :roomId');
        $stmt->execute(['roomId' => $this->employee->room]);
        $this->room = $stmt->fetch();

        $this->title = "Detail zaměstnance {$this->employee->surname}";
        
    }

    protected function pageBody()
    {
        //prezentovat data
        return MustacheProvider::get()->render(
            'staffDetail',
            ['employee' => $this->employee, 'keys' => $this->keys, 'roomData' => $this->room]
        );
    }

}

$page = new StaffDetailPage();
$page->render();

?>
