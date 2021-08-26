<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PersonRepository;
use Symfony\Component\Serializer\SerializerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Person;

class PersonController extends AbstractController
{
    /**
     * @Route("/api/person", name="api_person", methods="GET")
     */
    public function index(PersonRepository $personRepository,SerializerInterface $serializer): Response
    {
        $personnes = $personRepository->findAll();
      ////  $normalized = $normalizer->normalize($personnes);
        //$json = json_encode($normalized);
        $json = $serializer->serialize($personnes,'json',['groups'=>'person:read']);
        $reponse = new Response($json, 200, [
            'content-type' => 'application/json'
            ]);
            return $reponse;
    }
    /**
     * @Route("/api/person/{id}", name="api_person_avec_id", methods="GET")
     */
    public function findById(PersonRepository $personRepository,SerializerInterface $serializer,$id): Response
    {
        $person = $personRepository->find($id);
        //$normalized = $normalizer->normalize($person,null,['groups'=>'person:read']);

       // $json = json_encode($normalized);
       //$json = json_encode($person);
       $json = $serializer->serialize($person,'json',['groups'=>'person:read']);
        
        $reponse = new Response($json, 200, [
            'content-type' => 'application/json'
            ]);
            return $reponse;
    } 
/**
 * @Route("/api/person/", name="api_person_add",methods="POST")
 */
public function add(EntityManagerInterface $entityManager, Request $request, SerializerInterface $serializer, ValidatorInterface $validator) {
$contenu = $request->getContent();
try
    {
        $personne = $serializer->deserialize($contenu, Person::class, 'json');
        $errors = $validator->validate($personne);
        if (count($errors) > 0) 
        {
            return $this->json($errors, 400);
        }
            $entityManager->persist($personne);
            $entityManager->flush();
            return $this->json($personne, 201, [], ['groups' => 'person:read']);
    } 
catch (NotEncodableValueException $e) 
    {
        return $this->json(['status' => 400,'message' => $e->getMessage()]);
    }
}
}