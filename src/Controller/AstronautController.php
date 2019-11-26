<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AstronautController extends AbstractController
{
    /**
     * @Route("/astronaut", name="astronaut")
     */
    public function index()
    {
        return $this->render('astronaut/index.html.twig', [
            'controller_name' => 'AstronautController',
        ]);
    }
}
