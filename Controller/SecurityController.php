<?php

declare(strict_types=1);

namespace Goat\AccountBundle\Controller;

use Goat\AccountBundle\Security\Crypt;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends Controller
{
    use AccountMapperAwareController;

    /**
     * Login form.
     */
    public function loginAction()
    {
        $error = $this
            ->get('security.authentication_utils')
            ->getLastAuthenticationError()
        ;

        if ($error) {
            $this->addFlash('danger', $error->getMessage());

            return $this->render('@GoatAccount/security/login.html.twig', [], new Response('', 400));
        }

        return $this->render('@GoatAccount/security/login.html.twig');
    }

    /**
     * Change password action.
     */
    public function changePasswordAction(Request $request)
    {
        /** @var $account \Goat\AccountBundle\Model\Account */
        $account = $this->getUser()->getAccount();

        $this->denyAccessUnlessGranted('edit', $account);

        // Create the change password form.
        $form = $this
            ->createFormBuilder(null, [
                'method' => Request::METHOD_POST,
                'attr' => ['novalidate' => 'novalidate'],
            ])
            ->add('password_old', PasswordType::class, [
                'label'     => "Your current password",
                'required'  => true,
            ])
            ->add('password1', PasswordType::class, [
                'label'     => "Password",
                'required'  => true,
            ])
            ->add('password2', PasswordType::class, [
                'label'     => "Confirmation",
                'required'  => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Change password",
            ])
            ->getForm()
        ;

        if (Request::METHOD_POST === $request->getMethod()) {

            $form->handleRequest($request);

            $data = $form->getData();
            $matches = !empty($data['password1']) && $data['password1'] === $data['password2'];

            if ($form->isValid() && $matches) {

                $this->getAccountMapper()->updatePassword($account, $data['password1']);

                // Do never tell the user if the mail exist or not
                $this->addFlash('success', "Your password has been changed");

                return $this->redirectToRoute('goat_account.profile_view', [
                    'id' => $account->getId(),
                ]);

            } else {
                $this->addFlash('danger', "Please check that both passwords matches");
            }
        }

        return $this->render('@GoatAccount/security/change-password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Request new password form.
     */
    public function requestPasswordAction(Request $request)
    {
        $form = $this
            ->createFormBuilder(null, [
                'method' => Request::METHOD_POST,
                'attr' => ['novalidate' => 'novalidate'],
            ])
            ->add(EmailType::class, 'Symfony\Component\Form\Extension\Core\Type\EmailType', [
                'label' => "Email",
                'required' => true,
            ])
            ->add(SubmitType::class, 'Symfony\Component\Form\Extension\Core\Type\SubmitType', [
                'label' => "Request new password",
            ])
            ->getForm()
        ;

        if (Request::METHOD_POST === $request->getMethod()) {
            if ($form->handleRequest($request)->isValid()) {

                $model    = $this->getAccountMapper();
                $account  = $model->findUserByMail($form['email']->getData());

                if ($account) {

                    $token = Crypt::createRandomPlainToken();

                    $queryManager = $this
                        ->get('pomm')
                        ->getSession('default')
                        ->getClientUsingPooler('query_manager', '\PommProject\Foundation\PreparedQuery\PreparedQueryManager')
                    ;
                    $queryManager
                        ->query("DELETE FROM account_onetime WHERE id_account = $*", [
                            $account->id,
                        ])
                    ;
                    $queryManager
                        ->query("INSERT INTO account_onetime (id_account, login_token, ts_expire) VALUES ($*, $*, $*)", [
                            $account->id,
                            $token,
                            (new \DateTime("now +1 hour"))->format('Y-m-d H:i:s'),
                        ])
                    ;

                    $message = \Swift_Message::newInstance()
                        ->setSubject('Hello Email')
                        ->setFrom('media@processus.org')
                        ->setTo($account->mail)
                        ->setBody(
                            $this->renderView('@GoatAccount/email/request-password.html.twig', [
                                'account' => $account,
                                'token'   => $token,
                            ]),
                            'text/html'
                        )
                        ->addPart(
                            $this->renderView(
                                '@GoatAccount/emails/registration.txt.twig',
                                [] //array('name' => $name)
                            ),
                            'text/plain'
                        )
                    ;
                    $this->get('mailer')->send($message);
                }

                // Do never tell the user if the mail exist or not
                $this->addFlash('success', "A newly generated password has been sent to your e-mail address");

                return $this->redirectToRoute('goat_account.login');

            } else {
                $this->addFlash('danger', "Invalid email address");
            }
        }

        return $this->render('@GoatAccount/security/request-password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * One time login
     */
    public function oneTimeLoginAction(Request $request, $accountId, $token)
    {
        $queryManager = $this
            ->get('pomm')
            ->getSession('default')
            ->getClientUsingPooler('query_manager', '\PommProject\Foundation\PreparedQuery\PreparedQueryManager')
        ;

        $row = $queryManager
            ->query("SELECT id_account FROM account_onetime WHERE login_token = $* AND ts_expire > NOW()", [$token])
            ->current()
        ;

        if (!$row) {
            throw $this->createNotFoundException();
        }

        $loadedId = $row['id_account'];
        if ($loadedId != $accountId) {
            // @todo Invalidate the token ?
            throw $this->createNotFoundException();
        }

        $account = $this->getAccountModel()->findByPK(['id' => $accountId]);
        if (!$account) {
            throw $this->createNotFoundException();
        }

        // Create the change password form.
        $form = $this
            ->createFormBuilder(null, [
                'method' => Request::METHOD_POST,
                'attr' => ['novalidate' => 'novalidate'],
            ])
            ->add('password1', PasswordType::class, [
                'label'     => "Password",
                'required'  => true,
            ])
            ->add('password2', PasswordType::class, [
                'label'     => "Confirmation",
                'required'  => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Change password",
            ])
            ->getForm()
        ;

        if (Request::METHOD_POST === $request->getMethod()) {

            $form->handleRequest($request);

            $data = $form->getData();
            $matches = !empty($data['password1']) && $data['password1'] === $data['password2'];

            if ($form->isValid() && $matches) {

                $this->getAccountModel()->updatePassword($account, $data['password1']);

                $row = $queryManager
                    ->query("DELETE FROM account_onetime WHERE id_account = $*", [$accountId])
                    ->current()
                ;

                // Do never tell the user if the mail exist or not
                $this->addFlash('success', "Your password has been changed, you may now login");

                return $this->redirectToRoute('goat_account.login');

            } else {
                $this->addFlash('danger', "Please check that both passwords matches");
            }
        }

        return $this->render('@GoatAccount/security/one-time-login.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
