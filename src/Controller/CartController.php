<?php

namespace App\Controller;

use App\Services\CartServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    private $cartServices;

    public function __construct(CartServices $cartServices)
    {
        $this->cartServices = $cartServices;
    }

    #[Route('/cart', name: 'app_cart')]
    public function index(): Response
    {
        
        $cart = $this->cartServices->getFullCart();
        // dd($cart['products'][0]['product']->getMainImage());
        // dd($cart);
        if(!isset($cart['products'])) {
            return $this->redirectToRoute('app_index');
        }

        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
            'cart' => $cart
        ]);
        
    }

    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function addToCart($id): Response
    {
        $this->cartServices->addCart($id);

        return $this->redirectToRoute("app_cart");
    }

    #[Route('/cart/addqt/{id}', name: 'cart_add_qt')]
    public function addProductToCart(Request $request, $id): Response
    {
        $quantity = $request->request->get('quantity', 1);
        $this->cartServices->addProductToCart($id, $quantity);

        return $this->redirectToRoute("app_cart");
    }


    #[Route('/cart/delete/{id}', name: 'cart_delete')]
    public function deleteFromCart($id): Response
    {
        $this->cartServices->deleteFromCart($id);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/deleteAll/{id}', name:'cart_delete_all_to')]
    public function deleteAllToCart($id): Response
    {
        $this->cartServices->deleteAllToCart($id);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/deleteAll', name:'cart_delete_all')]
    public function deleteAll(): Response
    {
        $this->cartServices->deleteCart();

        return $this->redirectToRoute('app_index');
    }

    #[Route('/cart/place-order', name: 'cart_place_order')]
    public function placeOrder(): Response {
        
        $userId = $this->getUser()->getId(); 

        if(empty($userId)){
            return $this->redirectToRoute('app_login');
        }
        
        $cart = $this->cartServices->getFullCart();
        $totalOrder = $cart['data']['subTotal'];

        // Passer la commande
        $this->cartServices->placeOrder($userId, $cart, $totalOrder);

        // Rediriger vers une page de confirmation de commande ou autre
        // return $this->redirectToRoute('cart_place_order_confirmation');

        return $this->redirectToRoute('account_order');        
    }
}