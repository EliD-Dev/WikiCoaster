<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\Coaster;
use App\Repository\CoasterRepository;
use App\Repository\ParkRepository;
use App\Repository\CategoryRepository;
use App\Form\CoasterType;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\TextUI\XmlConfiguration\File;
use App\Service\LocalFileUploader;
use Symfony\Component\HttpFoundation\Request;

class CoasterController extends AbstractController{

    private LocalFileUploader $fileUploader;

    public function __construct(LocalFileUploader $fileUploader)
    {
        $this->fileUploader = $fileUploader;
    }

    #[Route('/coaster', name: 'app_coaster_index')]
    public function index(Request $request, CoasterRepository $coasterRepository, ParkRepository $parkRepository, CategoryRepository $categoryRepository): Response
    {
        $parks = $parkRepository->findAll();
        $categories = $categoryRepository->findAll();

        // Récupérer les filtres de la requête GET
        $parkId = $request->get('park', '');
        $categoryId = $request->get('category', '');
        $search = $request->get('search', '');
        $published = $request->get('published', '');

        $page = max((int)$request->get('p', 1), 1);
        $itemCount = 3;

        // Appliquer les filtres en appelant la méthode findFiltered
        $coasters = $coasterRepository->findFiltered($parkId, $categoryId, $search, $published, $itemCount, $page);

        // Calculer le nombre de pages
        $pageCount = max(ceil($coasters->count() / $itemCount), 1);

        return $this->render('coaster/index.html.twig', [
            'coasters' => $coasters,
            'parks' => $parks,
            'categories' => $categories,
            'pageCount' => $pageCount,
            'currentPage' => $page,
        ]);
    }
    
    #[Route('/coaster/add', name: 'app_coaster_add')]
    #[IsGranted('ROLE_USER')]
    public function add(Request $request, EntityManagerInterface $em): Response {
        $coaster = new Coaster();
        $user = $this->getUser();
        $coaster->setAuthor($user);
        $form = $this->createForm(CoasterType::class, $coaster);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload de l'image
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $fileName = $this->fileUploader->upload($imageFile);
                $coaster->setImageFileName($fileName);
            }

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
        // Vérifie si l'utilisateur a le droit de modifier ce coaster
        $this->denyAccessUnlessGranted('EDIT', $coaster);

        $fileName = $coaster->getImageFileName();
        $this->fileUploader->remove($fileName);

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
            $fileName = $coaster->getImageFileName();
            $this->fileUploader->remove($fileName);

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