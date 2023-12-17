<?php

namespace App\Controller;

use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/contact')]
class ApiContactController extends AbstractController
{
    #[Rest\Get('/', name: 'contact_api_list')]
    public function contactApiList(EntityManagerInterface $entityManager): JsonResponse
    {
        $contacts = $entityManager->getRepository(Contact::class)->findAll();
        $contactsList = [];

        if (count($contacts) > 0) {
            foreach($contacts as $contact) {
                $contactsList[] = $contact->toArray();
            }
            $response = [
                'ok' => true,
                'contacts' => $contactsList,
            ];
        } else {
            $response = [
                'ok' => false,
                'error' => 'No contacts found',
            ];
        }

        return new JsonResponse($response);
    }

    #[Rest\Get('/{id<\d+>}', name: 'single_contact_api')]
    public function index(EntityManagerInterface $entityManager, $id=''): JsonResponse
    {
        $contact = $entityManager->getRepository(Contact::class)->find($id);
        if ($contact) {
            $contactArray = $contact->toArray();
            $response = [
                'ok' => true,
                'contact' => $contactArray,
            ];
        } else {
            $response = [
                'ok' => false,
                'error' => 'No contact found with id '.$id,
            ];
        }
        return new JsonResponse($response);
    }

    #[Rest\Post('/', name: 'contact_api_new_contact')]
    public function newContact(EntityManagerInterface $entityManager,
                               Request $request, ValidatorInterface $validator): JsonResponse {

        try {
            $content = $request->getContent();
            $contact = new Contact();
            $contact->fromJson($content);
            $errors = $validator->validate($contact);

            if(count($errors) == 0)
            {
                $entityManager->persist($contact);
                $entityManager->flush();

                $response = [
                    'ok' => true,
                    'message' => 'contact inserted',
                ];
            } else {
                $errorsArray = [];
                foreach ($errors as $error){
                    $errorsArray[] = array(
                        $error->getPropertyPath() => $error->getMessage()
                    );
                }
                $response = [
                    'ok' => false,
                    'message' => 'Failed to insert contact: errors in data',
                    'errors' => $errorsArray ,
                ];
            }

        } catch (\Throwable $e) {
            $response = [
                'ok' => false,
                'error' => 'Failed to insert contact: '.$e->getMessage(),
            ];
        }
        return new JsonResponse($response);
    }

    #[Rest\Put('/{id<\d+>}', name: 'contact_api_edit_contact')]
    public function editContact(EntityManagerInterface $entityManager,
                                Request $request, ValidatorInterface $validator, $id=''): JsonResponse {

        try {
            $content = $request->getContent();

            $contact = $entityManager->getRepository(Contact::class)->find($id);
            $contact->fromJson($content);
            $errors = $validator->validate($contact);

            if(count($errors) == 0)
            {
                $entityManager->flush();
                $response = [
                    'ok' => true,
                    'message' => 'contact updated',
                ];
            } else {
                $errorsArray = [];
                foreach ($errors as $error){
                    $errorsArray[] = array(
                        $error->getPropertyPath() => $error->getMessage()
                    );
                }
                $response = [
                    'ok' => false,
                    'message' => 'Failed to insert contact: errors in data',
                    'errors' => $errorsArray ,
                ];
            }

        } catch (\Throwable $e) {
            $response = [
                'ok' => false,
                'error' => 'Failed to update contact: '.$e->getMessage(),
            ];
        }
        return new JsonResponse($response);
    }

    #[Rest\Delete('/{id<\d+>}', name: 'contact_api_delete_contact')]
    public function deleteContact(EntityManagerInterface $entityManager, $id=''): JsonResponse {
        $contact = $entityManager->getRepository(Contact::class)->find($id);
        if ($contact) {
            foreach($contact->getPhones() as $phone){
                $entityManager->remove($phone);
            }
            $entityManager->remove($contact);
            $entityManager->flush();

            $response = [
                'ok' => true,
                'message' => 'contact deleted',
            ];
        } else {
            $response = [
                'ok' => false,
                'error' => 'Delete failed: contact not found',
            ];
        }
        return new JsonResponse($response);
    }
}
