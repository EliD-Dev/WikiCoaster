<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Coaster;
use App\Form\CoasterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class CoasterController extends AbstractController{
    
    #[Route('/coaster/add')]
    public function add(Request $request, EntityManagerInterface $em): Response {
        $coaster = new Coaster();
        $form = $this->createForm(CoasterType::class, $coaster);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($coaster);
            $em->flush();

            // Redirection après soumission réussie
            return $this->redirectToRoute('app_app_index');
        }

        return $this->render('coaster/add.html.twig', ['coasterForm' => $form->createView(),]);
    }


}
?>