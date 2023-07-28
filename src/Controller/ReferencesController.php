<?php

namespace App\Controller;

use App\Entity\References;
use App\Form\ReferencesType;
use App\Repository\ReferencesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/references')]
class ReferencesController extends AbstractController
{
    #[Route('/', name: 'app_references_index', methods: ['GET'])]
    public function index(ReferencesRepository $referencesRepository): Response
    {
        return $this->render('references/index.html.twig', [
            'references' => $referencesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_references_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reference = new References();
        $form = $this->createForm(ReferencesType::class, $reference);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reference);
            $entityManager->flush();

            return $this->redirectToRoute('app_references_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('references/new.html.twig', [
            'reference' => $reference,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_references_show', methods: ['GET'])]
    public function show(References $reference): Response
    {
        return $this->render('references/show.html.twig', [
            'reference' => $reference,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_references_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, References $reference, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReferencesType::class, $reference);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_references_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('references/edit.html.twig', [
            'reference' => $reference,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_references_delete', methods: ['POST'])]
    public function delete(Request $request, References $reference, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reference->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reference);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_references_index', [], Response::HTTP_SEE_OTHER);
    }
}
