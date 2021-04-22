<?php


namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Produit;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/panier")
 */
class PanierController extends BaseController
{
    /**
     * @Route("/", name="panier")
     */
    public function basketAction(Request $request): Response
    {
        $this->allowRegisteredOnly();

        // On récupère tous les produits (nécessaire pour compléter $paniers->getProduit())
        $this->getDoctrine()
            ->getRepository(Produit::class)
            ->findAll();

        if ($request->getMethod() === 'POST') {
            if ($request->request->get('commander') !== null) {
                $this->deleteAllBasket(false);

                $this->addFlash(
                    'success', 'Votre commande a bien été passé'
                );
            } elseif ($request->request->get('vider')) {
                $this->deleteAllBasket();

                $this->addFlash(
                    'success', 'Le panier a bien été vidé'
                );
            } elseif ($request->request->get('panierId')) {
                $this->deleteBasket($request->request->get('panierId'));

                $this->addFlash(
                    'success', 'Le produit a bien été retiré du panier'
                );
            }
        }

        $paniers = $this->getDoctrine()
            ->getRepository(Panier::class)
            ->findBy(['utilisateur' => $this->getUser()]);




        return $this->render('Panier/panier.html.twig', [
            'paniers' => $paniers
        ]);
    }

    private function deleteAllBasket(bool $updateProductStock = true)
    {
        $paniers = $this->getDoctrine()
            ->getRepository(Panier::class)
            ->findBy(['utilisateur' => $this->getUser()]);

        foreach ($paniers as $panier) {
            $this->deleteBasket($panier, $updateProductStock);
        }
    }

    private function deleteBasket($basket, bool $updateProductStock = true) {
        $em = $this->getDoctrine()->getManager();
        /**
         * @var Panier $panier
         */
        if ($basket instanceof Panier) {
            $panier = $basket;
        } else {
            $panier =  $this->getDoctrine()
                ->getRepository(Panier::class)
                ->find($basket);
        }
        $em->remove($panier);

        if ($updateProductStock) {
            /**
             * @var Produit $produit
             */
            $produit = $panier->getProduit();
            $produit->setQuantiteStock($produit->getQuantiteStock() + $panier->getQuantite());
            $em->persist($produit);
        }

        $em->flush();
    }
}