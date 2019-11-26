<?php

namespace App\Controller;

use App\Entity\Astronaut;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class GetAstronautByIdController extends AbstractFOSRestController
{
    protected $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @FOSRest\Get("api/astronaut/{id}")
     * @param $id
     *
     * @return Response
     */
    public function getAstronautAction($id)
    {
        $astronautRepository = $this->manager->getRepository(Astronaut::class);
        $astronaut = $astronautRepository->find($id);
        if (!$astronaut instanceof Astronaut) {
            return $this->json([
                'success' => false,
                'error' => 'Astronaut not found'
            ], Response::HTTP_NOT_FOUND);
        }
        $encoders = [new JsonEncoder()]; // If no need for XmlEncoder
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonObject = $serializer->serialize($astronaut, 'json', [
            'circular_reference_handler' => function ($astronaut) {
                return $astronaut->getId();
            }
        ]);
        return new Response($jsonObject, 200, ['Content-Type' => 'application/json']);
    }
}
