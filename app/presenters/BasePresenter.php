<?php

namespace App\Presenters;

use App\Core\Component\Login\LoginComponent;
use App\Core\Manager\Loader;
use Kdyby\Translation\Translator;
use Nette;
use Nextras\Application\UI\SecuredLinksPresenterTrait;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    use SecuredLinksPresenterTrait;

    /**
     * @var string
     * @persistent
     */
    public $locale;
    /** @var Translator */
    protected $tr;
    /** @var Loader  */
    protected $loader;
    /** @var Nette\Database\Context */
    protected $db;
    /** @var LoginComponent */
    private $loginComponent;

    public function __construct(Loader $loader, Translator $tr, Nette\Database\Context $db)
    {
        parent::__construct();
        $this->tr = $tr;
        $this->loader = $loader;
        $this->db = $db;
    }

    public function injectComponents(LoginComponent $loginComponent): void{
        $this->loginComponent = $loginComponent;
    }

    protected function createComponentLogin(): LoginComponent{
        return $this->loginComponent;
    }

    /**
     * @throws Nette\Application\AbortException
     * @secured
     */
    public function handleLogout(): void{
        $this->getUser()->logout(true);
        $this->flashMessage($this->tr->trans('front.logout'),'success');
        $this->redirect('Homepage:default');
    }

    /**
     * @param string $lang
     * @throws Nette\Application\AbortException
     */
    public function handleChangeLang(string $lang): void{
        switch ($lang){
            case 'cs':
                $this->redirect('this',['locale' => 'cs']);
                break;
            case  'en':
                $this->redirect('this',['locale' => 'en']);
                break;
            default:
                $this->redirect('this',['locale' => 'cs']);
                break;
        }
    }

}
