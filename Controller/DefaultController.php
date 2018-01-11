<?php

declare(strict_types=1);

namespace Goat\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('@GoatAccount/default/index.html.twig');
    }
}
