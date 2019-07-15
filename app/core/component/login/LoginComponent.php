<?php
/**
 * This file is part of reliadmin project.
 * @author Stanislav Opletal <info@relisoft.cz>
 */

namespace App\Core\Component\Login;


use App\Core\Manager\Loader;
use App\Core\Model\User\User;
use Kdyby\Translation\Translator;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Security\Identity;
use Nette\Security\Passwords;
use Tracy\Debugger;

class LoginComponent extends Control
{
    /** @var Translator */
    private $tr;
    /** @var Loader  */
    private $loader;
    private $auth;

    public function __construct(Translator $tr, Loader $loader)
    {
        parent::__construct();
        $this->tr = $tr;
        $this->loader = $loader;
    }

    public function render(): void{
        $this->template->setFile(__DIR__."/templates/modal.latte");
        $this->template->setTranslator($this->tr);
        $this->template->render();
    }

    public function renderBtn(): void {
        $this->template->setFile(__DIR__."/templates/btn.latte");
        $this->template->setTranslator($this->tr);
        $this->template->render();
    }

    protected function createComponentLogin(): Form{
        $form = new Form();
        $form->setTranslator($this->tr);
        $form->addProtection('form.protection');
        $form->addEmail('email','form.login.email')
            ->setRequired();
        $form->addPassword('pass','form.login.password')
            ->setRequired();
        $form->addSubmit('submt','form.login.login');
        $form->addSubmit('register','form.login.register');
        $form->onSuccess[] = function (Form $form,$val){
            /** @var SubmitButton $submittedBy */
            $submittedBy = $form->isSubmitted();
            if($submittedBy->getName() === 'register'){
                $this->presenter->redirect('Register:default');
            }

            $user = new User();
            $this->loader->loadByOne($user,[
                'email' => $val->email
            ]);

            if($user->getId()){
                if(Passwords::verify($val->pass,$user->getPassword())){
                    $identityVars = [
                        'name' => $user->getName(),
                        'surname' => $user->getSurname(),
                        'create_date' => $user->getCreateDate(),
                        'update_date' => $user->getUpdateDate()
                    ];

                    $identity = new Identity($user->getId(),$user->getRole(),$identityVars);
                    $this->presenter->getUser()->login($identity);
                }else{
                    $this->presenter->flashMessage($this->tr->trans('front.login.pass'),'error');
                    $this->presenter->redrawControl('flashes');
                }
            }else{
                $this->presenter->flashMessage($this->tr->trans('front.login.not_found'),'error');
                $this->presenter->redrawControl('flashes');
                return;
            }

            $this->presenter->flashMessage($this->tr->trans('front.login.success'),'success');
            $this->presenter->redirect('Homepage:default');
        };
        return $form;
    }
}