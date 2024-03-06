<?php

namespace App\Controller;

use App\Entity\Product;
use App\ENtity\Category;
use App\Entity\Images;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Constraints\File;


#[Route('/admin/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $pRepo, CategoryRepository $cRepo): Response
    {
        $products = $pRepo->findAll();
        $categories = $cRepo->findAll();


        return $this->render('product/index.html.twig', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setSlug($slugger);

            $productDir = $this->getParameter('upload_directory') . '/' . $product->getSlug();
            if (!file_exists($productDir)) {
                mkdir($productDir, 0777, true);
            }

            $imagesData = $form->get('images')->getData();

            // Vérification de la sélection d'une image principale
            $mainImageCount = 0;
            foreach ($imagesData as $imageData) {
                if ($imageData->getIsMain(true)) {
                    $mainImageCount++;
                }
            }

            if ($mainImageCount === 0) {
                echo '<script>alert("Veuillez sélectionner une image principale.");history.back();</script>';
                return new Response();
            }

            if ($mainImageCount > 1) {
                echo '<script>alert("Vous ne pouvez sélectionner qu\'une seule image principale.");history.back();</script>';
                return new Response();
            }

            // Traitement des images
            foreach ($imagesData as $imageData) {
                $image = new Images();
                $imageFile = $imageData->getFile();

                if ($imageFile) {
                    $fileName = $imageFile->getClientOriginalName();
                    $imageFile->move($productDir, $fileName);
                    $image->setImageFileName($fileName);

                    $isMain = $imageData->getIsMain();
                    $image->setProduct($product);
                    $product->addImage($image);

                    if ($isMain) {
                        $product->setMainImage($fileName);
                        $image->setIsMain($isMain);
                    }

                    $entityManager->persist($image);
                }
            }

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }   
    


    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id, EntityManagerInterface $entityManager, ProductRepository $pRepo): Response
    {
        $product = $pRepo->find($id);
        $existingImagesData = $product->getImages()->toArray();
        // dd($existingImagesData);
        $existingImages = [];
        foreach ($existingImagesData as $existingImage) {
            $imageData = [
                'imageFileName' => $existingImage->getImageFileName(),
                'id' => $existingImage->getId(),
                'isMain' => $existingImage->getIsMain(),
            ];
            $existingImages[] = $imageData;
        }
        $imagesSource = "upload/products/";
        $imageDirectory = $imagesSource . $product->getSlug();
        // dd($imageDirectory);
        
        if (!$product) {
            throw $this->createNotFoundException('Produit non trouvé avec l\'ID : ' . $id);
        }

        $form = $this->createForm(ProductType::class, $product, [
            'imageDirectory' => $imageDirectory,
        ]);
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {            
            $productDir = $this->getParameter('upload_directory') . '/' . $product->getSlug();

            $mainImagePresent = false;
            foreach ($product->getImages() as $image) {
                if ($image->getIsMain()) {
                    $mainImagePresent = true;
                    break;
                }
            }
            
            // Vérifier que seule une image principale est sélectionnée parmi les nouvelles images
            $mainImageCount = 0;
            $newImages = $form->get('images')->getData();
            // dd($newImages);
            foreach ($newImages as $index => $imageData) {
                $isMain = $imageData->getIsMain() ?? false;
                
                if ($isMain) {
                    $mainImageCount++;
                }
            }

            // Vérifier si une image principale existe déjà parmi les images existantes
            foreach ($existingImagesData as $existingImage) {
                if ($existingImage->getIsMain()) {
                    $mainImageCount++;
                }
            }

            $imagesAdd = $form->get('images')->getData();

            if ($mainImagePresent === true){
                // dd('coucou imagePresent');
                if(!empty($imagesAdd)){

                    foreach ($imagesAdd as $imageData) {
                    $image = new Images();
                    $imageFile = $imageData->getFile();
                    // dd($imageFile);
                    if ($imageFile) {
                        $fileName = $imageFile->getClientOriginalName();
                        $imageFile->move($productDir, $fileName);
                        $image->setImageFileName($fileName);
                    }
        
                    $image->setProduct($product);
                    $product->addImage($image);
        
                    $entityManager->persist($image);
                    }
                }
                
            }else{
                // dd($form->get('images')->getData());
                // dd($mainImageCount);
                if (!empty($imagesAdd) && $mainImageCount === 1){
                    // 1ere boucle pour créer et définir l'image pricnipale

                        foreach ($imagesAdd as $index => $imageData) {
                            $image = new Images();
                            // dd($imageData->getFile());
                            $imageFile = $imageData->getFile();
                            // dd($imageFile);
                            
                            if ($imageFile) {
                                $isMain = $imageData->getIsMain();

                                if ($isMain) {
                                    $fileName = $imageFile->getClientOriginalName();
                                    $imageFile->move($productDir, $fileName);
                                    $image->setImageFileName($fileName);
                                
                                    $mainImage = $image->getImageFileName();
                                    $product->setMainImage($image->getImageFileName());
                                    $image->setIsMain($isMain);

                                    $image->setProduct($product);
                                    $product->addImage($image);
                                }
                                
        
                                
                            }
                        }
                }else{
                    if ($mainImageCount === 0){
                        echo '<script>alert("Veuillez définir une image principale.");history.back();</script>';
                        return new Response();
                    }
                    if ($mainImageCount > 1){
                        echo '<script>alert("Vous ne pouvez sélectionner qu\'une seule image principale.");history.back();</script>';
                        return new Response();
                    }
                }

                // 2ieme boucle pour ajouter toutes les images autres que la principale
                // dd($imagesAdd);
                foreach ($imagesAdd as $imageData) {
                    $image = new Images();
                    $imageFile = $imageData->getFile();
                    $fileName = $imageFile->getClientOriginalName();
                    // dd($imageFile); 
                    
                    if ($fileName != $mainImage) {
                        // dd($fileName);                
                        $imageFile->move($productDir, $fileName);
                        $image->setImageFileName($fileName);
                        
                        $image->setProduct($product);
                        $product->addImage($image);
                
                        
                        
                    }
                }

                $entityManager->persist($image);
                
            }
            // dd($mainImageCount);
            if ($mainImageCount === 1) {
                $entityManager->persist($product);
                $entityManager->flush();

                $this->addFlash('success', 'Produit modifié avec succès.');

                return $this->redirectToRoute('app_product_index');
            } else {
                echo '<script>alert("Vous ne pouvez sélectionner qu\'une seule image principale.");history.back();</script>';
                return new Response();
            }
        }
        

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
            'existingImages' => $existingImages,
            'imageDirectory' => $imageDirectory,
        ]);
    }

    #[Route('/remove_image/{id}', name: 'remove_image', methods: ['POST'])]
    public function removeSingleImage(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $image = $entityManager->getRepository(Images::class)->find($id);

        if (!$image) {
            return new JsonResponse(['error' => 'Image not found'], Response::HTTP_NOT_FOUND);
        }

        $product = $image->getProduct();

        if ($image) {
            if ($image->getIsMain(1)) {
                $product->setMainImage('');
            }
            // Supprime l'entité image
            $product->removeImage($image);

            // Supprime le fichier associé
            $imageFilePath = $this->getParameter('upload_directory') . '/' . $product->getSlug() . '/' . $image->getImageFileName();
            if (file_exists($imageFilePath)) {
                unlink($imageFilePath);
            }

            // Supprime l'image de la base de données
            $entityManager->remove($image);
            $entityManager->flush();
        }

        return new JsonResponse(['message' => 'Image removed successfully'], Response::HTTP_OK);
    }

    #[Route('/{id}/show', name: 'app_product_show', methods: ['GET'])]
    public function show(Request $request, Product $product): Response
    {

        return $this->render('product/show.html.twig', [
                    'product' => $product,
                ]);
    }    

    #[Route('/{id}', name: 'app_product_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, CsrfTokenManagerInterface $tokenManager, int $id, ProductRepository $pRepo, EntityManagerInterface $entityManager): Response
    {
        $product = $pRepo->find($id);
        
        $csrfToken = $tokenManager->getToken('delete' . $product->getId());

        // Vérifie si la requête est de type POST
        if ($request->isMethod('POST') && $tokenManager->isTokenValid($csrfToken)) {
            
            // Supprime les images associées au produit
            foreach ($product->getImages() as $image) {
                $entityManager->remove($image);
            }

            // Récupère le répertoire où sont stockées les images du produit
            $productDir = $this->getParameter('upload_directory') . '/' . $product->getSlug();

            // Supprime le répertoire et son contenu
            if (is_dir($productDir)) {
                // Supprime les fichiers du répertoire s'il y en a
                $objects = scandir($productDir);
                foreach ($objects as $object) {
                    if ($object != "." && $object != "..") {                        
                        $filePath = $productDir . '/' . $object;

                        if (file_exists($filePath) && is_file($filePath)) {
                            unlink($filePath);
                        }
                    }
                }
    
                    rmdir($productDir);
            }

            // Supprime le produit de la base de données
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_product_index');
    }
}
