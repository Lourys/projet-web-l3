<?php


namespace App\Controller;


use App\Entity\Utilisateur;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/auth")
 */
class AuthController extends BaseController
{
    /**
     * @Route("/connexion", name="connexion")
     */
    public function loginAction(): Response
    {
        $this->allowGuestOnly();

        return $this->render('Auth/connexion.html.twig');
    }

    /**
     * @Route("/inscription", name="inscription")
     * @param Request $request
     *
     * @return Response
     */
    public function registerAction(Request $request): Response
    {
        $this->allowGuestOnly();

        $user = new Utilisateur();

        $form = $this->createFormBuilder($user)
            ->add('identifiant')
            ->add('mot_de_passe')
            ->add('nom')
            ->add('prenom')
            ->add('anniversaire')
            ->add('save', SubmitType::class, ['label' => 'Créer mon compte'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setIsAdmin(false);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'success', 'Votre compte a bien été créé'
            );

            return $this->redirectToRoute('accueil');
        }

        return $this->render('Auth/inscription.html.twig', [
            'registerForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/deconnexion", name="deconnexion")
     */
    public function logoutAction()
    {
        if ($this->getUser() === null) {
            throw $this->createNotFoundException('Vous n\'êtes pas connecté.e');
        }

        $this->addFlash(
            'success', 'Tu es connecté.e en dur, il va falloir modifier le code source :('
        );

        return $this->redirectToRoute('accueil');
    }
}