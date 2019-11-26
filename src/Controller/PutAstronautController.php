<?php

namespace App\Controller;

use App\Entity\Astronaut;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\Controller\AbstractFOSRestController;


class PutAstronautController extends AbstractFOSRestController
{
    protected $manager;

    protected $request;

    public function __construct(ObjectManager $manager, Request $request)
    {
        $this->manager = $manager;
        $this->request = $request;
    }

    /**
     * @FOSRest\Put("/api/astronaut/{id}")
     *
     * @param $id
     *
     * @return Response
     */
    public function updateAstronautAction($id)
    {
        $astronautRepository = $this->manager->getRepository(Astronaut::class);
        $existingAstronaut = $astronautRepository->find($id);
        if (!$existingAstronaut instanceof Astronaut) {
            return $this->json([
                'success' => false,
                'error' => 'Astronaut not found'
            ], Response::HTTP_NOT_FOUND);
        } else {
            $existingAstronaut->setName($this->request->get('name'));
            $this->manager->persist($existingAstronaut);
            $this->manager->flush();
            return $this->json($existingAstronaut, Response::HTTP_CREATED);
        }
    }
}