<?php

namespace App\Controller;

use App\Entity\Sondage;
use App\Form\SondageType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class SondageController extends AbstractController
{
    #[Route('/', name: 'app_sondage', methods:['GET', 'POST'])]
    public function index(ManagerRegistry $manager, Request $request): Response
    {
        $sondage = new Sondage;
        $form = $this->createForm(SondageType::class, $sondage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $om = $manager->getManager();
            $om->persist($sondage);
            $om->flush();
            $this->addFlash('success', 'Sondage créé');

            return $this->redirectToRoute('app_sondage');
        }

        return $this->renderForm('sondage/index.html.twig', [
            'sondages' => $manager->getRepository(Sondage::class)->findAll(),
            'form' => $form
        ]);
    }

    #[Route('/sondage/{id}', name: "app_single_sondage", requirements:['id' => '\d+'], methods:['GET'])]
    public function single (int $id, ManagerRegistry $manager): Response
    {
        $sondage = $manager->getRepository(Sondage::class)->find($id);

        if (!$sondage) {
            $this->addFlash('danger', 'Aucun sondage existant avec cet id');
            return $this->redirectToRoute('app_sondage');
        }

        return $this->render('sondage/single.html.twig', [
            'sondage' => $sondage
        ]);
    }
}
