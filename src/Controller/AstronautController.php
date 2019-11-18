<?php

namespace App\Controller;

use App\Entity\Astronaut;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class AstronautController extends AbstractFOSRestController
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

    /**
     * @FOSRest\Get("api/astronauts")
     *
     * @param ObjectManager $manager
     *
     * @return Response
     */
    public function getAstronautsAction(ObjectManager $manager)
    {
        $astronautRepository = $manager->getRepository(Astronaut::class);
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

    /**
     * @FOSRest\Get("api/astronaut/{id}")
     *
     * @param ObjectManager $manager
     * @param $id
     *
     * @return Response
     */
    public function getAstronautAction(ObjectManager $manager, $id)
    {
        $astronautRepository = $manager->getRepository(Astronaut::class);
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

    /**
     * @FOSRest\Post("api/astronauts")
     *
     * @param ObjectManager $manager
     *
     * @param Request $request
     * @return Response
     */
    public function postAstronautAction(ObjectManager $manager, Request $request)
    {
        $astronaut = new Astronaut();
        $astronaut->setName($request->get('name'));
        $manager->persist($astronaut);
        $manager->flush();

        return $this->json($astronaut, Response::HTTP_CREATED);
    }

    /**
     * @FOSRest\Delete("api/astronaut/{id}")
     *
     * @param ObjectManager $manager
     * @param $id
     *
     * @return Response
     */
    public function deleteAstronautAction(ObjectManager $manager, $id)
    {
        $astronautRepository = $manager->getRepository(Astronaut::class);
        $astronaut = $astronautRepository->find($id);
        if ($astronaut instanceof Astronaut) {
            $manager->remove($astronaut);
            $manager->flush();
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

    /**
     * @FOSRest\Put("/api/astronaut/{id}")
     *
     * @param Request $request
     * @param int $id
     * @param ObjectManager $manager
     *
     * @return Response
     */
    public function updateArticleAction(Request $request, int $id, ObjectManager $manager)
    {
        $astronautRepository = $manager->getRepository(Astronaut::class);
        $existingAstronaut = $astronautRepository->find($id);
        if (!$existingAstronaut instanceof Astronaut) {
            return $this->json([
                'success' => false,
                'error' => 'Astronaut not found'
            ], Response::HTTP_NOT_FOUND);
        } else {
            $existingAstronaut->setName($request->get('name'));
            $manager->persist($existingAstronaut);
            $manager->flush();
            return $this->json($existingAstronaut, Response::HTTP_CREATED);
        }
    }

}
