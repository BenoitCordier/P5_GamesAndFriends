<?php

namespace App\Repository;

use App\Entity\MessageThread;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MessageThread>
 *
 * @method MessageThread|null find($id, $lockMode = null, $lockVersion = null)
 * @method MessageThread|null findOneBy(array $criteria, array $orderBy = null)
 * @method MessageThread[]    findAll()
 * @method MessageThread[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageThreadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MessageThread::class);
    }
}
