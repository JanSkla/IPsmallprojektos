<?php
require_once __DIR__ . "/../../bootstrap/bootstrap.php";

class StaffUpdatePage extends CRUDPage
{
    private ?Staff $staff;
    private ?array $errors = [];
    private ?array $rooms = [];
    private ?Room $room = null;
    private int $state;

    protected function prepare(): void
    {
        BasePage::redirectIfNotLogged();
        BasePage::redirectIfNotAdmin();
        
        parent::prepare();
        $this->findState();
        $this->title = "Upravit místnost";

        $staffId = filter_input(INPUT_GET, 'employeeId', FILTER_VALIDATE_INT);
        if (!$staffId)
            throw new BadRequestException();

        //jdi dál
        $this->staff = Staff::findByID($staffId);
        if (!$this->staff)
            throw new NotFoundException();
        
        $this->rooms = Staff::getKeysById($staffId);
        
        $this->room = Room::findByID($this->staff->room);
        if (!$this->room)
            throw new NotFoundException();

        //když poslal data
        if($this->state === self::STATE_DATA_SENT) {
            //načti je
            $this->staff = Staff::readPost();

            //zkontroluj je, jinak formulář
            $this->errors = [];
            $isOk = $this->staff->validate($this->errors);
            if (!$isOk)
            {
                $this->state = self::STATE_FORM_REQUESTED;
            }
            else
            {
                //ulož je
               $success = $this->staff->update();

                //přesměruj
               $this->redirect(self::ACTION_UPDATE, $success);
            }
        }
    }

    protected function pageBody()
    {
        return MustacheProvider::get()->render(
            'staffForm',
            [
                'staff' => $this->staff,
                'errors' => $this->errors,
                'rooms' => $this->rooms,
                'room' => $this->room
            ]
        );
    }

    private function findState() : void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
            $this->state = self::STATE_DATA_SENT;
        else
            $this->state = self::STATE_FORM_REQUESTED;
    }

}

$page = new StaffUpdatePage();
$page->render();

?>
