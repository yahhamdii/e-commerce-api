<?php

namespace Sogedial\ApiBundle\Service;

use Doctrine\ORM\EntityManager;
use Sogedial\ApiBundle\Entity\Attribut;
use Sogedial\ApiBundle\Entity\CartItem;
use Sogedial\ApiBundle\Entity\Container;
use Sogedial\ApiBundle\Entity\ContainerCartItem;
use Sogedial\ApiBundle\Entity\Item;
use Sogedial\ApiBundle\Entity\Package;
use Sogedial\ApiBundle\Entity\Platform;

class ContainerManager
{

    /**
     * @var EntityManager
     */
    private $em;

      /**
       * ContainerManager constructor.
       * @param EntityManager $em
       */
      public function __construct(EntityManager $em)
      {
        $this->em = $em;
      }

    protected function updateContainer($quantityToLoad, Package $package, Container $container, CartItem $cartItem)
    {

        $volumePackage = $cartItem->getItem()->getPackage()->getVolumePackage();
        if(strlen($volumePackage) == 0){
            $item = $cartItem->getItem();
            $pcb = $item->getPcb();
            $volumeUnit = $item->getPackage()->getVolumeUc();
            //TODO a verifier unite de mesure pour volume unit soit cm3 soit m3 pout savoir si on va diviser par 100 ou nn
            $volumePackage = ($volumeUnit * $pcb) / 100;
        }
        // il faut savoir pour le conteneur actuel la quantite qu'on peut ajouter à condition ne pas depasser le volume et poids permissible
        //attention ajouté aussi la quantite (commandé ou reste à chargé -$quantityToLoad-) car cet qt peut etre inferieure au qt allowed par le container
        $quantityAllowedToAdd = min($container->numberPackageAllowed($volumePackage, $package->getWeightGrossPackage()), $quantityToLoad);

        if($quantityAllowedToAdd != 0) {
            //generer ContainerCartItem
            if($container->getId() != null){
                $containerCartItem = $this->inContainer($cartItem, $container->getId());
            }

            if(!isset($containerCartItem)){
                $containerCartItem = new ContainerCartItem();
                $containerCartItem->setContainer($container)
                    ->setCartItem($cartItem);
                $cartItem->addContainerCartItem($containerCartItem);
                $container->addContainerCartItem($containerCartItem);
            }

            $containerCartItem->setQuantity($containerCartItem->getQuantity() + $quantityAllowedToAdd);

            //mettre à jour le volume chargé et poids chargé
            $container->setVolumeLoaded($container->getVolumeLoaded() + $containerCartItem->getVolumePackageLoaded($quantityAllowedToAdd));
            $container->setWeightLoaded($container->getWeightLoaded() + $containerCartItem->getWeightPackageLoaded($quantityAllowedToAdd));


            //mettre à jour la quantite restant à chargé
            $quantityToLoad -= $quantityAllowedToAdd;
            $this->em->persist($container);
            $this->em->persist($containerCartItem);
            $this->em->persist($cartItem);
        }

        return $quantityToLoad;
    }

    /**
     * @param CartItem $cartItem
     * @return array
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function loadContainerCart(CartItem $cartItem, $quantityToLoad = null)
    {
        $cart = $cartItem->getCart();
        $item = $cartItem->getItem();

        $containerizationAttributValue =  $this->hasConteneurization($cart->getPlatform());

        if ($containerizationAttributValue) {
            //on recupere les containers qui ont la meme temperature que l'item à chargé
                $containers = $cart->getContainerByTemperature($item->getType());

            if (count($containers) == 0) {
                //si il n ya pas de containers du meme temperature que l'item à chargé, on cree les containers necessaires ensuite on les charges
                $this->generateContainers($cartItem->getQuantity(), $item->getType(), $item->getPackage(), $cartItem);
            } else {
                //ici on a des containers de meme temperature que l'item à chargé
                $this->fillContainers($containers, $cartItem, $quantityToLoad);
            }
            $this->em->flush();
        }
    }

    protected function generateContainers($quantityToLoad, $temperature, Package $package, CartItem $cartItem)
    {
        if($quantityToLoad == 0){

            return ;
        }
        //creation du container
        $container = $this->createDefaultContainer($temperature);

        $quantityToLoad = $this->updateContainer($quantityToLoad, $package, $container, $cartItem);
        $this->generateContainers($quantityToLoad, $temperature, $package, $cartItem);
    }
  /**
      * remplir les containers deja existant et si il n ya pas de place il cree de nouveaux containers pour les cherger
      *
      * @param $containers
      * @param CartItem $cartItem
      *
      */
      protected function fillContainers($containers,CartItem $cartItem, $quantityToLoad = null)
      {
          /** @var Container $container */
          if(is_null($quantityToLoad)){
              $quantityToLoad = $cartItem->getQuantity();
          }
          $package = $cartItem->getItem()->getPackage();
          foreach ($containers as $container){
              $quantityToLoad = $this->updateContainer($quantityToLoad, $package, $container, $cartItem);
              //si toute la quantite est chargé , on sort du boucle
              if($quantityToLoad == 0){
                  break;
              }
          }
        if($quantityToLoad > 0){
              // on appel la fonction pour la preparation des containers
            $this->generateContainers($quantityToLoad, $cartItem->getItem()->getType(), $package, $cartItem);
        }
      }

    /**
     * @param CartItem $cartItem
     */
    public function deloadContainer(CartItem $cartItem, $quantityToDeload = null)
    {
        $containerCartItems = $cartItem->getContainerCartItems();
        if(!$containerCartItems->isEmpty()){
            if(is_null($quantityToDeload)){
                $quantityToDeload = $cartItem->getQuantity();
            }
            //inverser le contenue de containerCartItems
            $containerCartItems = array_reverse($containerCartItems->toArray());

            foreach ($containerCartItems as $containerCartItem){
                /** @var ContainerCartItem $containerCartItem */
                $quantityAllowedToRemove = min($quantityToDeload, $containerCartItem->getQuantity());

                $container = $containerCartItem->getContainer();
                $container->setWeightLoaded($container->getWeightLoaded() - $containerCartItem->getWeightPackageLoaded($quantityAllowedToRemove));
                $container->setVolumeLoaded($container->getVolumeLoaded() - $containerCartItem->getVolumePackageLoaded($quantityAllowedToRemove));

                //mettre à jour la quantiy de item dans le container
                $containerCartItem->setQuantity($containerCartItem->getQuantity() - $quantityAllowedToRemove);

                //mettre à jour la quantityToDeload
                $quantityToDeload -= $quantityAllowedToRemove;

                //tester si la quantity dans le container egale à zero
                if($containerCartItem->getQuantity() == 0){
                    $this->em->remove($containerCartItem);
                }else{
                    $this->em->persist($containerCartItem);
                }

                if($container->getVolumeLoaded() == 0 && $container->getWeightLoaded() == 0){
                    $this->em->remove($container);
                }else{
                    $this->em->persist($container);
                }

                //tester si il ya ancore de quantity a supprimer
                if($quantityToDeload == 0){
                    break;
                }
            }
        }
    }


    public function updateQuantity(CartItem $cartItem)
    {
        $containerCartItems = $cartItem->getContainerCartItems();
        if (isset($containerCartItems)) {
            $oldQuantity = 0;
            foreach ($containerCartItems as $containerCartItem) {
                $oldQuantity += $containerCartItem->getQuantity();
            }

            //comparer  oldQuantity avec newQuantity
            $newQuantity = $cartItem->getQuantity();
            if($newQuantity > $oldQuantity) {
                //TODO mettre à ajour la fonction load
                $quantityToLoad = $newQuantity - $oldQuantity;
                $this->loadContainerCart($cartItem, $quantityToLoad);
            }elseif ($newQuantity < $oldQuantity){
                $quantityToDeload = $oldQuantity - $newQuantity;
                $this->deloadContainer($cartItem, $quantityToDeload);
            }
        }
    }

    protected function inContainer(CartItem $cartItem, $containerId)
    {
        $containerCartItems = $cartItem->getContainerCartItems();
        if(isset($containerCartItems)){
            foreach ($containerCartItems as $containerCartItem){
                /** @var ContainerCartItem $containerCartItem*/
                if($containerCartItem->getContainer()->getId() == $containerId){

                    return $containerCartItem;
                }
            }
        }

        return null;
    }

    /**
     * @param $temperature
     * @return Container
     */
    protected function createDefaultContainer($temperature): Container
    {
        $container = new Container();
        $container->setWeightPermissibleMax("25500")
            ->setTemperature($temperature)
            ->setLoadingType(Container::LOADING_PALLET);

        if ($temperature == Item::TEMPERATURE_DRY) {
            $container->setVolumePermissibleMax("26")
                ->setName(Container::TYPE_20_DRY);
        } else {
            $container->setVolumePermissibleMax("22")
                ->setName(Container::TYPE_20_REEFER);
        }
        return $container;
    }

    protected function hasConteneurization(Platform $platform)
    {
        $containerizationAttributValue = 0;
        $containerizationAttribut = $platform->getAttributByKey(Attribut::KEY_HAS_CONTAINERIZATION);
        if ($containerizationAttribut) {
            $containerizationAttributValue = $containerizationAttribut->getValue();
        }

        return $containerizationAttributValue;
    }

    public function refreshContainer(Container $container)
    {
        if($this->outVolumeOrWeight($container)){
            $cartItemsToMove = $this->getExceedItems($container);
            foreach ($cartItemsToMove as $cartItem){
                $this->loadContainerCart($cartItem);
            }
        }

        return ;
    }

    /**
     * @param Container $container
     * @return bool
     */
    protected function outVolumeOrWeight(Container $container): bool
    {
        return ($container->getVolumeLoaded() > $container->getVolumePermissibleMax() ||
            $container->getWeightLoaded() > $container->getWeightPermissibleMax());
    }

    /**
     * @param Container $container
     * @return array
     */
    protected function getExceedItems(Container $container): array
    {
        $containerCartItems = array_reverse($container->getContainerCartItems()->toArray());
        $cartItemsToMove = [];
        if (count($containerCartItems) != 0) {
            foreach ($containerCartItems as $containerCartItem) {
                /** @var ContainerCartItem $containerCartItem */
                $container->setWeightLoaded($container->getWeightLoaded() - $containerCartItem->getWeightPackageLoaded());
                $container->setVolumeLoaded($container->getVolumeLoaded() - $containerCartItem->getVolumePackageLoaded());

                $cartItem = $containerCartItem->getCartItem();
                $cartItemsToMove[] = $cartItem;

                //supprimer les lien
                $container->removeContainerCartItem($containerCartItem);
                $cartItem->removeContainerCartItem($containerCartItem);

                $this->em->remove($containerCartItem);

                if (!$this->outVolumeOrWeight($container)) {
                    break;
                }
            }
        }

        return $cartItemsToMove;
    }

}
