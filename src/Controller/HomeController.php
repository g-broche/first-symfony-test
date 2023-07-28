<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Form\CategoriesType;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index():Response
    {
        return $this->render('home/index.html.twig', []);
    }
    #[Route('/test', name: 'test', methods: ['GET'])]
    public function test():Response
    {
        return $this->render('home/test.html.twig', []);
    }
    #[Route('/validate', name: 'app_validate_mail', methods: ['GET'])]
    public function validateMail():Response
    {
        return $this->render('home/validateMail.html.twig', []);
    }
}