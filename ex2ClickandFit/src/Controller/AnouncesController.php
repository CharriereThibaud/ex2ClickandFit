<?php

namespace App\Controller;

use App\Entity\Anounce;
use App\Form\AnounceFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnouncesController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/menu', name: 'Menu')]    
    public function index(): Response
    {
        $anounces = $this->em->getRepository(Anounce::class)->findAll();

        return $this->render('anounces/index.html.twig', ["Anounces" => $anounces]);
    }
    
    #[Route('/anounce/new', name: "Nouvelle annonce")]
    public function create(Request $request): Response
    {
        if ($this->getUser() == NULL) {
            return $this->redirectToRoute('Menu');
        }
        $anounce = new Anounce;
        $form = $this->createForm(AnounceFormType::class, $anounce);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newAnounce = $form->getData();
            $image = $form->get('Photo')->getData();
            if ($image) {
                $newImage = uniqid() . '.'  . $image->guessExtension();
                try {
                    $image->move($this->getParameter('kernel.project_dir') . '/public/uploads', $newImage);
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }

                $newAnounce->setPhoto('/uploads/' . $newImage);
            }
            $newAnounce->setPhone($this->getUser()->getPhone());
            $newAnounce->setDate(new \DateTime('@'.strtotime('now')));
            $newAnounce->setUser($this->getUser());
            
            $this->em->persist($newAnounce);
            $this->em->flush();

            return $this->redirectToRoute('Menu');
        }
    
        return $this->render('anounces/new.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/anounce/{id}/edit', name: "Edition annonce")]
    public function edit($id, Request $request): Response
    {
        if ($this->getUser() == NULL) {
            return $this->redirectToRoute('Menu');
        }
        $anounce = $this->em->getRepository(Anounce::class)->find($id);
        $form = $this->createForm(AnounceFormType::class, $anounce);

        if ($this->getUser()->getId() != $anounce->getUser()->getId()) {
            return $this->redirectToRoute('Menu');
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('Photo')->getData();
            if ($image) {
                if ($anounce->getImagePath() !== null) {
                    if (file_exists( $this->getParameter('kernel.project_dir') . $anounce->getImagePath())) {
                        $this->GetParameter('kernel.project_dir') . $anounce->getImagePath();
                    }
                    $newImage = uniqid() . '.'  . $image->guessExtension();
                    try {
                        $image->move($this->getParameter('kernel.project_dir') . '/public/uploads', $newImage);
                    } catch (FileException $e) {
                        return new Response($e->getMessage());
                    }

                    $anounce->setPhoto('/uploads/' . $newImage);
                }
            }
            $anounce->setTitle($form->get('Title')->getData());
            $anounce->setDescription($form->get('Description')->getData());
            $anounce->setPostalCode($form->get('PostalCode')->getData());
            
            $this->em->flush();
            return $this->redirectToRoute('Détail', ['id' => $id]);
        }
    
        return $this->render('anounces/edit.html.twig', ['anounce' => $anounce, 'form' => $form->createView()]);
    }

    #[Route('/anounce/{id}/delete', name: "Supprimer")]
    public function delete($id): Response
    {
        if ($this->getUser() == NULL) {
            return $this->redirectToRoute('Menu');
        }
    
        $anounce = $this->em->getRepository(Anounce::class)->find($id);

        if ($this->getUser()->getId() != $anounce->getUser()->getId()) {
            return $this->redirectToRoute('Menu');
        }

        $this->em->remove($anounce);
        $this->em->flush();

        return $this->redirectToRoute('Menu');
    }

    #[Route('/anounce/{id}', name: "Détail")]
    public function show($id): Response
    {
        $anounce = $this->em->getRepository(Anounce::class)->find($id);

        return $this->render('anounces/show.html.twig', ["Anounce" => $anounce]);
    }
}
