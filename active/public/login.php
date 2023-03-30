<?php
require_once __DIR__ . "/../bootstrap/bootstrap.php";

class LoginPage extends CRUDPage
{
    private User $user;
    private ?array $errors = [];
    private int $state;
    public function __construct()
    {
        $this->title = "Přihlášení";
    }

    protected function prepare(): void
    {
        if($_SESSION['loggedin'])
        {
            header('Location: /');
        }

        $this->findState();
        if ($this->state === self::STATE_FORM_REQUESTED)
        {
            $this->user = new User();
        }
        elseif($this->state === self::STATE_DATA_SENT)
        {
            $this->user = User::readPost();

            //zkontroluj je, jinak formulář
            $this->errors = [];
            $isOk = $this->user->validate($this->errors);
            if (!$isOk)
            {
                $this->state = self::STATE_FORM_REQUESTED;
            }
            else
            {
               $this->user->login();
               
               header('Location: /');
            }
        }
    }

    protected function pageBody()
    {
        return MustacheProvider::get()->render(
            'login',
            ['errors' => $this->errors]
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

$page = new LoginPage();
$page->render();

?>