<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Controller\ProductCrudController;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(ProductRepository $prodR): Response
    {
        $products = $prodR->findAll();
        $productBest = $prodR->findByIsBest(1);
        $productNew = $prodR->findByIsNew(1);
        

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'product' => $products,
            'productBest' => $productBest,
            'productNew' => $productNew,
        ]);
    }

    #[Route('/product/details/{slug}', name: 'product_details')]
    public function show(?Product $product, ?Category $category): Response{

            if(!$product) {
                return $this->redirectToRoute('app_index');
            }

            return $this->render('partials/detail_product.html.twig', [
                'product'=> $product,
            ]);
    }

    #[Route('/product/category/{id}/', name: 'product_category')]
    public function category(Category $category, ProductRepository $productRep):Response{

        $products = $productRep->findBy(['category' => $category]);

        return $this->render('category/category.html.twig', [
            'category' => $category,
            'products' => $products,
        ]);

    }
}
