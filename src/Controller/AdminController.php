<?php

namespace App\Controller;

use App\Form\UserRolesType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\UserType;



class AdminController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @Route("/admin", name="app_admin")
     */
    public function index(): Response
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();
        return $this->render('admin/index.html.twig', [
            'users' => $users,
        ]);
    }

     /**
     * @Route("/admin/listusers", name="app_list_users")
     * @IsGranted("ROLE_ADMIN")
     */
    public function listuser(): Response
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();
        return $this->render('admin/listusers.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/admin/users", name="admin_users")
     * @IsGranted("ROLE_ADMIN")
     */
    public function manageUsers()
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();

        return $this->render('admin/manage_users.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/admin/users/{id}/edit-roles", name="admin_edit_roles")
     * @IsGranted("ROLE_ADMIN")
     */
    public function editRoles(Request $request, User $user)
    {
        // Créez un formulaire pour modifier les rôles de l'utilisateur
        $form = $this->createForm(UserRolesType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mettez à jour les rôles de l'utilisateur
            $em = $this->entityManager;
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Rôles mis à jour avec succès.');

            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/edit_roles.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }



    /**
     * @Route("/admin/manage-users", name="custom_manage_users")
     * @IsGranted("ROLE_ADMIN")
     */
    public function customManageUsers(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();

        return $this->render('admin/custom_manage_users.html.twig', [
            'users' => $users,
        ]);
    }
    #[Route('/admin/{id}', name: 'app_admin_user_show', methods: ['GET'])]
    public function showAdmin(User $user): Response
    {
        return $this->render('admin/show.html.twig', [
            'user' => $user,
        ]);
    }
    
    #[Route('/admin/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    public function editAdmin(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
    
            return $this->redirectToRoute('app_admin_user_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('admin/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }


    #[Route('/admin/{id}/delete', name: 'app_admin_user_delete', methods: ['POST'])]
public function deleteAdmin(Request $request, User $user, EntityManagerInterface $entityManager): Response
{
    if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
        $entityManager->remove($user);
        $entityManager->flush();
    }

    return $this->redirectToRoute('app_list_users', [], Response::HTTP_SEE_OTHER);
}



#[Route('/search_users', name: 'search_users')]
public function searchUsers(Request $request, EntityManagerInterface $entityManager): Response
{
    // Retrieve search parameters from the request
    $searchQuery = $request->query->get('search_query');
    $searchParameters = [
        'prenom' => $request->query->get('prenom'),
        'nom' => $request->query->get('nom'),
        'email' => $request->query->get('email'),
        // Add more parameters as needed
    ];

    // Retrieve sorting parameters from the request
    $sortField = $request->query->get('sort_field', 'id'); // Default to sorting by 'id'
    $sortOrder = $request->query->get('sort_order', 'asc'); // Default to ascending order

    $queryBuilder = $entityManager->getRepository(User::class)
        ->createQueryBuilder('u');

    // Add conditions for each search parameter
    foreach ($searchParameters as $field => $value) {
        if ($value !== null) {
            if ($field === 'email') {
                // Exclude domain from email search
                $queryBuilder->andWhere("SUBSTRING(u.$field, 1, LENGTH(u.$field) - LENGTH(:domain)) LIKE :$field")
                    ->setParameter('domain', strlen('@gmail.com'));
            } else {
                $queryBuilder->andWhere("u.$field LIKE :$field")
                    ->setParameter($field, '%' . $value . '%');
            }
        }
    }

    // Add a global search condition if a general query is provided
    if ($searchQuery !== null) {
        $queryBuilder->andWhere('SUBSTRING(u.email, 1, LENGTH(u.email) - LENGTH(:domain)) LIKE :query OR u.prenom LIKE :query OR u.nom LIKE :query')
            ->setParameter('domain', strlen('@gmail.com'))
            ->setParameter('query', '%' . $searchQuery . '%');
    }

    // Add sorting conditions
    $queryBuilder->orderBy("u.$sortField", $sortOrder);

    $users = $queryBuilder->getQuery()->getResult();

    return $this->render('user/index.html.twig', [
        'users' => $users,
    ]);
}




}