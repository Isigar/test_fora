<?php
/**
 * This file is part of reliadmin project.
 * @author Stanislav Opletal <info@relisoft.cz>
 */

namespace App\Core\Component\Profile;


use App\Core\Manager\Loader;
use App\Core\Model\User\User;
use Kdyby\Translation\Translator;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Security\Identity;
use Nette\Security\Passwords;

class ProfileComponent extends Control
{
    private $tr;
    private $loader;

    public function __construct(Loader $loader, Translator $tr)
    {
        parent::__construct();
        $this->tr = $tr;
        $this->loader = $loader;
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . "/template/default.latte");
        $this->template->setTranslator($this->tr);
        $this->template->render();
    }

    public function renderPasswords(): void
    {
        $this->template->setFile(__DIR__ . "/template/pass.latte");
        $this->template->setTranslator($this->tr);
        $this->template->render();
    }

    protected function createComponentProfile(): Form
    {
        $form = new Form();
        $form->setTranslator($this->tr);
        $form->addProtection('form.protection');
        $form->addText('name', 'form.register.name')
            ->setDefaultValue($this->presenter->getUser()->getIdentity()->name)
            ->setRequired();
        $form->addText('surname', 'form.register.surname')
            ->setDefaultValue($this->presenter->getUser()->getIdentity()->surname)
            ->setRequired();
        $form->addSubmit('submt', 'form.profile.submt');
        $form->onSuccess[] = function (Form $form, $val) {
            if (!$this->presenter->getUser()->isLoggedIn()) {
                $this->presenter->redirect('Homepage:default');
            }

            $user = new User();
            $this->loader->loadById($user, $this->presenter->getUser()->getId());

            if ($user->getId()) {

                $user->setName($val->name);
                $user->setSurname($val->surname);

                $this->loader->save($user);

                //Update user identity
                $this->presenter->getUser()->logout();
                $identityVars = [
                    'name' => $user->getName(),
                    'surname' => $user->getSurname(),
                    'create_date' => $user->getCreateDate(),
                    'update_date' => $user->getUpdateDate()
                ];

                $identity = new Identity($user->getId(),$user->getRole(),$identityVars);
                $this->presenter->getUser()->login($identity);

                $this->presenter->flashMessage($this->tr->trans('form.profile.update_success'), 'success');
                $this->redirect('this');
                return;
            } else {
                $this->redirect('Homepage:default');
            }
        };
        return $form;
    }

    protected function createComponentPassword(): Form
    {
        $form = new Form();
        $form->setTranslator($this->tr);
        $form->addProtection('form.protection');
        $form->addPassword('old_password', 'form.profile.old_pass')
            ->setRequired();
        $form->addPassword('password', 'form.profile.password')
            ->setRequired();
        $form->addPassword('password_again', 'form.profile.password_again')
            ->addRule(Form::EQUAL, 'form.profile.password_again_rule', $form['password'])
            ->setRequired();
        $form->addSubmit('submt', 'form.profile.submt');
        $form->onSuccess[] = function (Form $form, $val) {
            if (!$this->presenter->getUser()->isLoggedIn()) {
                $this->presenter->redirect('Homepage:default');
            }

            $user = new User();
            $this->loader->loadById($user, $this->presenter->getUser()->getId());

            if ($user->getId()) {
                if (Passwords::verify($val->old_password, $user->getPassword())) {
                    $user->setPassword(Passwords::hash($val->password));
                    $this->loader->save($user);

                    $this->presenter->flashMessage($this->tr->trans('form.profile.pass_success'), 'success');
                    $this->redirect('this');
                    return;
                }

                $this->presenter->flashMessage($this->tr->trans('form.profile.wrong_password'), 'error');
                $this->presenter->redrawControl('flashes');
                return;
            } else {
                $this->redirect('Homepage:default');
            }
        };
        return $form;
    }
}