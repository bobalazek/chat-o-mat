<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;

class ChatChannelMessageRepository
    extends EntityRepository
{
    public function countAll()
    {
        return $this->createQueryBuilder('ccm')
            ->select('COUNT(ccm.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function countAllByChatChannel($chatChannel)
    {
        return $this->createQueryBuilder('ccm')
            ->select('COUNT(ccm.id)')
            ->where('ccm.chatChannel = :chatChannel')
            ->setParameter('chatChannel', $chatChannel)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getByChatChannelDatetime($chatChannel, $type, $datetime)
    {
        $qb = $this->createQueryBuilder('ccm')
            ->where('ccm.chatChannel = :chatChannel')
        ;

        if( $type == 'after') {
            $qb->andWhere('ccm.timeCreated >= :datetime');
        } elseif( $type == 'before' ) {
            $qb->andWhere('ccm.timeCreated <= :datetime');
        }

        $qb
            ->setParameter('chatChannel', $chatChannel)
            ->setParameter('datetime', $datetime)
        ;


        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    public function getByChatChannelPage($chatChannel, $page, $limitPerPage = 25)
    {
        $qb = $this->createQueryBuilder('ccm')
            ->where('ccm.chatChannel = :chatChannel')
            ->orderBy('ccm.timeCreated', 'DESC')
        ;

        $qb
            ->setParameter('chatChannel', $chatChannel)
        ;

        return array_reverse($qb
            ->getQuery()
            ->setMaxResults($limitPerPage)
            ->setFirstResult(($page -1) * $limitPerPage)
            ->getResult()
        );
    }
}
