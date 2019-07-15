<?php
/**
 * This file is part of reliadmin project.
 * @author Stanislav Opletal <info@relisoft.cz>
 */

namespace App\Presenters;


use App\Core\Component\Register\RegisterComponent;

class RegisterPresenter extends BasePresenter
{
    /**
     * @var RegisterComponent
     * @inject
     */
    public $registerComponent;

    public function actionDefault(): void {
        if($this->getUser()->isLoggedIn()){
            $this->redirect('Homepage:default');
        }
    }

    protected function createComponentRegister(): RegisterComponent {
        return $this->registerComponent;
    }
}