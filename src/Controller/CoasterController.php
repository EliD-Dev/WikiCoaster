<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Coaster;
use App\Repository\CoasterRepository;
use App\Form\CoasterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class CoasterController extends AbstractController{

    #[Route('/coaster', name: 'app_coaster_index')]
    public function index(CoasterRepository $coasterRepository): Response
    {
        // Récupération de tous les coasters depuis la base
        $coasters = $coasterRepository->findAll();

        dump($coasters);

        // Affichage de la liste des coasters dans la vue
        return $this->render('coaster/index.html.twig', [
            'coasters' => $coasters,
        ]);
    }
    
    #[Route('/coaster/add', name: 'app_coaster_add')]
    public function add(Request $request, EntityManagerInterface $em): Response {
        $coaster = new Coaster();
        $form = $this->createForm(CoasterType::class, $coaster);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($coaster);
            $em->flush();

            // Redirection après soumission réussie
            return $this->redirectToRoute('app_coaster_index');
        }

        dump($coaster);

        return $this->render('coaster/add.html.twig', ['coasterForm' => $form->createView(),]);
    }

    #[Route('/coaster/{id}/edit', name: 'app_coaster_edit')]
    public function edit(Coaster $coaster, Request $request, EntityManagerInterface $em): Response {
        $form = $this->createForm(CoasterType::class, $coaster);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('app_coaster_index');
        }

        dump($coaster);

        return $this->render('coaster/edit.html.twig', ['coasterForm' => $form->createView(),]);
    }

    #[Route('/coaster/{id}/delete', name: 'app_coaster_delete')]
    public function delete(Coaster $coaster, Request $request, EntityManagerInterface $em): Response {
        // Vérifier la validité du token CSRF
        if ($this->isCsrfTokenValid('delete' . $coaster->getId(), $request->request->get('_token'))) {
            $em->remove($coaster);
            $em->flush();

            $this->addFlash('success', 'Coaster supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Action non autorisée.');
        }

        dump($coaster);

        return $this->render('coaster/delete.html.twig', ['coaster' => $coaster,]);
    }
}
?>