<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;

class ChatUserMessageRepository
    extends EntityRepository
{
    public function countAll()
    {
        return $this->createQueryBuilder('cum')
            ->select('COUNT(cum.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function countAllByUsers($user1, $user2)
    {
        return $this->createQueryBuilder('cum')
            ->select('COUNT(cum.id)')
            ->where(
                '(cum.user = :user1 AND cum.userFrom = :user2) OR
                    (cum.user = :user2 AND cum.userFrom = :user1)'
            )
            ->setParameter('user1', $user1)
            ->setParameter('user2', $user2)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getByUsersByDatetime($user1, $user2, $type, $datetime)
    {
        $qb = $this->createQueryBuilder('cum')
            ->where(
                '(cum.user = :user1 AND cum.userFrom = :user2) OR
                    (cum.user = :user2 AND cum.userFrom = :user1)'
            )
        ;

        if( $type == 'after') {
            $qb->andWhere('cum.timeCreated >= :datetime');
        } elseif( $type == 'before' ) {
            $qb->andWhere('cum.timeCreated <= :datetime');
        }

        $qb
            ->setParameter('datetime', $datetime)
            ->setParameter('user1', $user1)
            ->setParameter('user2', $user2)
        ;

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }
}
