<?php
require_once __DIR__."/../../bootstrap/bootstrap.php";

class StaffDeletePage extends CRUDPage
{
    protected function prepare(): void
    {
        BasePage::redirectIfNotLogged();
        BasePage::redirectIfNotAdmin();
        
        parent::prepare();

        $employeeId = filter_input(INPUT_POST, 'employeeId', FILTER_VALIDATE_INT);
        if (!$employeeId)
            throw new BadRequestException();
        
        if($_SERVER['userId'] == $employeeId)
            header('Location: /staff/list');
        
        //když poslal data
        $success = Staff::deleteByID($employeeId);

        //přesměruj
        $this->redirect(self::ACTION_DELETE, $success);
    }

    protected function pageBody()
    {
        return "";
    }
}

$page = new StaffDeletePage();
$page->render();

?>