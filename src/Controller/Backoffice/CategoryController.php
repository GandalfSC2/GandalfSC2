<?php

namespace App\Controller\Backoffice;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backoffice/category", name="backoffice_category_", requirements={"id": "\d+"})
 */
class CategoryController extends AbstractController
{
    /**
     * Affiche toutes les catégories depuis l'Admin
     * 
     * URL : /backoffice/category/
     * Route : backoffice_category_index
     * 
     * @Route("/", name="index")
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('backoffice/category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * Page permettant la création d'une catégorie
     * 
     * @Route("/add", name="add")
     *
     * @return Response
     */
    public function add(Request $request)
    {
        // Etape 1 : on instancie l'entité Category
        $category = new Category();

        // Etape 2 : on instancie le form type
        // que l'on associe à l'objet $category
        $form = $this->createForm(CategoryType::class, $category);

        // Etape 4 : on intercepte les données du formulaire
        // que l'on injecte dans $category
        $form->handleRequest($request);

        // Etape 5 : on vérifie la validité des données du formulaire
        // avant de les sauvegarder
        if ($form->isSubmitted() && $form->isValid()) {
            // On sauvegarde les données
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'La catégorie ' . $category->getName() . ' a bien été créée');

            return $this->redirectToRoute('backoffice_category_index');
        }

        // Etape 3 : on transmet tout le nécessaire pour générer le
        // formulaire HTML
        return $this->render('backoffice/category/add.html.twig', [
            'formView' => $form->createView()
        ]);
    }

    /**
     * Permet l'affichage des détails d'une catégorie
     * 
     * URL : /backoffice/category/{id}
     * Route : backoffice_category_show
     * 
     * @Route("/{id}", name="show")
     *
     * @return Response
     */
    // public function show(int $id, CategoryRepository $categoryRepository)
    // {
    // Version 1 : On récupère la catégory en appellant directement le repository
    //     $category = $categoryRepository->find($id);

    //     dd($category);
    // }
    // Version 2 : Param converter ==> Conversion automatique de paramètres
    public function show(int $id, CategoryRepository $categoryRepository)
    {
        $category = $categoryRepository->find($id);

        if (!$category) {
            throw $this->createNotFoundException('La catégorie ' . $id . ' n\'existe pas');
        }

        // Category $category ==> $category = $categoryRepository->find($id)
        return $this->render('backoffice/category/show.html.twig', [
            'category' => $category
        ]);
    }

    /**
     * Page permettant l'édition d'une catégorie
     * 
     * URL : /backoffice/category/{id}/edit
     * 
     * @Route("/{id}/edit", name="edit")
     *
     * @return Response
     */
    public function edit(Category $category, Request $request)
    {
        $form = $this->createForm(CategoryType::class, $category);

        // On intercepte les données soumises
        // que l'on injecte dans $category
        $form->handleRequest($request);

        // On vérifie la validité des données
        // avant sauvegarde
        if ($form->isSubmitted() && $form->isValid()) {
            // On met à jour la catégorie en BDD
            // pas besoin de persist, puisque Doctrine connait déjà $category
            $category->setUpdatedAt(new DateTimeImmutable());
            $this->getDoctrine()->getManager()->flush();

            // Message de succès
            $this->addFlash('success', 'La catégorie ' . $category->getName() . ' a bien été modifiée');

            return $this->redirectToRoute('backoffice_category_show', ['id' => $category->getId()]);
        }

        return $this->render('backoffice/category/edit.html.twig', [
            'formView' => $form->createView()
        ]);
    }

    /**
     * Action permettant la suppression d'une catégorie
     *
     * URL : /backoffice/category/{id}/delete
     * Route : backoffice_category_delete
     * 
     * @Route("/{id}/delete", name="delete")
     * 
     * @return Response
     */
    public function delete(Category $category)
    {
        // On supprime la catégorie en BDD
        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();

        // Message flash
        $this->addFlash('info', 'La catégorie ' . $category->getName() . ' a bien été supprimée');

        return $this->redirectToRoute('backoffice_category_index');
    }
}
