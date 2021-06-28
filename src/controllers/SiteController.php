<?php

namespace src\controllers;

use components\View;
use src\services\SiteService;
use Throwable;

/**
 * Class SiteController
 * @package src\controller
 */
class SiteController
{
    /**
     * @var View
     */
    private View $view;

    /**
     * SiteController constructor.
     */
    public function __construct()
    {
        $this->view = new View();
    }

    public function actionIndex()
    {
        try {
            $this->view->render('/views/site/index');
        } catch (Throwable $e) {
            $this->view->render('/views/site/error', [$e->getMessage()]);
        }
    }

    public function actionTable()
    {
        try {
            $siteService = new SiteService();
            $this->view->render('/views/site/table', $siteService->calculateData());
        } catch (Throwable $e) {
            $this->view->render('/views/site/error', [$e->getMessage()]);
        }
    }
}
