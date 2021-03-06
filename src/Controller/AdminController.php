<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Event;
use App\Entity\Animal;
use App\Form\AnimalType;
use App\Form\AddEventType;
use App\Services\ModalService;
use App\Services\AnimalService;
use App\Repository\FARepository;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Repository\EventRepository;
use App\Repository\AnimalRepository;
use App\Repository\GerantRepository;
use App\Repository\MembreRepository;
use App\Repository\DonationRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\ContainerParametersHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin", name="admin")
     */
    public function index(DonationRepository $donationRepository, EventRepository $eventRepository, MembreRepository $membreRepository, FARepository $FARepository, UserRepository $userRepository)
    {
        $donors = $donationRepository->getThreeHighestDonors();
        $donorsMail = [];

        foreach ($donors as $donor) {
            $member = $donor[0]->getMemberDonating();
            $tmp = $member->getUser();
            array_push($donorsMail, $tmp->getEmail());
        }

        $newUsersArray = [];
        for ($i=0; $i<=6; $i++){
            $usersYesterday = $userRepository->getUsersByDay(-$i);
            array_push($newUsersArray, $usersYesterday);
        }

        $nextEvent = $eventRepository->findOneByNextDate();
        $participatingMembers = [];
        if ($nextEvent != null){
            $participatingMembers = $nextEvent->getParticipatingMembers();
        }
        $participatingUsersMail = [];

        $isModalValid = true;
        $nbOfUsers = $userRepository->getTotalUserNumber();
        if ($nbOfUsers != 0){
            $percentageOfMembers = (($membreRepository->getTotalMembersNumber())/$nbOfUsers)*100;
            $percentageOfFA =100-$percentageOfMembers;
        } else {
            $percentageOfMembers = 100;
            $percentageOfFA = 0;
        }

        for ($i=0; $i<count($participatingMembers); $i++){
            array_push($participatingUsersMail, $participatingMembers[$i]->getUser()->getEmail());
        }
        // $donations = $this->getDoctrine()->getRepository('App:Donation')->findThreeByLatest();
        $donations = $donationRepository->findAll();
        $donationsTotal=0;
        for ($i=0;$i<count($donations);$i++){
            $donationsTotal+=$donations[$i]->getAmount();
        }
        return $this->render('admin/index.html.twig',
        ['nextEvent' => $nextEvent, 'participatingUsers' => $participatingUsersMail,
         'donations' => $donations,
         'percentageOfMembers' => $percentageOfMembers,
         'percentageOfFA' => $percentageOfFA,
         'donationsTotal' => $donationsTotal,
         'newUsersArray' => $newUsersArray,
         'donors' => $donors,
         'donorsMail' => $donorsMail]
        );
    }

    /** 
    * @IsGranted("ROLE_ADMIN")
    * @Route("/admin/animalList", name="animalList")
    */
    public function animalList(AnimalService $animalService, AnimalRepository $animalRepository, Request $request)
    {
        $animal = new Animal();
        $addAnimalForm = $this->createForm(AnimalType::class, $animal);
        if ($addAnimalForm->handleRequest($request)){
            $animalService->createAnimal($addAnimalForm, $animal);
        }
        
        $animals = $animalRepository->findAll();
        return $this->render('admin/animalList.html.twig', 
            ['animals' => $animals, 'addAnimalForm' => $addAnimalForm->createView(), 'isModalValid' => true]);
    }

    /** 
    * @IsGranted("ROLE_ADMIN")
    * @Route("/admin/userList", name="userList")
    */
    public function userList(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();

        return $this->render('admin/userList.html.twig', 
        ['users' => $users]);
    }

    /** 
    * @IsGranted("ROLE_ADMIN")
    * @Route("/admin/eventList", name="eventList")
    */
    public function eventList(GerantRepository $gerantRepository, EventRepository $eventRepository, Request $request, EntityManagerInterface $entityManager)
    {
        $events = $eventRepository->findAll();
        $event = new Event();
        $gerant = $gerantRepository->findOneByUser($this->getUser());

        $addEventForm = $this->createForm(AddEventType::class, $event);
        $addEventForm->handleRequest($request);
        $isModalValid = true;

        if ($addEventForm->isSubmitted()) {
            if ($addEventForm->isValid()){
                $event->setGerant($gerant);
                $entityManager->persist($event);
                $entityManager->flush();
                $this->addFlash(
                    'success',
                    'L\'évenement a bien été enregistré !'
                );
                $event = new Event();
                $addEventForm = $this->createForm(AddEventType::class, $event);
                
                $this->redirectToRoute('eventList', ['addEventForm' => $addEventForm->createView()]);
            } else {
                $isModalValid = false;
            }
        }
        return $this->render('admin/eventList.html.twig', 
        ['events' => $events, 'addEventForm' => $addEventForm->createView(), 'isModalValid' => $isModalValid]);
    }

    /** 
    * @IsGranted("ROLE_ADMIN")
    * @Route("/admin/donationsList", name="donationsList")
    */
    public function donationsList(DonationRepository $donationRepository, UserRepository $userRepository, MembreRepository $membreRepository)
    {
        $donations = $donationRepository->findAll();
        $donationMailsArray = [];
        foreach ($donations as $don) {
         
            $donatingMember= $don->getMemberDonating();
            // $userCorresponding = $userRepository->findByMember($donatingMember);
            $userCorresponding = $donatingMember->getUser();
            $mail = $userCorresponding->getEmail();
            array_push($donationMailsArray, $mail);
        }
        return $this->render('admin/donationsList.html.twig', 
        ['donations' => $donations, 'donationsIds' => $donationMailsArray],);
    }

    /** 
    * @IsGranted("ROLE_ADMIN")
    * @Route("/injectEventToModal", name="injectEventToModal")
    */
    public function injectEventToModal(EventRepository $eventRepository, Request $request, ModalService $modalService)
    {
        return $modalService->injectEntityToModal($eventRepository, $request);
    }

    /** 
    * @IsGranted("ROLE_ADMIN")
    * @Route("/deleteEvent/{id}", name="deleteEvent")
    */
    public function deleteEvent($id, UserRepository $userRepository, EventRepository $eventRepository, MembreRepository $membreRepository, Request $request, EntityManagerInterface $entityManager)
    {
        $event = $eventRepository->findOneById($id);
        $membres = $event->getParticipatingMembers();
        foreach ($membres as $membre) {
            $event->removeParticipatingMember($membre);
        }

        $entityManager->remove($event);
        $entityManager->flush();
        $this->addFlash(
            'success',
            'L\'évenement a bien été supprimé !'
        );
        return $this->redirectToRoute('eventList');
    }
           
        
    /** 
    * @IsGranted("ROLE_ADMIN")
    * @Route("/injectUserToModal", name="injectUserToModal")
    */
    public function injectUserToModal(UserRepository $userRepository, Request $request, ModalService $modalService)
    {
        return $modalService->injectEntityToModal($userRepository, $request);
    }

    /** 
    * @IsGranted("ROLE_ADMIN")
    * @Route("/deleteUser/{id}", name="deleteUser")
    */
    public function deleteUser($id, UserRepository $userRepository, FARepository $FARepository, MembreRepository $membreRepository, Request $request, EntityManagerInterface $entityManager)
    {
        $user = $userRepository->find($id);

        // Check if the user corresponds to a member and deletes the foreign keys first
        $membre = $membreRepository->findByUser($user);
        if ($membre != []){
            $donations = $membre[0]->getDonation();
            if (count($donations)>0){
                for($i=0;$i<count($donations);$i++){
                    $entityManager->remove($donations[$i]);
                }
            }

            $events = $membre[0]->getEvent();
            if (count($events)>0){
                for($i=0;$i<count($events);$i++){
                    $entityManager->remove($events[$i]);
                }
            }

            $entityManager->remove($membre[0]);
        }

        // Check if the user corresponds to a foster family and deletes the foreign keys first
        $fa = $FARepository->findByUser($user);
        if ($fa != []){
            // TODO check et remove les animaux hebergés -> plus tard
            $entityManager->remove($fa[0]);
        }


        $entityManager->remove($user);
        $entityManager->flush();
        $this->addFlash(
            'success',
            'L\'utilisateur a bien été supprimé !'
        );
        return $this->redirectToRoute('userList');
    }

    /** 
    * @IsGranted("ROLE_ADMIN")
    * @Route("/injectAnimalToModal", name="injectAnimalToModal")
    */
    public function injectAnimalToModal(AnimalRepository $animalRepository, Request $request, ModalService $modalService)
    {
        return $modalService->injectEntityToModal($animalRepository, $request);
    }

    /** 
    * @IsGranted("ROLE_ADMIN")
    * @Route("/deleteAnimal/{id}", name="deleteAnimal")
    */
    public function deleteAnimal($id, AnimalRepository $animalRepository, Request $request, EntityManagerInterface $entityManager)
    {
        $animal = $animalRepository->find($id);
        $entityManager->remove($animal);
        $entityManager->flush();
        $this->addFlash(
            'success',
            'L\'animal a bien été supprimé !'
        );
        return $this->redirectToRoute('animalList');
    }
}
