<?php

abstract class BasePage
{
    protected string $title = "";

    protected function prepare() : void
    {}

    protected function sendHttpHeaders() : void
    {}

    protected function extraHTMLHeaders() : string
    {
        return "";
    }

    protected function pageHeader() : string
    {
        $islogged = $_SESSION['loggedin'];

        $m = MustacheProvider::get();
        return $m->render('header',[ 'islogged' => $islogged ]);
    }

    abstract protected function pageBody();

    protected function pageFooter() : string
    {
        $m = MustacheProvider::get();
        return $m->render('footer',[]);
    }

    public function render() : void
    {
        session_start();
        try
        {
            $this->prepare();
            $this->sendHttpHeaders();

            $m = MustacheProvider::get();
            $data = [
                'lang' => AppConfig::get('app.lang'),
                'title' => $this->title,
                'pageHeader' => $this->pageHeader(),
                'pageBody' => $this->pageBody(),
                'pageFooter' => $this->pageFooter()
            ];
            echo $m->render("page", $data);
        }

        catch (BaseException $e)
        {
            $exceptionPage = new ExceptionPage($e);
            $exceptionPage->render();
            exit;
        }

        catch (Exception $e)
        {
            if (AppConfig::get('debug'))
                throw $e;

            $e = new BaseException("Server error", 500);
            $exceptionPage = new ExceptionPage($e);
            $exceptionPage->render();
            exit;
        }
    }
    public static function redirectIfNotLogged()
    {
        //zkontroluje jestli je přihlášen
        if(!$_SESSION['loggedin'])
        {
            header('Location: /login.php');
        }
    }
    public static function redirectIfNotAdmin()
    {
        //zkontroluje jestli je přihlášen
        if(!$_SESSION['isAdmin'])
        {
            header('Location: /');
        }
    }
}