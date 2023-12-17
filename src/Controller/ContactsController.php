<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Contact;
use App\Entity\Phone;
class ContactsController extends AbstractController
{
    #[Route('/contact/{id<\d+>}', name: 'single_contact')]
    public function contact(EntityManagerInterface $entityManager, $id=''): Response
    {
        $contact = $entityManager->getRepository(Contact::class)->find($id);

        return $this->render('contacts/contact.html.twig', [
            'contact' => $contact,
            'page_title' => 'My Contacts App - Contact',
        ]);
    }

    /*
    Automatically Fetching with EntityValueResolver
    #[Route('/contact/{id<\d+>}', name: 'single_contact')]
    public function contact(Contact $contact): Response
    {
        return $this->render('contacts/contact.html.twig', [
            'contact' => $contact,
            'page_title' => 'My Contacts App - Contact',
        ]);
    }*/

    #[Route('/contact_list', name: 'contact_list')]
    public function contactList(EntityManagerInterface $entityManager): Response
    {
        return $this->render('contacts/list.html.twig', [
            'contacts' => $entityManager->getRepository(Contact::class)->findAll(),
            'page_title' => 'My Contacts App - Contact List'
        ]);
    }

    #[Route('/contact/search/{search_string}', name: 'search_contact')]
    public function searchContact(EntityManagerInterface $entityManager, $search_string=''): Response
    {
        return $this->render('contacts/list.html.twig', [
            'contacts' => $entityManager->getRepository(Contact::class)
                ->findByNameOrSurname($search_string),
            'page_title' => 'My Contacts App - Search results'
        ]);
    }

    #[Route('/contact/test/new', name: 'new_contact')]
    public function newContact(EntityManagerInterface $entityManager) : Response {
        $contact = new Contact();
        $contact->setTitle("Mrs.");
        $contact->setName("Carla");
        $contact->setSurname("Fontana");
        $contact->setBirthdate(date_create("1980-01-30"));
        $contact->setEmail("carlafon@mail.com");

        $entityManager->persist($contact);
        $entityManager->flush();

        $action = ($contact ? 'New contact added' : 'Failed to add contact');

        return $this->render('contacts/new_edit_contact.html.twig', [
            'contact' => $contact,
            'page_title' => 'My Contacts App - New contact',
            'action' => $action
        ]);
    }

    #[Route('/contact/test/edit/{id<\d+>}', name: 'contact_edit')]
    public function updateContact(EntityManagerInterface $entityManager, $id='') : Response {
        $contact = $entityManager->getRepository(Contact::class)->find($id);
        if($contact) {
            $action = "Contact updated";
            $contact->setName("New Name");
            $entityManager->flush();
        } else {
            $action = "Failed to modify contact";
        }

        return $this->render('contacts/new_edit_contact.html.twig', [
            'contact' => $contact,
            'page_title' => 'My Contacts App - Update contact',
            'action' => $action
        ]);
    }

    #[Route('/contact/test/delete/{id<\d+>}', name: 'contact_delete')]
    public function deleteContact(EntityManagerInterface $entityManager, $id=''): Response {
        $contact = $entityManager->getRepository(Contact::class)->find($id);
        if($contact) {
            //Remove the phones
            $phones = $entityManager->getRepository(Phone::class)
                ->findBy(['id_contact'=>$id]);
            foreach ($phones as $phone){
                $entityManager->remove($phone);
            }
            $entityManager->remove($contact);
            $entityManager->flush();
            $action = "Contact deleted";
        } else {
            $action = "Failed to delete contact";
        }

        return $this->render('contacts/new_edit_contact.html.twig', [
            'contact' => $contact,
            'page_title' => 'My Contacts App - Delete contact',
            'action' => $action
        ]);
    }
}
