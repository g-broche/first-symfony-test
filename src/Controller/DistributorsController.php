<?php

namespace App\Controller;

use App\Entity\Distributors;
use App\Form\DistributorsType;
use App\Repository\DistributorsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/distributors')]
class DistributorsController extends AbstractController
{
    #[Route('/', name: 'app_distributors_index', methods: ['GET'])]
    public function index(DistributorsRepository $distributorsRepository): Response
    {
        return $this->render('distributors/index.html.twig', [
            'distributors' => $distributorsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_distributors_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $distributor = new Distributors();
        $form = $this->createForm(DistributorsType::class, $distributor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($distributor);
            $entityManager->flush();

            return $this->redirectToRoute('app_distributors_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('distributors/new.html.twig', [
            'distributor' => $distributor,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_distributors_show', methods: ['GET'])]
    public function show(Distributors $distributor): Response
    {
        return $this->render('distributors/show.html.twig', [
            'distributor' => $distributor,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_distributors_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Distributors $distributor, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DistributorsType::class, $distributor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_distributors_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('distributors/edit.html.twig', [
            'distributor' => $distributor,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_distributors_delete', methods: ['POST'])]
    public function delete(Request $request, Distributors $distributor, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$distributor->getId(), $request->request->get('_token'))) {
            $entityManager->remove($distributor);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_distributors_index', [], Response::HTTP_SEE_OTHER);
    }
}
