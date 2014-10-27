<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 21.01.14
 * Time: 14:37
 */

namespace App\PurchaseBundle\Form;


use App\AccountBundle\Entity\AccountProfile;
use App\PurchaseBundle\Entity\Purchase;
use App\PurchaseBundle\Form\PurchaseWrapper\Item;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraints as Assert;

class PurchaseWrapper
{
    protected $purchase;

    /**
     * @Assert\Count(min = "1", minMessage = "You must add at least one product" )
     */
    protected $items;

    protected $isDraft;

    /**
     * @var EntityManager
     */
    protected $entity_manager;

    public function __construct(Purchase $purchase, $entity_manager){
        $this->purchase = $purchase;
        $this->entity_manager = $entity_manager;
    }

    /**
     * @param array $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getItems()
    {
        if($this->items === null){
            $this->items = [];

            foreach($this->purchase->getItems() as $item){
                /** @var Purchase\Item $item */
                $wrapper_item = new Item();
                $wrapper_item->setAccountProduct($item->getProduct());
                $wrapper_item->setPrice($item->getPrice());
                $wrapper_item->setQuantity($item->getQuantity());
                $wrapper_item->setSupplier($this->purchase->getSupplier());
                $wrapper_item->setTaxes($item->getTaxes());

                $this->items[] = $wrapper_item;
            }
        }

        return $this->items;
    }

    public function getAccountProfile()
    {
        return $this->purchase->getAccountProfile();
    }

    public function setAccountProfile(AccountProfile $accountProfile)
    {
        $this->purchase->setAccountProfile($accountProfile);
    }

    public function getStatus()
    {
        return $this->purchase->getStatus();
    }

    public function setStatus($status)
    {
        $this->purchase->setStatus($status);
    }

    public function getIsDraft()
    {
        return $this->purchase->getIsDraft();
    }

    public function setIsDraft($isDraft)
    {
        if($isDraft == false ){
            $this->purchase->makeOrder();
        }
    }

    public function getPurchases()
    {
        $purchases_by_supplier_id = [];
        $purchase_items = [];

        //prepare purchase items
        if($this->purchase->getSupplier() !== null){
            $purchases_by_supplier_id[$this->purchase->getSupplier()->getId()] = $this->purchase;
        } elseif(count($this->items) > 0 && $this->purchase->getSupplier() === null){
            $first_supplier = $this->items[0]->getSupplier();
            $this->purchase->setSupplier($first_supplier);
            $purchases_by_supplier_id[$first_supplier->getId()] = $this->purchase;
        }

        //items
        if(count($purchases_by_supplier_id) > 0){
            $items_by_account_product = [];
            foreach($this->purchase->getItems() as $purchase_item){
                /** @var Purchase\Item $purchase_item */
                $purchase_item->setQuantity(0);
                $purchase_item->setPrice(0);
                $items_by_account_product[$purchase_item->getProduct()->getId()] = $purchase_item;
            }

            $purchase_items[key($purchases_by_supplier_id)] = $items_by_account_product;
        }

        foreach($this->items as $item){
            /** @var Item $item */
            $item_supplier = $item->getSupplier();

            if(!isset($purchases_by_supplier_id[$item_supplier->getId()])){
                $purchase = clone $this->purchase;
                $purchase->setSupplier($item_supplier);
                $purchases_by_supplier_id[$item_supplier->getId()] = $purchase;
                $purchase_items[$item_supplier->getId()] = [];
            }

            /** @var Purchase $purchase */
            $purchase = $purchases_by_supplier_id[$item_supplier->getId()];

            if(!isset($purchase_items[$item_supplier->getId()][$item->getAccountProduct()->getId()])){
                $purchase_item = new Purchase\Item();
                $purchase_item->setPrice($item->getPrice());
                $purchase_item->setQuantity($item->getQuantity());
                $purchase_item->setProduct($item->getAccountProduct());
                $purchase_item->setTaxes($item->getTaxes());

                //add to collections
                $purchase->addItemItem($purchase_item);
                $purchase_items[$item_supplier->getId()][$item->getAccountProduct()->getId()] = $purchase_item;
            } else {
                /** @var Purchase\Item $purchase_item */
                $purchase_item = $purchase_items[$item_supplier->getId()][$item->getAccountProduct()->getId()];

                if($purchase_item->getPrice() === 0){
                    $purchase_item->setPrice($item->getPrice());
                    $purchase_item->setTaxes($item->getTaxes());
                } elseif($purchase_item->getPrice() !== $item->getPrice()) {
                    throw new \InvalidArgumentException('Cannot add the same product with different prices');
                }

                $purchase_item->increaseQuantity($item->getQuantity());
            }
        }

        $purchase_item = null;
        if($this->purchase->getSupplier() !== null){
            foreach($purchase_items[$this->purchase->getSupplier()->getId()] as $purchase_item){
                if($purchase_item->getQuantity() == 0){
                    $this->purchase->getItems()->removeElement($purchase_item);
                    $this->entity_manager->remove($purchase_item);
                }
            }
        }

        return array_values($purchases_by_supplier_id);
    }
}