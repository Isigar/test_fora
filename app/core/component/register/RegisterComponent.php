<?php
/**
 * This file is part of reliadmin project.
 * @author Stanislav Opletal <info@relisoft.cz>
 */

namespace App\Core\Component\Register;


use App\Core\Manager\Loader;
use App\Core\Model\User\User;
use Kdyby\Translation\Translator;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Security\Identity;
use Nette\Security\Passwords;
use Nette\Utils\DateTime;

class RegisterComponent extends Control
{
    /** @var Translator */
    private $tr;
    /** @var Loader */
    private $loader;

    public function __construct(Translator $tr, Loader $loader)
    {
        parent::__construct();
        $this->tr = $tr;
        $this->loader = $loader;
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . "/templates/default.latte");
        $this->template->setTranslator($this->tr);
        $this->template->render();
    }

    protected function createComponentRegister(): Form
    {
        $form = new Form();
        $form->setTranslator($this->tr);
        $form->addText('name', 'form.register.name')
            ->setRequired();
        $form->addText('surname', 'form.register.surname')
            ->setRequired();
        $form->addEmail('email', 'form.register.email')
            ->setRequired();
        $form->addPassword('password', 'form.register.password')
            ->setRequired();
        $form->addPassword('password_again', 'form.register.password_again')
            ->addRule(Form::EQUAL, 'form.register.password_again_rule', $form['password'])
            ->setRequired();
        $form->addSubmit('submt', 'form.register.submt');
        $form->addProtection('form.protection');
        $form->onSuccess[] = function (Form $form, $val) {
            $model = new User();
            //TODO: Set guest send email with active code and then set to user
            $model->setRole($model::ROLE_USER);
            $model->setName($val->name);
            $model->setSurname($val->surname);
            $model->setEmail($val->email);
            $model->setPassword(Passwords::hash($val->password));
            $model->setUpdateDate(new DateTime());
            $model->setCreateDate(new DateTime());

            $this->loader->save($model);

            if($model->getId()){
                $identityVars = [
                    'name' => $model->getName(),
                    'surname' => $model->getSurname(),
                    'create_date' => $model->getCreateDate(),
                    'update_date' => $model->getUpdateDate()
                ];

                $identity = new Identity($model->getId(),$model->getRole(),$identityVars);
                $this->presenter->getUser()->login($identity);
                //Success register
                $this->presenter->flashMessage($this->tr->trans('front.register.success'),'success');
                $this->presenter->redirect('Homepage:default');
            }else{
                //Cannot create
                $this->presenter->flashMessage($this->tr->trans('front.register.cannot_create'),'error');
                $this->presenter->redrawControl('flashes');
                return;
            }
        };
        return $form;
    }
}