<?php

namespace App\Controller;

use App\Entity\Products;
use App\Entity\User;
use App\Form\ProductsType;
use App\Form\FilterProductFormType;
use App\Repository\ProductsRepository;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Orm\EntityPaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Distributors;
use App\Repository\DistributorsRepository;
use App\Controller\ProductsFilterController;
use PhpParser\Node\Arg;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

#[Route('/products')]
class ProductsController extends AbstractController
{
    public function __construct(private Security $security){}

    #[Route('/', name: 'app_products_index', methods: ['GET', 'POST'])]
    public function index(Request $request, ProductsRepository $productsRepository, DistributorsRepository $distributorsRepository,
    CategoriesRepository $categoriesRepository): Response
    {
        
        $user = $this->security->getUser();
        if(!empty($user)){
            $userIdentifier = $user->getUserIdentifier();
            $previousQuery=$this->extractQuery($_GET);
            if(isset($_GET['filter']) && $_GET['filter']==="filter"){
                $productToDisplay = $this->handleFilterRequest($productsRepository, $_GET, $userIdentifier);
                return $this->render('products/index.html.twig', [
                    'products' => $productToDisplay,
                    'distributorList'=> $distributorsRepository->getAllByName(),
                    'categoryList'=> $categoriesRepository->getAllByName(),
                    'previousQueryParameters' => $previousQuery
                ]);
            }elseif(isset($_GET['search']) && $_GET['search']==="search"){
                $productToDisplay = $this->handleSearchRequest($productsRepository, $_GET, $userIdentifier);
                return $this->render('products/index.html.twig', [
                    'products' => $productToDisplay,
                    'distributorList'=> $distributorsRepository->getAllByName(),
                    'categoryList'=> $categoriesRepository->getAllByName(),
                    'previousQueryParameters' => $previousQuery
                ]);
            }else{
                return $this->render('products/index.html.twig', [
                    'products' => $productsRepository->getSellerProducts($userIdentifier),
                    'lastProduct' => $productsRepository->getLastProduct($userIdentifier),
                    'distributorList'=> $distributorsRepository->getAllByName(),
                    'categoryList'=> $categoriesRepository->getAllByName(),
                ]);
            }


        }

    }

    #[Route('/new', name: 'app_products_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Products();
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['image_product']->getData();
            if (!is_string($file)) {
                $file_name = $file->getClientOriginalName();
                $file->move(
                    $this->getParameter("images_directory"),
                    $file_name
                );
                $product->setImageProduct($file_name);
            } else {
                return $this->redirect($this->generateUrl("app_produits"));
            }

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('app_products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('products/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_products_show', methods: ['GET'])]
    public function show(Products $product): Response
    {
        return $this->render('products/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_products_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Products $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('products/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_products_delete', methods: ['POST'])]
    public function delete(Request $request, Products $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_products_index', [], Response::HTTP_SEE_OTHER);
    }

    /* ************************** */
    /* ***** UTILITY METHODS **** */
    /* ************************** */

    // Handle form request logic for the distributors and price range filter
    private function handleFilterRequest($productsRepository, $requestData, $userId) {
        $distributorArray=[];
        $minPrice=null;
        $maxPrice=null;
        $targetCategory=null;
        $searchPattern=null;
        if (isset($requestData['allowedDistributors']) && !empty($requestData['allowedDistributors'])){
            foreach($requestData['allowedDistributors'] as $distributorName){
                $distributorArray[]=$distributorName;
            }
        }
        if(isset($requestData['minPrice']) && $requestData['minPrice']!= null &&
        is_numeric($requestData['minPrice']) && floatval($requestData['minPrice'])>0){
            $minPrice=floatval($requestData['minPrice']);
        }else{
            $minPrice=0;
        }
        if(isset($requestData['maxPrice']) && $requestData['maxPrice']!= null &&
        is_numeric($requestData['maxPrice']) && floatval($requestData['maxPrice'])>$minPrice){
            $maxPrice=floatval($requestData['maxPrice']);
        }else{
            $maxPrice=null;
        }
        if(isset($requestData['category'])){
            $targetCategory=($requestData['category'] !== "all"? $requestData['category'] : null);
        }else{
            $targetCategory=null;
        }

        if(isset($requestData['searchQuery'])){
            $searchPattern=($requestData['searchQuery'] !== ""? $requestData['searchQuery'] : null);
        }else{
            $searchPattern=null;
        }
        return $productsRepository->filterProductsOnRequest($userId, $distributorArray, $minPrice, $maxPrice,
         $targetCategory, $searchPattern);
    }

    // Handle form request logic for the category and product name search filter
    private function handleSearchRequest($productsRepository, $requestData, $userId){
        if(isset($requestData['category'])){
            $targetCategory=($requestData['category'] !== "all"? $requestData['category'] : null);
        }else{
            $targetCategory=null;
        }

        if(isset($requestData['searchQuery'])){
            $searchPattern=($requestData['searchQuery'] !== ""? $requestData['searchQuery'] : null);
        }else{
            $searchPattern=null;
        }

        return $productsRepository->searchProducts($userId, $targetCategory, $searchPattern);
    }

    // Extract the separate GET values from the request URI for further use in next rendering
    private function extractQuery(array $requestData){
        $currentQuery = [];
        if(isset($requestData['category']) && !empty($requestData['category'])){
            $currentQuery['targetCategory'] = $requestData['category'];
        }
        if(isset($requestData['searchQuery']) && !empty($requestData['searchQuery'])){
            $currentQuery['searchedName'] = $requestData['searchQuery'];
        }
        if(isset($requestData['allowedDistributors']) && !empty($requestData['allowedDistributors'])){
            $currentQuery['allowedDistributors'] = $requestData['allowedDistributors'];
        }
        if(isset($requestData['minPrice']) && $requestData['minPrice']!= null &&
        is_numeric($requestData['minPrice']) && floatval($requestData['minPrice'])>0){
            $minPrice=floatval($requestData['minPrice']);
            $currentQuery['minPrice'] = $requestData['minPrice'];
        }else{
            $minPrice=0;
        }
        if(isset($requestData['maxPrice']) && $requestData['maxPrice']!= null &&
        is_numeric($requestData['maxPrice']) && floatval($requestData['maxPrice'])>$minPrice){
            $currentQuery['maxPrice'] = $requestData['maxPrice'];
        }
        return $currentQuery;
    }
}
