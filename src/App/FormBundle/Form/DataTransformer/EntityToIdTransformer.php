<?php
namespace App\FormBundle\Form\DataTransformer;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\DataTransformerInterface;

class EntityToIdTransformer implements DataTransformerInterface{
	
	/**
	 * @var EntityRepository
	 */
	protected $repository;
	
	public function __construct(EntityRepository $repository){
		$this->repository = $repository;
	}
	
	/**
	 * Transforms entity to id
	 * 
	 * @param \Object $entity
	 * @return integer
	 */
	public function transform($entity){
		if($entity === null || $entity->getId() === null){
			return '';
		}
		else{
			return $entity->getId();
		}
	}
	
	/**
	 * Transfers id to entity
	 * 
	 * @param integer $id
	 * @return \Object
	 */
	public function reverseTransform($id) {
		if(!empty($id)){
			return $this->repository->findOneById($id);
		}
		else{
			return null;
		}
	}


}
