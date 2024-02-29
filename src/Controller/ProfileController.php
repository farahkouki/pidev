<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{

    /**
     * @Route("/profile", name="user_profile")
     */
    public function userProfile(): Response
    {
        // Récupérer les informations de l'utilisateur à partir de la session ou de la base de données
        $user = $this->getUser();

        // Afficher la page de profil en passant les informations de l'utilisateur au template
        return $this->render('profile/profile.html.twig', [
            'user' => $user,
        ]);
    }


    /**
     * @Route("/profile/edit", name="edit_profile")
     */
    public function edit(Request $request): Response
{
    $user = $this->getUser(); // Récupérer l'utilisateur connecté

    // Récupérer l'ID de l'utilisateur connecté
    $userId = $user->getId();

    // Récupérer l'utilisateur à partir de la base de données
    $entityManager = $this->getDoctrine()->getManager();
    $user = $entityManager->getRepository(User::class)->find($userId);

    // Vérifier si l'utilisateur existe
    if (!$user) {
        throw $this->createNotFoundException('Utilisateur non trouvé.');
    }

    // Créer le formulaire d'édition du profil
    $form = $this->createForm(ProfileType::class, $user);
    $form->handleRequest($request);

    // Vérifier si le formulaire a été soumis et est valide
    if ($form->isSubmitted() && $form->isValid()) {
        // Enregistrer les modifications dans la base de données
        $entityManager->flush();

        $this->addFlash('success', 'Profil mis à jour avec succès.');

        return $this->redirectToRoute('user_profile');
    }

    return $this->render('profile/edit.html.twig', [
        'form' => $form->createView(),
    ]);
}
}