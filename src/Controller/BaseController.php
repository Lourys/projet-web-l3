<?php


namespace App\Controller;


use App\Entity\Produit;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends AbstractController
{
    /**
     * @var null|Utilisateur
     */
    private $user = null;

    public function getUser(): ?Utilisateur
    {
        if ($this->user === null)
            if ($this->getParameter('logged_user') !== null)
                $this->user = $this->getDoctrine()
                    ->getRepository(Utilisateur::class)
                    ->find($this->getParameter('logged_user'));

        return $this->user;
    }

    public function allowGuestOnly()
    {
        if ($this->getUser() !== null) {
            throw $this->createNotFoundException('Vous êtes déjà connecté.e');
        }
    }

    public function allowRegisteredOnly()
    {
        if ($this->getUser() === null) {
            throw $this->createNotFoundException('Vous n\'êtes pas connecté.e');
        } elseif ($this->getUser() !== null && $this->getUser()->getIsAdmin()) {
            throw $this->createNotFoundException('Vous êtes connecté.e à un compte administrateur');
        }
    }

    public function allowAdminOnly()
    {
        if ($this->getUser() === null) {
            throw $this->createNotFoundException('Vous n\'êtes pas connecté.e');
        } elseif ($this->getUser() !== null && !$this->getUser()->getIsAdmin()) {
            throw $this->createNotFoundException('Vous n\'êtes pas connecté.e à un compte administrateur');
        }
    }


    public function header(): Response
    {
        return $this->render('Layouts/entete.html.twig', ['user' => $this->getUser()]);
    }

    public function menu(): Response
    {
        $produitsNB = count($this->getDoctrine()
            ->getRepository(Produit::class)
            ->findAll());

        return $this->render('Layouts/menu.html.twig', [
            'user' => $this->getUser(),
            'nb_produits' => $produitsNB
        ]);
    }
}


/**
 * GETREAU Lucas
 * CHAKARA Ibrahim
 */