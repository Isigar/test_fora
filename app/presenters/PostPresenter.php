<?php
/**
 * This file is part of reliadmin project.
 * @author Stanislav Opletal <info@relisoft.cz>
 */

namespace App\Presenters;


use App\Core\Component\Post\PostComponent;
use App\Core\Model\Post;
use Nette\Application\UI\Form;

class PostPresenter extends BasePresenter
{
    /** @var PostComponent @inject */
    public $postComponent;

    /**
     * @param int $page
     * @param int $limit
     */
    public function actionDefault(int $page, int $limit = 20): void
    {
        $pageCount = 0;
        $data = $this->db->table((new Post())->table())->where('response_to IS NULL')->page($page, $limit, $pageCount)->order('update_date DESC')->fetchAll();
        $posts = [];
        foreach ($data as $item) {
            $posts[] = (new Post())->associate($item);
        }

        $this->template->posts = $posts;
        $this->template->count = $pageCount;
        $this->template->page = $page;
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function actionDetail(int $id): void
    {
        $post = new Post();
        $this->loader->loadById($post, $id);
        $responses = [];
        $resp = $this->db->table($post->table())->where('response_to = ?', $id)->fetchAll();
        foreach ($resp as $item) {
            $responses[] = (new Post())->associate($item);
        }
        $this->template->responses = $responses;
        if ($post->getId()) {
            $this->template->post = $post;
        } else {
            $this->redirect('default');
        }
    }

    public function actionAdd(): void
    {
        if (!$this->getUser()->isLoggedIn()) {
            $this->flashMessage($this->tr->trans('front.need_login'));
            $this->redirect('default');
        }
    }

    /**
     * @return PostComponent
     */
    protected function createComponentAdd(): PostComponent
    {
        /** @var PostComponent $post */
        $post = $this->postComponent;
        if ($this->getAction() === 'detail') {
            $post->getComponent('add')->setDefaults([
                'response_to' => $this->presenter->getParameter('id')
            ]);
            $post->onAdd[] = function (){
                $post = new Post();
                $responses = [];
                $resp = $this->db->table($post->table())->where('response_to = ?', $this->getParameter('id'))->fetchAll();
                foreach ($resp as $item) {
                    $responses[] = (new Post())->associate($item);
                }
                $this->template->responses = $responses;
                $this->presenter->redrawControl('responses');
            };
        }
        return $post;
    }

}