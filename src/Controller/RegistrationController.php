<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\RegistrationFormProType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationFormType::class, $user);
        // dd($form);
        $form->handleRequest($request);

        $formPro = $this->createForm(RegistrationFormProType::class, $user);
        // dd($formPro);
        $formPro->handleRequest($request);
        
        // dd($request->request->all());
        

        if ($form->isSubmitted() && $form->isValid()) {

            // encodez le mot de passe
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setStatus(1);
            $user->setUserType("standard");
            $user->setRoles(['ROLE_USER']);

             // Vérifiez si le champ `agreeTerms` est coché
            if (!$form->get('agreeTerms')->getData()) {
                // Si le champ n'est pas coché, ajoutez une erreur
                $form->addError(new FormError('Vous devez accepter les termes.'));
            } else {
                // Si le champ est coché, enregistrez l'utilisateur
                $entityManager->persist($user);
                $entityManager->flush();
                
                return $this->redirectToRoute('app_index');
            }

            // $entityManager->persist($user);
            // $entityManager->flush();
            
            return $this->redirectToRoute('app_index');
        }

        if ($formPro->isSubmitted() && $formPro->isValid()) {
            
            // encodez le mot de passe
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $formPro->get('plainPassword')->getData()
                )
            );
            $user->setStatus(1);
            $user->setUserType("pro");
            $user->setRoles(['ROLE_USER']);
            
            $entityManager->persist($user);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_index');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'registrationFormPro' => $formPro->createView(),
        ]);
    }
}
