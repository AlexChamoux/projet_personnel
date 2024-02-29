<?php

namespace App\Services;

use App\Entity\Order;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\VarDumper\VarDumper;

Class CartServices {
    private $productRepository;
    private $session;
    private $entityManager;

    public function __construct(ProductRepository $productRepository, RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->productRepository = $productRepository;
        $this->session = $requestStack->getCurrentRequest()->getSession();
        $this->entityManager = $entityManager;
    }

    public function addCart ($id) {
        $cart = $this->getCart();
        
        if(isset($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }
        
        $this->updateCart($cart);
    }

    public function addProductToCart($id, $quantity) {
        $cart = $this->getCart();
        
        if(isset($cart[$id])) {
            $cart[$id] += $quantity;
        } else {
            $cart[$id] = $quantity;
        }
        
        $this->updateCart($cart);
    }
    

    public function deleteFromCart ($id) {
        $cart = $this->getCart();

        if(isset($cart[$id])) {
            if($cart[$id] > 1) {
                $cart[$id]--;
            }else{
                unset($cart[$id]);
            }
            $this->updateCart($cart);
        }
    }

    public function deleteAllToCart ($id) {
        $cart = $this->getCart();

        if(isset($cart[$id])) {
            unset($cart[$id]);
            $this->updateCart($cart);
        }
    }

    public function deleteCart () {
        $this->updateCart([]);
    }    

    public function updateCart ($cart) {
        $this->session->set('cart', $cart);
        $this->session->set('cartData', $this->getFullCart());
    }

    public function getCart () {

        return $this->session->get('cart', []);

    }

    public function getFullCart() {
        $cart = $this->getCart();
        $fullCart = [];
        $quantityCart = 0;
        $subtotal = 0;
    
        foreach ($cart as $id => $quantity) {
            $product = $this->productRepository->find($id);
    
            if ($product) {
                $fullCart['products'][] = [
                    'quantity' => $quantity,
                    'product' => $product
                ];
                $quantityCart += $quantity;
                $subtotal += $quantity * $product->getPriceProduct();
            } else {
                $this->deleteFromCart($id);
            }
        }
    
        $fullCart['data'] = [
            'quantityCart' => $quantityCart,
            'subTotal' => $subtotal
        ];
    
        return $fullCart;
    }

    public function placeOrder($userId, $cart, $totalOrder) {
        
        if (!isset($cart['products']) || empty($cart['products'])) {
            // Gérer le cas où le panier est vide
            // Peut-être rediriger l'utilisateur vers une page appropriée ou afficher un message d'erreur
            return;
        }

        foreach ($cart['products'] as $product) {
            // Créer une nouvelle instance d'Order pour chaque produit
            $order = new Order();
            $order->setUserId($userId);
            $order->setDateOrder(new \DateTime());
            $order->setTotalOrder($totalOrder);
            $order->setStatus(1);
    
            // Collecter les informations sur le produit actuel
            $productId = $product['product']->getId();
            $productName = $product['product']->getNameProduct();
            $quantity = $product['quantity'];
            $price = $product['product']->getPriceProduct();
            $slug = $product['product']->getSlug();
            $mainImage = $product['product']->getMainImage();

            $pathMainImage = $slug . '/' . $mainImage;
            
            $total = $price * $quantity;
    
            $order->setOrderedItemId($productId);
            $order->setOrderedItemName($productName);
            $order->setPriceUnit($price);
            $order->setQuantity($quantity);
            $order->setTotal($total);
            $order->setPathMainImage($pathMainImage);
    
            // Persistez et flush l'entité Order actuelle
            $this->entityManager->persist($order);
            $this->entityManager->flush();
        }
    
        // Effacer le panier après avoir passé la commande
        $this->deleteCart();
    }
    
}