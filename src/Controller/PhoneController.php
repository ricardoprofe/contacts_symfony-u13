<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Phone;
use App\Entity\Contact;
use App\Repository\PhoneRepository;
use App\Repository\ContactRepository;

class PhoneController extends AbstractController
{
    #[Route('/phone/test/new/{id<\d+>}', name: 'new_phone')]
    public function newPhone(EntityManagerInterface $entityManager, $id=''): Response
    {
        $contact = $entityManager->getRepository(Contact::class)->find($id);
        if($contact == null) {
            return $this->render('phone/new_edit_phone.html.twig', [
                'contact'=> $contact,
                'phone' => null,
                'action' => 'Contact not found',
                'page_title' => 'My Contacts App - New phone',
            ]);
        } else {
            $phone = new Phone();
            $phone->setIdContact($contact);
            $phone->setNumber("656565611");
            $phone->setType("Mobile");

            $entityManager->persist($phone);
            $entityManager->flush();

            $action = ($phone ? 'New phone added' : 'Failed to add phone');

            return $this->render('phone/new_edit_phone.html.twig', [
                'phone' => $phone,
                'contact' => $contact,
                'action' => $action,
                'page_title' => 'My Contacts App - New phone',
            ]);
        }
    }

    #[Route('/phone/test/edit/{id<\d+>}/{number}', name: 'phone_edit')]
    public function updatePhone(EntityManagerInterface $entityManager, $id='', $number=''): Response
    {
        $contact = $entityManager->getRepository(Contact::class)->find($id);
        if($contact == null) {
            return $this->render('phone/new_edit_phone.html.twig', [
                'contact'=> $contact,
                'phone' => null,
                'page_title' => 'My Contacts App - New phone',
                'action' => 'Failed to modify phone: no contact found'
            ]);
        } else {
            $phone = $entityManager->getRepository(Phone::class)
                ->findOneBy(['number'=>$number, 'id_contact'=>$id]);
            if($phone) {
                $phone->setNumber("686868611");
                $entityManager->flush();
                $action = "Phone updated";
            } else {
                $action = "Failed to modify phone";
            }

            return $this->render('phone/new_edit_phone.html.twig', [
                'phone' => $phone,
                'contact' => $contact,
                'page_title' => 'My Contacts App - Update phone',
                'action' => $action
            ]);
        }
    }

    #[Route('/phone/test/delete/{id<\d+>}/{number}', name: 'phone_delete')]
    public function deletePhone(EntityManagerInterface $entityManager, $id='', $number=''): Response
    {
        $contact = $entityManager->getRepository(Contact::class)->find($id);
        if($contact == null) {
            return $this->render('phone/new_edit_phone.html.twig', [
                'contact'=> $contact,
                'phone' => null,
                'page_title' => 'My Contacts App - New phone',
                'action' => 'Failed to delete phone: no contact found'
            ]);
        } else {
            $phone = $entityManager->getRepository(Phone::class)
                ->findOneBy(['number'=>$number, 'id_contact'=>$id]);
            if($phone) {
                $entityManager->remove($phone);
                $entityManager->flush();
                $action = "Phone deleted";
            } else {
                $action = "Failed to delete phone";
            }

            return $this->render('phone/new_edit_phone.html.twig', [
                'phone' => $phone,
                'contact' => $contact,
                'page_title' => 'My Contacts App - Delete phone',
                'action' => $action
            ]);
        }
    }
}
