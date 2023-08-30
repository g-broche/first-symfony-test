<?php

namespace App\Controller;

use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ApiController extends AbstractController

{
    #[Route('/products', name: 'api_products', methods: ['GET'])]
    public function sendProducts(Request $request, ProductsRepository $productsRepository):Response
    {
        $result = $productsRepository->getApiProducts();
        if($result["success"]){
            if(!empty($result["data"])){
                $responsePackage=["success"=>true, "data"=>$this->formatRawDQLData($result["data"]), "message"=>null];
            }else{
                $responsePackage=["success"=>false, "error"=>0, "message"=>"No products were found"];
            }   
        }else{
            $responsePackage=["success"=>false, "error"=>-1, "message"=>"Server error : the request could not complete"];
        }
        return new Response(json_encode($responsePackage));
    }

    #[Route('/products/{id}', name: 'api_get_product', methods: ['GET'])]
    public function getProductById(string $id, Request $request, ProductsRepository $productsRepository):Response{
        if(isset($id) && is_numeric($id) && intval($id)>0){
            $result = $productsRepository->getSingleApiProduct(intval($id));
            if($result["success"]){
                if(!empty($result["data"])){
                    $responsePackage=["success"=>true, "data"=>$this->formatRawDQLSingleData($result["data"]), "message"=>null];
                }else{
                    $responsePackage=["success"=>false, "error"=>0, "message"=>"No products were found"];
                }   
            }else{
                $responsePackage=["success"=>false, "error"=>-1, "message"=>"Server error : the request could not complete"];
            }
        }else{
            $responsePackage=["success"=>false, "error"=>0, "message"=>"wrong id format was requested"];
        }
        return new Response(json_encode($responsePackage));

    }


    public function formatRawDQLData($data){
        $groupKeysAndAlias=[
            "product_distributors"=>"distributors",
        ];
        $wantedKeysPerEntityAndAlias=[
            "product_distributors"=>["name_distributor" => null],
            "category_id"=>["name_category" => "category"],
            "reference_id"=>["number_reference" => "reference"],
            "seller"=>["username" => "seller_name"]
        ];
        $parsedData=[];
        foreach ($data as $dataElement){
            $newElement=[];
            foreach ($dataElement as $key => $value) {
                if(gettype($value)==="array"){
                    if(array_key_exists($key, $groupKeysAndAlias)){
                        $newSubElementData=[];
                        foreach ($value as $subData) {
                            foreach ($subData as $subKey => $subValue) {
                                if(isset($wantedKeysPerEntityAndAlias[$key]) && array_key_exists($subKey, $wantedKeysPerEntityAndAlias[$key])){
                                    if($wantedKeysPerEntityAndAlias[$key][$subKey] !== null){
                                        $newSubElementData[]=[$wantedKeysPerEntityAndAlias[$key][$subKey]=>$subValue];
                                    }else{
                                        $newSubElementData[]=$subValue;
                                    }
                                }
                            }
                        }
                        $newElement[$groupKeysAndAlias[$key]]=$newSubElementData;
                    }else{
                            foreach ($value as $subKey => $subValue) {
                                if(isset($wantedKeysPerEntityAndAlias[$key]) && array_key_exists($subKey, $wantedKeysPerEntityAndAlias[$key])){
                                    $newElement[$wantedKeysPerEntityAndAlias[$key][$subKey]]=$subValue;
                                }
                            } 
                        }
                }else{
                    $newElement[$key]=$value;
                }
            }
            $parsedData[]=$newElement;
        }
        return $parsedData;
    }
    public function formatRawDQLSingleData($data){
        $groupKeysAndAlias=[
            "product_distributors"=>"distributors",
        ];
        $wantedKeysPerEntityAndAlias=[
            "product_distributors"=>["name_distributor" => null],
            "category_id"=>["name_category" => "category"],
            "reference_id"=>["number_reference" => "reference"],
            "seller"=>["username" => "seller_name"]
        ];
        $parsedData=[];
            foreach ($data as $key => $value) {
                if(gettype($value)==="array"){
                    if(array_key_exists($key, $groupKeysAndAlias)){
                        $newSubElementData=[];
                        foreach ($value as $subData) {
                            foreach ($subData as $subKey => $subValue) {
                                if(isset($wantedKeysPerEntityAndAlias[$key]) && array_key_exists($subKey, $wantedKeysPerEntityAndAlias[$key])){
                                    if($wantedKeysPerEntityAndAlias[$key][$subKey] !== null){
                                        $newSubElementData[]=[$wantedKeysPerEntityAndAlias[$key][$subKey]=>$subValue];
                                    }else{
                                        $newSubElementData[]=$subValue;
                                    }
                                }
                            }
                        }
                        $newElement[$groupKeysAndAlias[$key]]=$newSubElementData;
                    }else{
                            foreach ($value as $subKey => $subValue) {
                                if(isset($wantedKeysPerEntityAndAlias[$key]) && array_key_exists($subKey, $wantedKeysPerEntityAndAlias[$key])){
                                    $newElement[$wantedKeysPerEntityAndAlias[$key][$subKey]]=$subValue;
                                }
                            } 
                        }
                }else{
                    $newElement[$key]=$value;
                }
            }
            $parsedData[]=$newElement;
            
        return $parsedData[0];
    }
}



