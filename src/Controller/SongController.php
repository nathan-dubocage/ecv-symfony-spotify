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
    public function index(SongRepository $songRepository, CoreSecurity $coreSecurity,): Response
    {
        $user = $coreSecurity->getUser();
        !empty($user) ? $artist = $user->getArtist() : $artist = null;


        $songs = $songRepository->findAll();

        foreach($songs as $song) {
            $songArtists = $song->getArtists();
            $isOwner = false;

            if($artist) {
                foreach($songArtists as $songArtist) {
                    $isOwner = $songArtist === $artist ? true : false;
                }
            }

            $song->isOwner = $isOwner;
        }

        return $this->render('song/index.html.twig', [
            'songs' => $songs,
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

            $file = $form->get('formFile')->getData();
            $newFilename = $song->getName().'.'.$file->guessExtension();

            if ($file->isValid()) {
                $stream = fopen($file->getRealPath(), "r+");
                $defaultStorage->writeStream('songFiles/'.$newFilename, $stream);
                fclose($stream);
                $song->setFile($newFilename);
            }

            $image = $form->get('imageFile')->getData();
            $newImageName = $song->getName().'.'.$image->guessExtension();

            if ($image->isValid()) {
                $stream = fopen($image->getRealPath(), "r+");
                $defaultStorage->writeStream('songCover/'.$newImageName, $stream);
                fclose($stream);
                $song->setImage($newImageName);
            }

            $songRepository->add($song, true);

            return $this->redirectToRoute('app_song_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('song/new.html.twig', [
            'song' => $song,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_song_show', methods: ['GET'])]
    public function show(Song $song, CoreSecurity $coreSecurity,): Response
    {
        $user = $coreSecurity->getUser();
        $artist = $user?->getArtist() ?? null;
        $isAllowedToDelete = $song->getArtists()->contains($artist);

        return $this->render('song/show.html.twig', [
            'song' => $song,
            'isAllowedToDelete' => $isAllowedToDelete
        ]);
    }

    #[Route('/{id}/edit', name: 'app_song_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Song $song, SongRepository $songRepository, CoreSecurity $coreSecurity): Response
    {
        $user = $coreSecurity->getUser();
        !empty($user) ? $artist = $user->getArtist() : $artist = null;
        $isAllowedToEdit = false;

        if ($artist) {
            $artistId = $artist->getId();
            $artistsInSong = $song->getArtists();


            foreach ($artistsInSong as $artistInSong) {
                if ($artistInSong->getId() === $artistId) {
                    $isAllowedToEdit = true;
                }
            }
        }

        if (!$isAllowedToEdit) {
            return $this->redirectToRoute('app_song_index', [], Response::HTTP_SEE_OTHER);
        }

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
