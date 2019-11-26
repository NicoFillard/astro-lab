<?php

namespace App\Controller;

use App\Entity\Astronaut;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\Controller\AbstractFOSRestController;

class DeleteAstronautController extends AbstractFOSRestController
{
    protected $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @FOSRest\Delete("api/astronaut/{id}")
     *
     * @param $id
     *
     * @return Response
     */
    public function deleteAstronautAction($id)
    {
        $astronautRepository = $this->manager->getRepository(Astronaut::class);
        $astronaut = $astronautRepository->find($id);
        if ($astronaut instanceof Astronaut) {
            $this->manager->remove($astronaut);
            $this->manager->flush();
            return $this->json([
                'success' => true,
            ], Response::HTTP_OK);
        } else {
            return $this->json([
                'success' => false,
                'error' => 'Astronaut not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
