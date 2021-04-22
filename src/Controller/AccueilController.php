<?php

namespace App\Controller;

use App\Service\MonService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends BaseController
{
    /**
     * @Route("/", name="accueil")
     * @param MonService $monService
     *
     * @return Response
     */
    public function indexAction(MonService $monService): Response
    {
        return $this->render('Accueil/index.html.twig', [
            'user' => $this->getUser(),
            'reverseWelcome' => $monService->reverseString('Je sais également inverser des chaines de caractère :)')
        ]);
    }
}


/**
 * GETREAU Lucas
 * CHAKARA Ibrahim
 */
