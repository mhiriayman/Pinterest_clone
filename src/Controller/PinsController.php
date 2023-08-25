<?php

namespace App\Controller;

use App\Repository\PinRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PinsController extends AbstractController
{

    /**
     * @var PinRepository
     */
    private $pinRepository;

    public function __construct(PinRepository $pinRepository)
    {
        $this->pinRepository = $pinRepository;
    }

    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {
        $pins = $this->pinRepository->findBy([],['createdAt'=>'DESC']);
        return $this->render('home/index.html.twig', compact('pins'));
    }

    /**
     * @Route("/pin/{id}", name="app_pin_show")
     */
    public function show(int $id): Response
    {
        $pin=$this->pinRepository->find($id);
        return $this->render('home/show.html.twig', compact('pin'));
    }
}
