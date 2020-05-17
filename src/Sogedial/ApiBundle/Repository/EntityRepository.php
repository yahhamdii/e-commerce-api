<?php

namespace Sogedial\ApiBundle\Repository;

use Sogedial\ApiBundle\Helper\RepositoryHelper;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * EntityRepository
 *
 */
class EntityRepository extends \Doctrine\ORM\EntityRepository
{

    protected $tokenStorage;

    public function getTokenStorage(){

        return $this->tokenStorage;

    }

    public function setTokenStorage(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;

        return $this;
    }

    public function getRepositoryInjected(String $class){

        return $this->getEntityManager()
        ->getRepository($class)
        ->setTokenStorage($this->getTokenStorage());

    }

    public function createRegexp($searchString)
    {
        // array containing stopwords
        $stopwords = array("alors", "au", "aucuns", "aussi", "autre", "avant", "avec", "avoir", "bon", "car", "ce", "cela", "ces", "ceux", "chaque", "ci", "comme", "comment",
            "dans", "des", "du", "dedans", "dehors", "depuis", "devrait", "doit", "donc", "dos", "début", "elle", "elles", "en", "encore", "essai", "est", "et", "eu", "fait",
            "faites", "fois", "font", "hors", "ici", "il", "ils", "je", "juste", "la", "le", "les", "leur", "là", "ma", "maintenant", "mais", "mes", "mine", "moins", "mon",
            "mot", "même", "ni", "nommés", "notre", "nous", "ou", "où", "par", "parce", "pas", "peut", "peu", "plupart", "pour", "pourquoi", "quand", "que", "quel", "quelle",
            "quelles", "quels", "qui", "sa", "sans", "ses", "seulement", "si", "sien", "son", "sont", "sous", "soyez","sujet", "sur", "ta", "tandis", "tellement", "tels", "tes",
            "ton", "tous", "tout", "trop", "très", "tu", "voient", "vont", "votre", "vous", "vu", "ça", "étaient", "état", "étions", "été", "être"
        );

        // escape the stopword array and implode with pipe
        $s = '#\b('.implode("|", $stopwords).')\b#i';

        // replace with emptystring
        $searchString = preg_replace($s, "", $searchString);
        //delete multiple space
        $searchString = trim(preg_replace('/\s+/', ' ', $searchString));
        $searchString = str_replace(" ","|",$searchString);

        return $searchString;
    }

    public function findBy( array $filter, array $orderBy = null,  $limit = null,  $offset = null ){

        $qb = $this->createFindByQueryBuilder( $filter, $orderBy,  $limit,  $offset );

        return $qb->getQuery()->getResult();
    }

    protected function createFindByQueryBuilder( array $filter, array $orderBy = null,  $limit = null,  $offset = null ){

        $qb = $this->createQueryBuilder( $this->getAlias() );

        $this->processCriteria( $qb, $filter );        
        $this->processOrderBy( $qb, $orderBy );        
        $this->processLimit( $qb, $limit,  $offset );
        
        return $qb;
    }


    /**
     * count any custom query
     *
     * @param array $filter     
     * @return mixed
     */
    public function getCount(array $filter)
    {
        $qb = $this->createCountQueryBuilder($filter);       

        return $qb->getQuery()->getOneOrNullResult();
    }

    protected function createCountQueryBuilder( array $filter ){

        $qb = $this->createQueryBuilder( $this->getAlias() );

        $this->processCriteria( $qb, $filter );
        $this->processCount( $qb );
        
        return $qb;
    }

    protected function processCriteria( $qb, $filter ){

        RepositoryHelper::processCriteria($qb, $filter);

    }

    protected function processOrderBy( $qb, $orderBy ){

        RepositoryHelper::processOrderBy($qb, $orderBy);

    }

    protected function processLimit( $qb, $limit,  $offset ){

        RepositoryHelper::processLimit($qb, $limit,  $offset);

    }

    protected function processCount( $qb ){

        RepositoryHelper::processCount( $qb );

    }

    public function getAlias(){

        return $this->alias;
        
    }


    protected function addTypeQueryPart(QueryBuilder $qb, $type = null){

        if($type != null){
            $instanceOf = $this->getEntityName().ucfirst( $type );
            $class = new \ReflectionClass($instanceOf);
            $qb->andWhere($this->getAlias().' INSTANCE OF :instanceOf');
            $qb->setParameter('instanceOf', $this->getEntityManager()->getClassMetadata($class->getName()));
        }

        return $qb;
    }

    protected function findPlatform( $platform ){

        return $this->getEntityManager()->getRepository('SogedialApiBundle:Platform')->find( $platform );

    }
}
