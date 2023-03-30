<?php
require_once __DIR__ . "/../bootstrap/bootstrap.php";

class IndexPage extends BasePage
{
    public function __construct()
    {
        $this->title = "Prohlížeč databáze firmy";
    }

    protected function pageBody()
    {
        self::redirectIfNotLogged();
        return MustacheProvider::get()->render(
            'welcomePage'
        );
    }

}

$page = new IndexPage();
$page->render();

?>