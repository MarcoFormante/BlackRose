<?php

namespace App\Repository;

use App\Entity\ArtistImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ArtistImage>
 */
class ArtistImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArtistImage::class);
    }
   /**
     * Retrieves all images associated with a specific artist ID.
     *
     * @param int $id The ID of the artist.
     * @return ArtistImage[]
     */
   public function findImagesByArtistId(int $id): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.Artist = :id')
            ->setParameter('id', $id)   
            ->getQuery()
            ->getResult();
    }

}
