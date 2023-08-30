<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\CreatePinType;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PinsController extends AbstractController
{

    /**
     * @var PinRepository
     */
    private $pinRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(PinRepository $pinRepository, EntityManagerInterface $entityManager)
    {
        $this->pinRepository = $pinRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="app_home", methods="GET")
     */
    public function index(): Response
    {
        $pins = $this->pinRepository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('home/index.html.twig', compact('pins'));
    }

    /**
     * @Route("/pin/create", name="app_pin_create", methods={"GET","POST"})
     */
    public function create(Request $request): Response
    {
        $pin = new Pin;
        $form = $this->createForm(CreatePinType::class, $pin);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($pin);
            $this->entityManager->flush();
            $this->addFlash('success', 'Pin created successfully !');
            return $this->redirectToRoute('app_home');
        }
        return $this->render('home/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/pin/{id}", name="app_pin_show", methods="GET")
     */
    public function show(int $id): Response
    {
        $pin = $this->pinRepository->find($id);
        return $this->render('home/show.html.twig', compact('pin'));
    }

    /**
     * @Route("/pin/{id}/edit", name="app_pin_edit", methods={"GET","POST"})
     */
    public function edit(int $id, Request $request): Response
    {
        $pin = $this->pinRepository->find($id);
        $form = $this->createForm(CreatePinType::class, $pin);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Pin edited successfully !');
            return $this->redirectToRoute('app_pin_show', compact('id'));
        }
        return $this->render('home/edit.html.twig', ['pin' => $pin, 'form' => $form->createView()]);
    }

    /**
     * @Route("/pin/{id}/delete", name="app_pin_delete", methods="DELETE")
     */
    public function delete(Request $request, int $id): Response
    {
        $pin = $this->pinRepository->find($id);
        if ($this->isCsrfTokenValid('pin_delete_'.$pin->getId(), $request->request->get('csrf_token'))) {
            $this->entityManager->remove($pin);
            $this->entityManager->flush();
            $this->addFlash('error', 'Pin deleted successfully');
        }
        return $this->redirectToRoute('app_home');
    }
}
