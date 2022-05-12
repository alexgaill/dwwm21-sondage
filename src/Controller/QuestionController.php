<?php

namespace App\Controller;

use App\Entity\Question;
use App\Form\QuestionType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class QuestionController extends AbstractController
{
    #[Route('/questions', name: 'app_question', methods:['GET', 'POST'])]
    public function index(ManagerRegistry $manager, Request $request): Response
    {
        $question = new Question;
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $om = $manager->getManager();
            $om->persist($question);
            $om->flush();
            $this->addFlash('success', "Question ajoutée au sondage ". $question->getSondage()->getTitle());

            return $this->redirectToRoute('app_question');
        }

        return $this->renderForm('question/index.html.twig', [
            'questions' => $manager->getRepository(Question::class)->findAll(),
            'form' => $form
        ]);
    }

    #[Route('/question/{id}', name:'app_update_question', methods:['GET', 'POST'], requirements:['id' => '\d+'])]
    public function update (int $id, ManagerRegistry $manager, Request $request): Response
    {
        $question = $manager->getRepository(Question::class)->find($id);
        if (!$question) {
            $this->addFlash('danger', "Aucune question n'a été trouvée");
            return $this->redirectToRoute('app_question');
        }

        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $om = $manager->getManager();
            $om->persist($question);
            $om->flush();
            $this->addFlash('success', "Question modifiée avec succés");

            return $this->redirectToRoute('app_question');
        }

        return $this->renderForm('question/update.html.twig', [
            'form' => $form
        ]);
    }
}
