<?php
require_once __DIR__ . "/../bootstrap/bootstrap.php";

class LoginPage extends CRUDPage
{
    private PasswordChange $passChange;
    private ?array $errors = [];
    private int $state;
    private bool $success = false;
    public function __construct()
    {
        $this->title = "Přihlášení";
    }

    protected function prepare(): void
    {
        $this->findState();

        if($this->state === self::STATE_DATA_SENT)
        {
            $this->passChange = PasswordChange::readPost();

            //zkontroluj je, jinak formulář
            $this->errors = [];
            $isOk = $this->passChange->validate($this->errors);
            if (!$isOk)
            {
                $this->state = self::STATE_FORM_REQUESTED;
            }
            else
            {
                $this->success = $this->passChange->process();
            }
        }
    }

    protected function pageBody()
    {
        return MustacheProvider::get()->render(
            'passwordChange',
            [
                'errors' => $this->errors,
                'success' => $this->success
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

$page = new LoginPage();
$page->render();

?>