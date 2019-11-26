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

class GetAstronautsController extends AbstractFOSRestController
{
    protected $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @FOSRest\Get("api/astronauts")
     *
     * @return Response
     */
    public function getAstronautsAction()
    {
        $astronautRepository = $this->manager->getRepository(Astronaut::class);
        $astronauts = $astronautRepository->findAll();
        $encoders = [new JsonEncoder()]; // If no need for XmlEncoder
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonObject = $serializer->serialize($astronauts, 'json', [
            'circular_reference_handler' => function ($astronauts) {
                return $astronauts;
            }
        ]);
        return new Response($jsonObject, 200, ['Content-Type' => 'application/json']);
    }
}
