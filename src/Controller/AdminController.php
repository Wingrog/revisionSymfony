<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminController extends AbstractController
{

    // POUR AJOUTER AVEC UNE IMAGE EN UPLOAD

    /**
     * @Route("/admin/add", name="admin_add")
     */
    public function addUser(Request $request, SluggerInterface $slugger, UserPasswordEncoderInterface $encoder)
    {
        $form = $this->createForm(AdminType::class, new User());

        // Ici nous traitons notre requête
        $form->handleRequest($request);

        $user = $form->getData();

        if ($form->isSubmitted() && $form->isValid()) {

            //ENCODAGE DU MOT DE PASS
            $user->setPassword(
                $encoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            //TRAITEMENT DE L'UPLOAD DE L'IMAGE

            /** @var UploadedFile $image */
            $image = $form->get('photo')->getData();

            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

                try {
                    $image->move(
                        'images/upload',
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $user->setPhoto($newFilename);
            }
            // FIN DU TRAITEMENT DE L'UPLOAD  DE L'IMAGE


            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('admin');
        } else {
            return $this->render('admin/admin-addForm.html.twig', [
                'form' => $form->createView(),
                // 'errors' => $form->getErrors()
            ]);
        }
    }


    /**
     * @Route("/admin", name="admin")
     */
    public function index(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'users' => $users
        ]);
    }




    // POUR MODIFIER DANS LA BASE DE DONNEE AVEC UNE IMAGE EN UPLOAD

    /**
     * @Route("/admin/update/{user}", name="update_admin")
     */
    public function update(User $user, Request $request, SluggerInterface $slugger)
    {
        $form = $this->createForm(AdminType::class, $user);

        // Ici nous traitons notre requête
        $form->handleRequest($request);

        $user = $form->getData();

        if ($form->isSubmitted() && $form->isValid()) {

            //TRAITEMENT DE L'UPLOAD DE L'IMAGE

            /** @var UploadedFile $image */
            $image = $form->get('photo')->getData();

            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

                try {
                    $image->move(
                        'images/upload',
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $user->setPhoto($newFilename);
            }


            // FIN DU TRAITEMENT DE L'UPLOAD DE L'IMAGE


            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('admin');
        } else {
            return $this->render('admin/admin-editForm.html.twig', [
                'form' => $form->createView(),
                'errors' => $form->getErrors(),
                'user' => $user
            ]);
        }
    }


    // POUR SUPPRIMER DANS LA BASE DE DONNEE

    /**
     * @Route("/admin/delete/{user}", name="delete_user")
     */
    public function delete(User $user)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->remove($user);
        $entityManager->flush();
        return $this->redirectToRoute('admin');
    }
}
