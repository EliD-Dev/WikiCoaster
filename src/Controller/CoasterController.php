<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Coaster;
use App\Repository\CoasterRepository;
use App\Repository\ParkRepository;
use App\Repository\CategoryRepository;
use App\Form\CoasterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class CoasterController extends AbstractController{

    #[Route('/coaster', name: 'app_coaster_index')]
    public function index(Request $request, CoasterRepository $coasterRepository, ParkRepository $parkRepository, CategoryRepository $categoryRepository): Response
    {
        $parks = $parkRepository->findAll();
        $categories = $categoryRepository->findAll();

        // Récupérer les filtres de la requête GET
        $parkId = $request->get('park', '');
        $categoryId = $request->get('category', '');
        $search = $request->get('search', '');

        // Appliquer les filtres en appelant la méthode findFiltered
        $coasters = $coasterRepository->findFiltered($parkId, $categoryId, $search);

        // Passer les données à la vue
        return $this->render('coaster/index.html.twig', [
            'coasters' => $coasters,
            'parks' => $parks,
            'categories' => $categories,
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