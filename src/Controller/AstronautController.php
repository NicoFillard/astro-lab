<?php

namespace App\Controller;

use App\Entity\Astronaut;
use App\Form\AstronautType;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AstronautController extends FOSRestController
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
                'error' => 'Article not found'
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
     * @ParamConverter("astronaut", converter="fos_rest.request_body")
     *
     * @param ObjectManager $manager
     * @param Astronaut $astronaut
     * @param ValidatorInterface $validator
     *
     * @return Response
     */
    public function postAstronautAction(ObjectManager $manager, Astronaut $astronaut, ValidatorInterface $validator)
    {
        $errors = $validator->validate($astronaut);
        if (!count($errors)) {
            $astronaut->setName("New astronaut");
            $manager->persist($astronaut);
            $manager->flush();
            return $this->json($astronaut, Response::HTTP_CREATED);
        } else {
            return $this->json([
                'success' => false,
                'error' => $errors[0]->getMessage().' ('.$errors->getPropertyPath().')'
            ], Response::HTTP_BAD_REQUEST);
        }
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
                'error' => 'Article not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }
    /**
     * @FOSRest\Put("/api/astronaut/{id}")
     *
     * @param Request $request
     * @param int $id
     * @param ObjectManager $manager
     * @param ValidatorInterface $validator
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateArticleAction(Request $request, int $id, ObjectManager $manager, ValidatorInterface $validator)
    {
        $astronautRepository = $manager->getRepository(Astronaut::class);
        $existingAstronaut   = $astronautRepository->find($id);
        if (!$existingAstronaut instanceof Astronaut) {
            return $this->json([
                'success' => false,
                'error'   => 'Article not found'
            ], Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm(AstronautType::class, $existingAstronaut);
        $form->submit($request->request->all());
        $errors = $validator->validate($existingAstronaut);
        if (!count($errors)) {
            $manager->persist($existingAstronaut);
            $manager->flush();
            return $this->json($existingAstronaut, Response::HTTP_CREATED);
        } else {
            return $this->json([
                'success' => false,
                'error'   => $errors[0]->getMessage() . ' (' . $errors[0]->getPropertyPath() . ')'
            ], Response::HTTP_BAD_REQUEST);
        }
    }

}
