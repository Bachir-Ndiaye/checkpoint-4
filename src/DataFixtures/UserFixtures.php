<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordHasher)
    {
        $this->passwordEncoder = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('john@gmail.com');
        $user->setLastname('Doe');
        $user->setFirstname('John');
        $user->setRoles(['ROLE_USER']);
        $user->setAvatar('https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png');
        $user->setPassword($this->passwordEncoder->encodePassword($user,'password'));

        $admin = new User();
        $admin->setEmail('admin@gmail.com');
        $admin->setLastname('Admin');
        $admin->setFirstname('Admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setAvatar('https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png');
        $admin->setPassword($this->passwordEncoder->encodePassword($user, 'admin'));

        $manager->persist($user);
        $manager->persist($admin);

        $manager->flush();
    }
}
