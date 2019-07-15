<?php
/**
 * This file is part of reliadmin project.
 * @author Stanislav Opletal <info@relisoft.cz>
 */

namespace App\Core\Component\Post;


use App\Core\Manager\Loader;
use App\Core\Model\Post;
use HTMLPurifier;
use Kdyby\Translation\Translator;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\DateTime;

class PostComponent extends Control
{
    private $tr;
    private $loader;
    /** @var callable */
    public $onAdd;

    public function __construct(Translator $tr, Loader $loader)
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

    protected function createComponentAdd(): Form
    {
        $form = new Form();
        $form->setTranslator($this->tr);
        $form->addProtection('form.protection');
        $form->addTextArea('content', 'form.content', 8, 8)
            ->setRequired();
        $form->addHidden('response_to');
        $form->addSubmit('submt', 'form.submt');
        $form->onSuccess[] = function (Form $form, $val) {
            if (!$this->presenter->getUser()->isLoggedIn()) {
                $this->presenter->redirect('Homepage:default');
            }

            //Source: https://www.kutac.cz/blog/weby-a-vse-okolo/html-purifier-ochrana-pred-xss/ - moc dobrý config
            $config = \HTMLPurifier_Config::create([
                // Povolí IDčka u tagů
                'Attr.EnableID' => true,
                'Attr.ID.HTML5' => true,
                // Povolí target="_blank" u odkazů, automaticky také dodá
                // rel="noreferrer noopener" proti útokům přes window.opener
                'Attr.AllowedFrameTargets' => array('_blank', '_self', '_target', '_top'),
                // URL adresy, které nejsou mezi tagy <a> automaticky odkazy udělá
                'AutoFormat.Linkify' => true,

                // Povolení YouTube a Vimeo iframe, jinak jsou mazány
                'HTML.SafeIframe' => 'true',
                'URI.SafeIframeRegexp' => "%^(http://|https://|//)(www.youtube.com/embed/|player.vimeo.com/video/)%",
            ]);

            $purifier = new HTMLPurifier($config);
            $clean_html5 = $purifier->purify($val->content);

            $model = new Post();
            $model->setUpdateDate(new DateTime());
            $model->setCreateDate(new DateTime());
            $model->setContent($clean_html5);
            $model->setResponseTo($val->response_to);
            $model->setAuthor($this->presenter->getUser()->getId());

            $this->loader->save($model);

            if($model->getId()){
                $this->presenter->flashMessage($this->tr->trans('front.post.success'),'success');
                if($val->response_to){
                    $this->onAdd();
                }else{
                    $this->presenter->redirect('default',1);
                }
            }else{
                $this->presenter->flashMessage($this->tr->trans('front.post.error'),'error');
                $this->presenter->redrawControl('flashes');
            }
        };
        return $form;
    }
}