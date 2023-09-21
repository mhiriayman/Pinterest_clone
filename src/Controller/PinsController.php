<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\CreatePinType;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
     * @IsGranted("PIN_CREATE")
     */
    public function create(Request $request): Response
    {
        $pin = new Pin;
        $form = $this->createForm(CreatePinType::class, $pin);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pin->setUser($this->getUser());
            $this->entityManager->persist($pin);
            $this->entityManager->flush();
            $this->addFlash('success', 'Pin created successfully !');
            return $this->redirectToRoute('app_home');
        }
        return $this->render('home/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/pin/{id<[0-9]+>}", name="app_pin_show", methods="GET")
     */
    public function show(Pin $pin): Response
    {
        return $this->render('home/show.html.twig', compact('pin'));
    }

    /**
     * @Route("/pin/{id<[0-9]+>}/edit", name="app_pin_edit", methods={"GET","PUT"})
     * @IsGranted("PIN_MANAGE", subject="pin")
     */
    public function edit(Pin $pin, Request $request): Response
    {
        $form = $this->createForm(CreatePinType::class, $pin, [
            'method'=>'PUT'
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Pin Successfully Updated!');
            return $this->redirectToRoute('app_pin_show', ['id'=>$pin->getId()]);
        }
        return $this->render('home/edit.html.twig', ['pin' => $pin, 'form' => $form->createView()]);
    }

    /**
     * @Route("/pin/{id}", name="app_pin_delete", methods="DELETE")
     * @IsGranted("PIN_MANAGE", subject="pin")
     */
    public function delete(Request $request, Pin $pin): Response
    {
        if ($this->isCsrfTokenValid('pin_delete_'.$pin->getId(), $request->request->get('csrf_token'))) {
            $this->entityManager->remove($pin);
            $this->entityManager->flush();
            $this->addFlash('error', 'Pin Successfully Deleted');
        }
        return $this->redirectToRoute('app_home');
    }
}
