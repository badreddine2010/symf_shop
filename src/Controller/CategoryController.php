<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
// use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="app_category")
     *  
     */
    public function index(CategoryRepository $categoryRepo): Response
    {
        // if (!$this->isGranted('ROLE_ADMIN')) {
		// 	return $this->render('/error/accessDenied.html.twig');
		// }
        try {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
    
           
            $categories = $categoryRepo->findAll();
            // dd($categories);
            return $this->render('category/index.html.twig', [
                'controller_name' => 'CategoryController',
                'categories'=>$categories
            ]);
        }
        catch(AccessDeniedException $ex){
    
            // Retour 2 : Envoyer une alerte qui pourra être traitée par Twig
            $this->addFlash('error', "Vous n'avez pas les droits necessaires pour accèder à cette fonction");
            return $this->redirectToRoute('home');
        }
    }
    
    /**
     * @Route("/category/new",name="cat-new")
     * @Route("/category/edit/{id}",name="cat-edit")
     */
    public function addOrUpdateCategory(
        Category $category = null,
        Request $req,
        EntityManagerInterface $em
        ) {
            if (!$this->isGranted('ROLE_ADMIN')) {
                return $this->render('/error/accessDenied.html.twig');
            }
            if (!$category) {
                $category = new Category();
            }
            
            $formCategory = $this->createForm(CategoryType::class, $category);
            
            $formCategory->handleRequest($req);
            // dump($req);
            // dump($article);
            if ($formCategory->isSubmitted() && $formCategory->isValid()) {
                $em->persist($category);
                $em->flush();
                return $this->redirectToRoute('app_category', [
                    'id' => $category->getId(),
                ]);
            }
            
            return $this->render('category/catForm.html.twig', [
                'formCategory' => $formCategory->createView(),
                'mode' => $category->getId() != null,
            ]);
        }

        /**
     * @Route("/category/delete/{id}",name="cat-delete")
     */
    public function delete(ManagerRegistry $doctrine, int $id): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->render('/error/accessDenied.html.twig');
		}
        $entityManager = $doctrine->getManager();
        $category = $entityManager->getRepository(Category::class)->find($id);
        
        if (!$category) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $entityManager->remove($category);
        $entityManager->flush();
        
        return $this->redirectToRoute('app_category', [
            'id' => $category->getId()
        ]);
    }
    //     /**
    //     * @Route("/category/{id}", name="detail_category")
    //     */
    //    public function show($id,CategoryRepository $categoryRepo): Response
    //    {
    //        $category = $categoryRepo->find($id);
    //        // dd($categories);
    //        return $this->render('category/catDetail.html.twig', [
    //            'controller_name' => 'CategoryController',
    //            'category'=>$category
    //        ]);
    //    }
}
