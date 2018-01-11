<?php

declare(strict_types=1);

namespace Goat\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AccountController extends Controller
{
    use AccountMapperAwareController;

    /**
     * View account
     */
    public function selfAction()
    {
        $account = $this->getUserAccountOrDie();

        $this->denyAccessUnlessGranted('view', $account);

        return $this->forward('GoatAccountBundle:Account:view', ['id' => $account->getId()]);
    }

    /**
     * View account
     */
    public function viewAction($id)
    {
        $account = $this->findAccountOr404($id);

        $this->denyAccessUnlessGranted('view', $account);

        return $this->render('@GoatAccount/account/view.html.twig', ['account' => $account]);
    }
}
