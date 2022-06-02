<?php

namespace App\Controller;

use App\Entity\Song;
use App\Form\SongType;
use App\Repository\SongRepository;
use League\Flysystem\FilesystemOperator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Security as CoreSecurity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/songs')]
class SongController extends AbstractController
{
    #[Route('/', name: 'app_song_index', methods: ['GET'])]
    public function index(SongRepository $songRepository): Response
    {
        return $this->render('song/index.html.twig', [
            'songs' => $songRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_song_new', methods: ['GET', 'POST'])]
    #[Security('is_granted("IsArtist")')]
    public function new(Request $request, SongRepository $songRepository, CoreSecurity $coreSecurity, FilesystemOperator $defaultStorage): Response
    {
        $song = new Song();
        $form = $this->createForm(SongType::class, $song);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $coreSecurity->getUser();
            $artist = $user->getArtist();
            $song->addArtist($artist);
            $songRepository->add($song, true);

            // $defaultStorage

            return $this->redirectToRoute('app_song_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('song/new.html.twig', [
            'song' => $song,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_song_show', methods: ['GET'])]
    public function show(Song $song): Response
    {
        return $this->render('song/show.html.twig', [
            'song' => $song,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_song_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Song $song, SongRepository $songRepository): Response
    {
        $form = $this->createForm(SongType::class, $song);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $songRepository->add($song, true);

            return $this->redirectToRoute('app_song_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('song/edit.html.twig', [
            'song' => $song,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_song_delete', methods: ['POST'])]
    public function delete(Request $request, Song $song, SongRepository $songRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$song->getId(), $request->request->get('_token'))) {
            $songRepository->remove($song, true);
        }

        return $this->redirectToRoute('app_song_index', [], Response::HTTP_SEE_OTHER);
    }
}
