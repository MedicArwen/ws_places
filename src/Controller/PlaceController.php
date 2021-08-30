<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PlaceRepository;
use App\Repository\PersonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use Symfony\Component\Validator\Validator\ValidatorInterface;

class PlaceController extends AbstractController
{
    /**
     * @Route("/api/place", name="api_place", methods="GET")
     */
    public function index(PlaceRepository $placeRepository,NormalizerInterface $normalizer): Response
    {
        $places = $placeRepository->findAll();
        $normalized = $normalizer->normalize($places,null,['groups'=>'place:read']);
        $json = json_encode($normalized);
        $reponse = new Response($json, 200, [
            'content-type' => 'application/json'
            ]);
            return $reponse;
    }
    /**
     * @Route("/api/place/{id}", name="api_place_avec_id", methods="GET")
     */
    public function findById(PlaceRepository $placeRepository, NormalizerInterface $normalizer,$id): Response
    {
        $place = $placeRepository->find($id);
        $normalized = $normalizer->normalize($place,null,['groups'=>'place:read']);

        $json = json_encode($normalized);
        $reponse = new Response($json, 200, [
            'content-type' => 'application/json'
            ]);
            return $reponse;
    } 
    /**
     * @Route("api/place/{numPlace}/liked/{numPerson}", name="api_place_add_liker",methods="POST")
     */

    public function likeIt(EntityManagerInterface $entityManager,
        PlaceRepository $placeRepository,PersonRepository $personRepository,$numPlace,$numPerson) 
        {
            $place = $placeRepository->find($numPlace);
            $personne = $personRepository->find($numPerson);
            $place->addLikedBy($personne);
            $entityManager->flush();
            return $this->json($place, 201, [], ['groups' => 'place:read']);
        }

    /*
    public function beLiked(EntityManagerInterface $entityManager,PlaceRepository $placeRepository, PersonRepository $personRepository, 
     Request $request,$id,$numPerson) {
        $contenu = $request->getContent();
        try
            {
                
                    $place = $placeRepository->find($id);
                    $personne = $personRepository->find($numPerson);
                    $place->addLikedBy($personne);
                    $entityManager->flush();
                    return $this->json($place, 201, [], ['groups' => 'place:read']);
            } 
        catch (NotEncodableValueException $e) 
            {
                return $this->json(['status' => 400,'message' => $e->getMessage()]);
            }
        }*/
}