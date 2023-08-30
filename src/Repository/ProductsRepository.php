<?php

namespace App\Repository;

use App\Entity\Products;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Types\Types;
use PhpParser\Node\Stmt\TryCatch;

/**
 * @extends ServiceEntityRepository<Products>
 *
 * @method Products|null find($id, $lockMode = null, $lockVersion = null)
 * @method Products|null findOneBy(array $criteria, array $orderBy = null)
 * @method Products[]    findAll()
 * @method Products[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Products::class);
    }

    public function getLastProduct($userIdentifier){
        $getUserId = $this->_em->createQueryBuilder()
        ->select('u.id')
        ->from('App\Entity\User', 'u')
        ->where('u.username = :identifier')
        ->setParameter('identifier', $userIdentifier, Types::STRING )
        ->getQuery()
        ->getOneOrNullResult()['id'];

        $lastProduct = $this->_em->createQueryBuilder()
        ->select('p')
        ->from('App\Entity\Products', 'p')
        ->where('p.seller = :id')
        ->orderBy('p.id', 'DESC')
        ->setParameter('id', $getUserId, Types::INTEGER)
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();
        return $lastProduct;
    }

    public function getSellerProducts($userIdentifier){
        $getUserId = $this->_em->createQueryBuilder()
        ->select('u.id')
        ->from('App\Entity\User', 'u')
        ->where('u.username = :identifier')
        ->setParameter('identifier', $userIdentifier, Types::STRING )
        ->getQuery()
        ->getOneOrNullResult()['id'];

        $getProducts = $this->_em->createQueryBuilder()
        ->select('p, r, c, d')
        ->from('App\Entity\Products', 'p')
        ->leftJoin('p.reference_id', 'r')
        ->leftJoin('p.category_id', 'c')
        ->leftJoin('p.product_distributors', 'd')
        ->where('p.seller = :id')
        ->setParameter('id', $getUserId, Types::INTEGER )
        ->getQuery()
        ->getArrayResult();

        // dd($getProducts);
        return $getProducts;
    }

    public function filterProductsOnRequest($userIdentifier, array $distributors, float $minPrice,
    float|null $maxPrice, $targetCategory, $searchPattern)
    {
        $getUserId = $this->_em->createQueryBuilder()
        ->select('u.id')
        ->from('App\Entity\User', 'u')
        ->where('u.username = :identifier')
        ->setParameter('identifier', $userIdentifier, Types::STRING )
        ->getQuery()
        ->getOneOrNullResult()['id'];

        $getFilteredProducts=$this->_em->createQueryBuilder()
        ->select('p, r, c, d')
        ->from('App\Entity\Products', 'p')
        ->leftJoin('p.reference_id', 'r')
        ->leftJoin('p.category_id', 'c')
        ->leftJoin('p.product_distributors', 'd')
        ->where('p.seller = :id')
        ->setParameter('id', $getUserId, Types::INTEGER );

        if(!empty($distributors)){
            $getFilteredProducts
            ->andWhere('d.name_distributor IN(:distributorList)')
            ->setParameter('distributorList', array_values($distributors));
        }
        if($minPrice>=0){
            $getFilteredProducts
            ->andWhere('p.price_product >= :minPrice')
            ->setParameter('minPrice', $minPrice, Types::FLOAT);
        }
        if($maxPrice!= null && $maxPrice>=0){
            $getFilteredProducts
            ->andWhere('p.price_product <= :maxPrice')
            ->setParameter('maxPrice', $maxPrice, Types::FLOAT);
        }

        if(!empty($targetCategory)){
            $getFilteredProducts
            ->andWhere('c.name_category = :targetCategory')
            ->setParameter('targetCategory', $targetCategory, Types::STRING);
        }
        if(!empty($searchPattern)){
            $getFilteredProducts
            ->andWhere('p.name_product like :pattern')
            ->setParameter('pattern', '%'.$searchPattern.'%', Types::STRING);
        }

        return $getFilteredProducts->getQuery()->getArrayResult();
    }

    public function searchProducts($userIdentifier, string|null $targetCategory, string|null $searchPattern){
        $getUserId = $this->_em->createQueryBuilder()
        ->select('u.id')
        ->from('App\Entity\User', 'u')
        ->where('u.username = :identifier')
        ->setParameter('identifier', $userIdentifier, Types::STRING )
        ->getQuery()
        ->getOneOrNullResult()['id'];

        $getSearchedProducts=$this->_em->createQueryBuilder()
        ->select('p, r, c, d')
        ->from('App\Entity\Products', 'p')
        ->leftJoin('p.reference_id', 'r')
        ->leftJoin('p.category_id', 'c')
        ->leftJoin('p.product_distributors', 'd')
        ->where('p.seller = :id')
        ->setParameter('id', $getUserId, Types::INTEGER );

        if(!empty($targetCategory)){
            $getSearchedProducts
            ->andWhere('c.name_category = :targetCategory')
            ->setParameter('targetCategory', $targetCategory, Types::STRING);
        }
        if(!empty($searchPattern)){
            $getSearchedProducts
            ->andWhere('p.name_product like :pattern')
            ->setParameter('pattern', '%'.$searchPattern.'%', Types::STRING);
        }
        return $getSearchedProducts->getQuery()->getArrayResult();
    }


    public function getApiProducts(){
        try {
            $getProducts = $this->_em->createQueryBuilder()
            ->select('p, r, c, d, u')
            ->from('App\Entity\Products', 'p')
            ->leftJoin('p.reference_id', 'r')
            ->leftJoin('p.category_id', 'c')
            ->leftJoin('p.product_distributors', 'd')
            ->leftJoin('p.seller', 'u')
            ->getQuery()
            ->getArrayResult();
            if(!empty($getProducts)){
                return ["success" => true, "data" => $getProducts];
            }else{
                return ["success" => true, "data" => null];
            }
        } catch (\Throwable $th) {
            return ["success" => false, "data" => null];
        }
    }

    public function getSingleApiProduct($id){

        try {
            $getProducts = $this->_em->createQueryBuilder()
            ->select('p, r, c, d, u')
            ->from('App\Entity\Products', 'p')
            ->leftJoin('p.reference_id', 'r')
            ->leftJoin('p.category_id', 'c')
            ->leftJoin('p.product_distributors', 'd')
            ->leftJoin('p.seller', 'u')
            ->where('p.id=:id')
            ->setParameter('id', $id, Types::INTEGER )
            ->getQuery()
            ->getArrayResult();
            if(!empty($getProducts)){
                return ["success" => true, "data" => $getProducts[0]];
            }else{
                return ["success" => true, "data" => null];
            }
        } catch (\Throwable $th) {
            return ["success" => false, "data" => null];
        }
    }
}

