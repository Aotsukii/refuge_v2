<?php

namespace App\DataFixtures;


use App\Entity\Race;
use App\Entity\User;
use App\Entity\Animal;
use App\Entity\Gerant;
use App\Entity\Membre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder){
        $this->passwordEncoder = $passwordEncoder;
    }
    
    public function load(ObjectManager $manager)
    {
        $superAdmin = new User();
        $superAdmin->setEmail("buck@buck.fr");
        $superAdmin->setRoles(["ROLE_SUPER_ADMIN"]);
        $superAdmin->setPassword($this->passwordEncoder->encodePassword($superAdmin, "password"));
        $superAdmin->setUsualBrowser("Google Chrome");
        $superAdmin->setRegisterDate(new \DateTime('now'));
        $manager->persist($superAdmin);

        
        $admin = new User();
        $admin->setEmail("admin@admin.fr");
        $admin->setRoles(["ROLE_ADMIN"]);
        $admin->setPassword($this->passwordEncoder->encodePassword($admin, "password"));
        $admin->setUsualBrowser("Google Chrome");
        $admin->setRegisterDate(new \DateTime('now'));
        $manager->persist($admin);

        $gerant = new Gerant();
        $gerant->setUser($admin);
        $manager->persist($gerant);

        $user = new User();
        $user->setEmail("member1@member.fr");
        $user->setRoles(["ROLE_MEMBER"]);
        $user->setPassword($this->passwordEncoder->encodePassword($user, "password"));
        $user->setUsualBrowser("Google Chrome");
        $user->setRegisterDate(new \DateTime('now'));
        $manager->persist($user);

        $member = new Membre();
        $member->setUser($user);
        $manager->persist($member);

        $user2 = new User();
        $user2->setEmail("member2@member.fr");
        $user2->setRoles(["ROLE_MEMBER"]);
        $user2->setPassword($this->passwordEncoder->encodePassword($user2, "password"));
        $user2->setUsualBrowser("Google Chrome");
        $user2->setRegisterDate(new \DateTime('now'));
        $manager->persist($user2);

        $member2 = new Membre();
        $member2->setUser($user2);
        $manager->persist($member2);

        $race = new Race();
        $race->setName("Européen");
        $manager->persist($race);

        for ($i=0; $i<20; $i++){
            $animal = new Animal();
            $animal->setType("Chat");
            $animal->setName("Animal no.".$i);
            $animal->setAge(3);
            if ($i%2==0){
                $animal->setSex("Male");
                $animal->setOkDogs(true);
                $animal->setOkKids(true);

            } else {
                $animal->setSex("Femelle");
                $animal->setOkDogs(false);
                $animal->setOkKids(false);
            }
            $animal->setDescription("Description de l'animal no.".$i);
            $animal->setOkCats(true);
            $animal->setNeedCare(false);
            $animal->setAdoptionPrice(random_int(0,100));
            $animal->setDateAdd(new \DateTime('now'));
            $animal->setRace($race);
            $animal->setIsHosted(false);
            $animal->setGerant($gerant);
            $animal->setImageLinks(["via.placeholder.com/300x400"]);
            $manager->persist($animal);
        }

        $manager->flush();
    }
}
