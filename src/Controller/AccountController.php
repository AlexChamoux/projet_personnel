<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Entity\Credits;
use App\Repository\CreditsRepository;
use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Entity\Voucher;
use App\Repository\VoucherRepository;
use App\Entity\Address;
use App\Repository\AddressRepository;
use App\Form\AddressType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function index(): Response
    {
        $user = $this->getUser();

        if(empty($user)){
            return $this->redirectToRoute('app_login');
        }

        return $this->render('account/index.html.twig', [
            'user' => $this->getUser(),
        ]);
        
    }

    #[Route('/account/personal_information', name: 'account_personal_information')]
    public function personalInformation(UserRepository $userRep, AddressRepository $AddressRep): Response
    {
        $user = $this->getUser();
        
        $userType = $this->getUser()->getUserType();
        $addresses = [];

        if(empty($user)){
            return $this->redirectToRoute('app_login');
        }

        $userData = $userRep->findOneBy(['id' => $this->getUser()->getId()]);
        $addresses = $AddressRep->findBy(['user_id' => $user->getId()]);

        if ($userType === 'standard') {

            return $this->render('account/personal_information.html.twig', [
                'user' => $userData,
                'addresses' => $addresses,
            ]);
        }elseif($userType === 'pro'){

            return $this->render('account/personal_information_pro.html.twig', [
                'user' => $userData,
                'addresses' => $addresses,
            ]);
        }
    }

    #[Route('/account/credits_discount_voucher', name: 'account_credits_discount_voucher')]
    public function creditsVoucher(CreditsRepository $creditsRep, VoucherRepository $voucherRep): Response
    {
        $userId = $this->getUser()->getId();

        if(empty($userId)){
            return $this->redirectToRoute('app_login');
        }

        $credits = $creditsRep->findBy(['user_id' => $userId]);
        $vouchers = $voucherRep->findBy(['user_id' => $userId]);
        
        
        return $this->render('account/credits_discount_voucher.html.twig', [
            'credits' => $credits,
            'vouchers' => $vouchers,
        ]);
    }

    #[Route('/account/order', name: 'account_order')]
    public function order(OrderRepository $orderRep): Response
    {
        $userId = $this->getUser()->getId();

        if(empty($userId)){
            return $this->redirectToRoute('app_login');
        }

        // $orders= $orderRep->findBy(['user_id' => $userId]);
        $orders= $orderRep->findBy(['user_id' => $userId], ['date_order' => 'DESC']);

        return $this->render('account/order.html.twig', [
            'orders' => $orders,
        ]);
    }

    
}
