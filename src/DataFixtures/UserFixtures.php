<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;

    // ON INJECTE DANS LE CONSTRUCTOR L'ENCODER POUR LE MOT DE PASS

    public function __construct(UserPasswordEncoderInterface $encoder)
    {

        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // $sectors = $this->sectorRepository->findOneBy(['nom' => 'SECRETAIRE']);



        // AJOUT DU COMPTE ADMIN

        $user = new User();
        $user->setEmail('admin@deloitte.com');
        $user->setNom('Delorme');
        $user->setPrenom('Aurelien');
        $user->setPhoto('admin.jpg');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setSecteur('Administration');

        // CRYPTAGE DU MOT DE PASS
        $password = $this->encoder->encodePassword($user, 'admin123@');
        $user->setPassword($password);

        $user->setRedefinePassword(true);
        $manager->persist($user);

        $manager->flush();
    }
}
