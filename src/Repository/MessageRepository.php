<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function findMessagesBetweenUsers($user1, $user2)
    {
        return $this->createQueryBuilder('m')
            ->where('(m.sender = :user1 AND m.receiver = :user2) OR (m.sender = :user2 AND m.receiver = :user1)')
            ->setParameter('user1', $user1)
            ->setParameter('user2', $user2)
            ->orderBy('m.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findChatUsersWithLastMessage(User $user): array
    {
        $qb = $this->createQueryBuilder('m')
            ->select('u.id, u.name, u.email, m.text AS lastMessage, IDENTITY(m.sender) AS lastSenderId')
            ->join('m.sender', 'u_sender')
            ->join('m.receiver', 'u_receiver')
            ->leftJoin('App\Entity\User', 'u', 'WITH', 'u.id = u_sender.id OR u.id = u_receiver.id')
            ->where('m.id = (
                SELECT MAX(m2.id) 
                FROM App\Entity\Message m2
                WHERE (m2.sender = u OR m2.receiver = u) 
                  AND (m2.sender = :user OR m2.receiver = :user)
            )')
            ->andWhere('u.id != :user') // Exclude the current user
            ->setParameter('user', $user)
            ->orderBy('m.id', 'DESC') // Make sure the latest message is selected
            ->getQuery()
            ->getResult();
    
        return $qb;
    }
    
    

    //    /**
    //     * @return Message[] Returns an array of Message objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Message
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
