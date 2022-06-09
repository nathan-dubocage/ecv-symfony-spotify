<?php

namespace App\Controller;

use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Repository\PlaylistRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security as CoreSecurity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/playlist')]
class PlaylistController extends AbstractController
{
    #[Route('/', name: 'app_playlist_index', methods: ['GET'])]
    public function index(PlaylistRepository $playlistRepository, CoreSecurity $coreSecurity): Response
    {
        $user = $coreSecurity->getUser();
        !empty($user) ? $artist = $user->getArtist() : $artist = null;

        $playlists = $playlistRepository->findAll();
        $isAllowedToEdit = false;

        foreach ($playlists as $playlist) {
            $userId = $user;
            $playlistOwnerId = $playlist->getUser();

            if ($userId === $playlistOwnerId) {
                $isAllowedToEdit = true;
            }

            $playlist->isOwner = $isAllowedToEdit;
        }


        return $this->render('playlist/index.html.twig', [
            'playlists' => $playlistRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_playlist_new', methods: ['GET', 'POST'])]
    #[Security('is_granted("ROLE_USER")')]
    public function new(Request $request, PlaylistRepository $playlistRepository, CoreSecurity $coreSecurity): Response
    {
        $playlist = new Playlist();
        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $coreSecurity->getUser();
            $playlist->setUser($user);

            $playlistRepository->add($playlist, true);
            return $this->redirectToRoute('app_playlist_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('playlist/new.html.twig', [
            'playlist' => $playlist,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_playlist_show', methods: ['GET'])]
    public function show(Playlist $playlist, CoreSecurity $coreSecurity): Response
    {
        $user = $coreSecurity->getUser();
        $playlistOwnerId = $playlist->getUser()->getId();
        !empty($user) ? $artist = $user->getArtist() : $artist = null;
        $isAllowedToEdit = false;
        $userId = $user;

        if ($userId === $playlistOwnerId) {
            $isAllowedToEdit = true;
        }

        return $this->render('playlist/show.html.twig', [
            'playlist' => $playlist,
            'songs' => $playlist->getSongs(),
            'isAllowedToEdit' => $isAllowedToEdit
        ]);
    }

    #[Route('/{id}/edit', name: 'app_playlist_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Playlist $playlist, PlaylistRepository $playlistRepository, CoreSecurity $coreSecurity): Response
    {
        $user = $coreSecurity->getUser();
        !empty($user) ? $artist = $user->getArtist() : $artist = null;
        $playlists = $playlistRepository->findAll();
        $isAllowedToEdit = false;

        foreach ($playlists as $playlist) {
            $userId = $user;
            $playlistOwnerId = $playlist->getUser();

            if ($userId === $playlistOwnerId) {
                $isAllowedToEdit = true;
            }
        }

        if (!$isAllowedToEdit) {
            return $this->redirectToRoute('app_playlist_index', [], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $playlistRepository->add($playlist, true);

            return $this->redirectToRoute('app_playlist_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('playlist/edit.html.twig', [
            'playlist' => $playlist,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_playlist_delete', methods: ['POST'])]
    public function delete(Request $request, Playlist $playlist, PlaylistRepository $playlistRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$playlist->getId(), $request->request->get('_token'))) {
            $playlistRepository->remove($playlist, true);
        }

        return $this->redirectToRoute('app_playlist_index', [], Response::HTTP_SEE_OTHER);
    }
}
