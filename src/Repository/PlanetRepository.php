<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class PlanetRepository extends EntityRepository
{
    /**
     * @param $id
     * @param $user
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByCurrentPlanet($id, $user) {
        return $this->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(['id' => $id, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $username
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByFirstPlanet($username) {
        return $this->createQueryBuilder('p')
            ->join('p.user', 'u')
            ->where('u.username = :user')
            ->setParameters(['user' => $username])
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }
}