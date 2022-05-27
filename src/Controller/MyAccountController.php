<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Entity\User;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security as UserSecurity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyAccountController extends AbstractController
{
    #[Route('/my-account', name: 'app_my_account')]
    #[Security('is_granted("ROLE_USER")')]
    public function index(UserSecurity $security, Request $request, UserPasswordHasherInterface $userPassWordHasher, UserRepository $userRepository): Response
    {
        /** @var User $user  */
        $user = $security->getUser();

        $formBuilder = $this->createFormBuilder([
            'artist' => $user->getArtist() ?? ""
        ]);

        $formBuilder->add('password', RepeatedType::class, [
           'type' => PasswordType::class,
           'required' => false,
           'first_options' => ["label" => "Mot de passe"],
           'second_options' => ["label" => "Répéter le mot de passe"],
        ])
        ->add('artist', TextType::class, [
            "label" => "Nom d'artiste",
            "required" => false
        ])
        ->add('submit', SubmitType::class, [
            'label' => "Mettre à jour"
        ]);

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if (!empty($data['password'])) {
                $user->setPassword($userPassWordHasher->hashPassword($user, $data['password']));
            }

            if (!empty($data['artist']) && $user->getArtist() === null) {
                $artist = new Artist();
                $artist->setName($data['artist']);
                $user->setArtist($artist);

            }

            $userRepository->flush();
        }

        return $this->render('my_account/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
