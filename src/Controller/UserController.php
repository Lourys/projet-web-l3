<?php


namespace App\Controller;

use App\Entity\Utilisateur;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/utilisateur")
 */
class UserController extends BaseController
{
    /**
     * @Route("/liste", name="clients")
     * @param Request $request
     *
     * @return Response
     */
    public function listAction(Request $request): Response
    {
        $this->allowAdminOnly();

        $users = $this->getDoctrine()
            ->getRepository(Utilisateur::class)
            ->findAll();

        if ($request->getMethod() === 'POST') {
            /**
             * @var Utilisateur $user
             */
            $user = $this->getDoctrine()
                ->getRepository(Utilisateur::class)
                ->find($request->request->get('userId'));

            // On vérifie que l'utilisateur n'est pas celui connecté
            if ($user->getId() === $this->getUser()->getId()) {
                $this->addFlash(
                    'error', 'Vous ne pouvez pas supprimer votre propre compte'
                );

                return $this->redirectToRoute('clients');
            }

            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();

            $this->addFlash(
                'success', 'L\'utilisateur a bien été supprimé'
            );

            return $this->redirectToRoute('clients');
        }

        return $this->render('User/liste.html.twig', [
            'users' => $users,
            'currentUser' => $this->getUser()
        ]);
    }
    /**
     * @Route("/compte", name="compte")
     * @param Request $request
     *
     * @return Response
     */
    public function accountAction(Request $request): Response
    {
        $user = $this->getUser();
        if ($user === null) {
            throw $this->createNotFoundException('Vous n\'êtes pas connecté.e');
        }

        $form = $this->createFormBuilder($user)
            ->add('identifiant')
            ->add('mot_de_passe')
            ->add('nom')
            ->add('prenom')
            ->add('anniversaire')
            ->add('save', SubmitType::class, ['label' => 'Modifier mes informations'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'success', 'Votre compte a bien été modifié'
            );

            return $this->redirectToRoute('accueil');
        } else {
            // On vide le champ mot de passe si on affiche le formulaire
            $form->get('mot_de_passe')->setData('');
        }

        return $this->render('User/compte.html.twig', [
            'registerForm' => $form->createView()
        ]);
    }
}


/**
 * GETREAU Lucas
 * CHAKARA Ibrahim
 */