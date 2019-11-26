<?php

namespace App\Controller;

use App\Entity\Astronaut;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\Controller\AbstractFOSRestController;

class PostAstronautController extends AbstractFOSRestController
{
    protected $manager;

    protected $request;

    protected $astronaut;

    public function __construct(ObjectManager $manager, Request $request, Astronaut $astronaut)
    {
        $this->manager = $manager;
        $this->request = $request;
        $this->astronaut = $astronaut;
    }

    /**
     * @FOSRest\Post("api/astronaut")
     *
     * @return Response
     */
    public function postAstronautAction()
    {
        $astronaut = new Astronaut();
        $this->astronaut->setName($this->request->get('name'));
        $this->manager->persist($astronaut);
        $this->manager->flush();

        return $this->json($astronaut, Response::HTTP_CREATED);
    }
}
