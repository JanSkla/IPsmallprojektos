<?php
require_once __DIR__ . "/../bootstrap/bootstrap.php";

class RegisterPage extends CRUDPage
{
    private int $state;
    public function __construct()
    {
        $this->title = "Registrace";
    }

    protected function prepare(): void
    {
        if($_SESSION['loggedin'])
        {
            header('Location: /');
        }
    }

    protected function pageBody()
    {
        return MustacheProvider::get()->render(
            'register'
        );;
    }

    private function findState() : void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
            $this->state = self::STATE_DATA_SENT;
        else
            $this->state = self::STATE_FORM_REQUESTED;
    }
}

$page = new RegisterPage();
$page->render();

?>