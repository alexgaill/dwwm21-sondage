<?php

namespace App\Controller;

use App\Entity\Reponse;
use App\Form\ReponseType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReponseController extends AbstractController
{
    #[Route('/reponse', name: 'app_reponse', methods:['GET', 'POST'])]
    public function index(ManagerRegistry $manager, Request $request): Response
    {
        $reponse = new Reponse;
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reponse->setScore(0);
            $om = $manager->getManager();
            $om->persist($reponse);
            $om->flush();
            $this->addFlash('success', 'Une nouvelle réponse a été associée à la question '. $reponse->getQuestion()->getTitle());

            return $this->redirectToRoute('app_reponse');
        }
        return $this->renderForm('reponse/index.html.twig', [
            'reponses' => $manager->getRepository(Reponse::class)->findAll(),
            'form' => $form
        ]);
    }

    #[Route('/reponse/{id}', name:'app_update_reponse', methods:['GET', 'POST'], requirements:['id' => '\d+'])]
    public function update (int $id, ManagerRegistry $manager, Request $request): Response
    {
        $reponse = $manager->getRepository(Reponse::class)->find($id);
        if (!$reponse) {
            $this->addFlash('danger', "Aucune réponse n'a été trouvée");
            return $this->redirectToRoute('app_reponse');
        }

        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $om = $manager->getManager();
            $om->persist($reponse);
            $om->flush();
            $this->addFlash('success', "Réponse modifiée avec succés");

            return $this->redirectToRoute('app_reponse');
        }

        return $this->renderForm('reponse/update.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('reponse/{id}/vote', name:'app_vote_reponse', methods:['GET'], requirements:['id' => "\d+"])]
    public function vote (int $id, ManagerRegistry $manager): Response
    {
        $reponse = $manager->getRepository(Reponse::class)->find($id);
        if (!$reponse) {
            $this->addFlash('danger', 'Aucune réponse trouvée');
        } else {
            $reponse->setScore($reponse->getScore() +1);
            $om = $manager->getManager();
            $om->persist($reponse);
            $om->flush();
            $this->addFlash('success', 'Vote pris en compte');
        }

        return $this->redirectToRoute('app_sondage');
    }
}
