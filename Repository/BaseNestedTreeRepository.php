<?php

namespace TNQSoft\CommonBundle\Repository;

use Doctrine\ORM\EntityManager;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/**
 * Base class for repositories.
 */
class BaseNestedTreeRepository extends NestedTreeRepository
{
    /**
     * The entity manager.
     *
     * @var EntityManager
     */
    protected $em;

    /**
     * Name of bundle that entity belongs to.
     *
     * @var string
     */
    protected $bundleName;

    /**
     * Constructs the current object.
     *
     * @param EntityManager $em
     * @param string        $bundleName
     */
    public function init(EntityManager $em, $bundleName)
    {
        $this->em = $em;
        $this->bundleName = $bundleName;
    }

    /**
     * Gets entity manager.
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    /**
     * Gets a repository from current entity manager.
     *
     * @param string $name Entity shortcut name
     *
     * @return EntityRepository
     */
    public function getRepository()
    {
        $sEntityName = $this->getEntityName();
        $sFullNameRepository = $this->bundleName.':'.$sEntityName;

        return $this->em->getRepository($sFullNameRepository);
    }

    /**
     * Get name of entity corresponding with this repository.
     *
     * @return string
     */
    public function getEntityName()
    {
        $manager_class = get_class($this);
        $aClass = explode('\\', $manager_class);
        $sEntityRepository = $aClass[count($aClass) - 1];
        $sEntityName = str_replace('Repository', '', $sEntityRepository);

        return $sEntityName;
    }

    /**
     * Finds all element from current repository.
     *
     * @return array
     */
    public function findAll()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Finds elements from the current repository according to a given set of
     * parameters.
     *
     * @param array $criteria The given set of criterias.
     * @param array $orderBy  The given set of sort orders.
     * @param int   $limit    The given limit.
     * @param int   $offset   The given offset.
     *
     * @return array
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Finds one element in current repository, given its by id.
     *
     * @param int $id The given id.
     *
     * @return object
     */
    public function findOneById($id)
    {
        return $this->getRepository()->findOneById($id);
    }

    /**
     * Finds one element in current repository with a given set of criterias and sort orders.
     *
     * @param array $criteria The given set of criterias.
     * @param array $orderBy  The given set of "order by"
     *
     * @return object
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * Flush all the current persisted and removed object from current entity
     * manager's repositories.
     */
    public function flush()
    {
        $this->getEntityManager()->flush();
    }

    /**
     * Persists a given object.
     *
     * @param object $oObject
     */
    public function persist($oObject)
    {
        $this->getEntityManager()->persist($oObject);
    }

    /**
     * Persists and flushes a given object in its current repository.
     *
     * @param object $oObject The given object.
     *
     * @return object.
     */
    public function persistAndFlush($oObject)
    {
        $this->getEntityManager()->persist($oObject);
        $this->getEntityManager()->flush();

        return $oObject;
    }

    /**
     * Removes a given object from its repository.
     *
     * @param object $oObject
     */
    public function remove($oObject)
    {
        $this->getEntityManager()->remove($oObject);
    }

    /**
     * Removes and flushes a given object from its repository.
     *
     * @param type $oObject
     */
    public function removeAndFlush($oObject)
    {
        $this->getEntityManager()->remove($oObject);
        $this->getEntityManager()->flush();
    }
}
