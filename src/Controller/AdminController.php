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
use App\Form\AdminType;
use App\Form\AdminChangePasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


#[Route('/admin')]
class AdminController extends AbstractController
{    

    #[Route('/', name: 'app_admin_index')]
    #[Security("ROLE_SUPER_ADMIN")]
    public function indexAdmin(UserRepository $userRepository): Response
    {
        
        // $admins = $userRepository->findAll();
        $admins = $userRepository->findByRoles('ROLE_ADMIN');
        // $admins = $userRepository->findBy(['roles' => ['ROLE_ADMIN']]);
        // dd($admins);
        return $this->render('admin/indexAdmin.html.twig', [
            'admins' => $admins,
        ]);
    }
    
    #[Route('/add', name: 'app_admin_add', methods: ['GET', 'POST'])]
    #[Security("ROLE_SUPER_ADMIN")]  
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(AdminType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $user->setPassword(
                password_hash(
                    $form->get('password')->getData(), PASSWORD_DEFAULT
                )
            );
            $user->setStatus(1);
            $user->setRoles(['ROLE_ADMIN']);
            // Enregistrer l'utilisateur Admin dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Afficher un message de succès
            $this->addFlash('success', 'L\'administrateur a été ajouté avec succès.');

            return $this->redirectToRoute('app_admin_index');
        }

        return $this->render('admin/addAdmin.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    
    #[Route('/delete/{id}', name: 'app_admin_delete', methods: ['POST'])]
    #[Security("ROLE_SUPER_ADMIN")]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {        
        // Supprimer l'utilisateur Admin de la base de données
        $entityManager->remove($user);
        $entityManager->flush();

        // Afficher un message de succès
        $this->addFlash('success', 'L\'administrateur a été supprimé avec succès.');
        

        return $this->redirectToRoute('app_admin_index');
    }

    #[Route('/change', name: 'app_admin_change_password')]
    #[Security("ROLE_ADMIN")]
    public function changePassword(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();
        $currentPassword = $user->getPassword();
        $form = $this->createForm(AdminChangePasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // dd(password_verify( ( $form->get('password')->getData() ), $currentPassword));

            if (!password_verify( ( $form->get('password')->getData() ), $currentPassword) ) {

                echo '<script>alert("Le mot de passe actuel est incorrect.");history.back();</script>';
                return new Response();
                
            }else{
                // Hacher le nouveau mot de passe
                $user->setPassword(
                    password_hash(
                        $form->get('newPassword')->getData(), PASSWORD_DEFAULT)
                );

                // Enregistrer l'utilisateur dans la base de données
                $entityManager->flush();

                // Afficher un message de succès
                $this->addFlash('success', 'Votre mot de passe a été modifié avec succès.');

                return $this->redirectToRoute('app_admin_account');
            }
        }

        return $this->render('admin/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/account', name: 'app_admin_account')]
    #[Security("ROLE_ADMIN")]
    public function adminAccount(): Response
    {
        
        return $this->render('admin/account.html.twig');
    }

    #[Route('/user', name: 'app_admin_index_user')]
    #[Security("ROLE_ADMIN")]
    public function indexUser(UserRepository $userRepository): Response
    {
        $users = $userRepository->findByRoles('ROLE_USER');
        
        return $this->render('admin/user/indexUser.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/user/{id}/account', name: 'app_admin_user_account')]
    #[Security("ROLE_ADMIN")]
    public function userAccount(int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);
        // dd($user);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        return $this->render('admin/user/userAccount.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/user/{id}/account/personal_information/', name: 'app_admin_user_account_personal_information')]
    #[Security("ROLE_ADMIN")]
    public function personalInformation(UserRepository $userRep, AddressRepository $AddressRep, int $id): Response
    {
        $user = $userRep->find($id);

        $userType = $user->getUserType();
        $addresses = [];

        if(empty($user)){
            return $this->redirectToRoute('app_login');
        }

        $userData = $userRep->findOneBy(['id' => $user]);
        $addresses = $AddressRep->findBy(['user_id' => $user->getId()]);

        if ($userType === 'standard') {

            return $this->render('admin/user/user_personal_information.html.twig', [
                'user' => $userData,
                'addresses' => $addresses,
            ]);
        }elseif($userType === 'pro'){

            return $this->render('admin/user/user_personal_information_pro.html.twig', [
                'user' => $userData,
                'addresses' => $addresses,
            ]);
        }
    }

    #[Route('/user/{id}/account/credits_discount_voucher/}', name: 'app_admin_user_account_credits_discount_voucher')]
    #[Security("ROLE_ADMIN")]
    public function creditsVoucher(UserRepository $userRep, CreditsRepository $creditsRep, VoucherRepository $voucherRep, int $id): Response
    {
        $userId = $userRep->find($id);

        if(empty($userId)){
            return $this->redirectToRoute('app_login');
        }

        $credits = $creditsRep->findBy(['user_id' => $userId]);
        $vouchers = $voucherRep->findBy(['user_id' => $userId]);
        
        
        return $this->render('admin/user/user_credits_discount_voucher.html.twig', [
            'user' => $userId,
            'credits' => $credits,
            'vouchers' => $vouchers,
        ]);
    }

    #[Route('/user/{id}/account/order/}', name: 'app_admin_user_account_order')]
    #[Security("ROLE_ADMIN")]
    public function order(UserRepository $userRep, OrderRepository $orderRep, int $id): Response
    {
        $userId = $userRep->find($id);

        if(empty($userId)){
            return $this->redirectToRoute('app_login');
        }

        // $orders= $orderRep->findBy(['user_id' => $userId]);
        $orders= $orderRep->findBy(['user_id' => $userId], ['date_order' => 'DESC']);

        return $this->render('admin/user/user_order.html.twig', [
            'user' => $userId,
            'orders' => $orders,
        ]);
    }

    #[Route('/user/{id}/account/address/', name: 'app_admin_user_address_index', methods: ['GET'])]
    public function index(UserRepository $userRep, AddressRepository $addressRep, int $id): Response
    {
        $userId = $userRep->find($id);

        $addresses = $addressRep->findBy(['user_id' => $userId]);
        
        return $this->render('admin/user/userAddress.html.twig', [
            'user' => $userId,
            'addresses' => $addresses,
        ]);
    }

}








