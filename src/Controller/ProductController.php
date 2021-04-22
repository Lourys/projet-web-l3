<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Produit;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/produits")
 */
class ProductController extends BaseController
{

    /**
     * @Route("/ajouter", name="ajouter_produit")
     * @param Request $request
     *
     * @return Response
     */
    public function addAction(Request $request): Response
    {
        $this->allowAdminOnly();

        $product = new Produit();
        $form = $this->createFormBuilder($product)
            ->add('libelle')
            ->add('prix')
            ->add('quantite_stock')
            ->add('save', SubmitType::class, ['label' => 'Ajouter le produit'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash(
                'success', 'Le produit a bien été ajouté'
            );

            return $this->redirectToRoute('accueil');
        }

        return $this->render('Product/ajouter.html.twig', [
            'addProductForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/", name="produits")
     * @param Request $request
     *
     * @return Response
     */
    public function listAction(Request $request): Response
    {
        $this->allowRegisteredOnly();

        $produitRepository = $this->getDoctrine()
            ->getRepository(Produit::class);

        if ($request->getMethod() === 'POST') {
            $paniers = $this->getDoctrine()
                ->getRepository(Panier::class)
                ->findBy(['utilisateur' => $this->getUser()]);


            $em = $this->getDoctrine()->getManager();
            $baskedEdited = false;
            foreach ($request->request->get('quantite') as $productID => $amount) {
                // On ignore si amount <= 0
                if ($amount > 0) {
                    /**
                     * @var Produit $product
                     */
                    $product = $produitRepository->find($productID);

                    // On créé un nouveau panier avec les bonnes valeurs
                    $newPanier = new Panier();
                    $newPanier->setProduit($product);
                    $newPanier->setUtilisateur($this->getUser());
                    $newPanier->setQuantite($amount);

                    // Si un enregistrement existe déjà en BDD, on écrase $newPanier par le contenu en BDD et on met à jour
                    // la quantite
                    foreach ($paniers as $panier) {
                        if ($panier->getProduit()->getId() === $productID) {
                            $newPanier = $panier;
                            $newPanier->setQuantite($newPanier->getQuantite() + $amount);
                            break;
                        }
                    }
                    $em->persist($newPanier);

                    // On met à jour la quantité disponible en modifiant le produit
                    $product->setQuantiteStock($product->getQuantiteStock() - $amount);
                    $em->persist($product);

                    $baskedEdited = true;
                }
            }

            if ($baskedEdited) {
                $this->addFlash(
                    'success', 'Le(s) produit(s) a/ont bien été ajouté(s) au panier'
                );
                $em->flush();
            } else {
                $this->addFlash(
                    'success', 'Aucun produit n\'a été ajouté au panier'
                );
            }

        }

        $produits = $produitRepository->findAll();

        return $this->render('Product/liste.html.twig', [
            'produits' => $produits
        ]);
    }
}