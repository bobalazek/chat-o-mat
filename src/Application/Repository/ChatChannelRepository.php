<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;

class ChatChannelRepository
    extends EntityRepository
{
    public function countAll()
    {
        return $this->createQueryBuilder('cc')
            ->select('COUNT(cc.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function countAllPrivate()
    {
        return $this->createQueryBuilder('cc')
            ->select('COUNT(cc.id)')
            ->where('cc.private = true')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getAllPublic()
    {
        return $this->createQueryBuilder('cc')
            ->where('cc.private = false')
            ->getQuery()
            ->getResult()
        ;
    }
}
