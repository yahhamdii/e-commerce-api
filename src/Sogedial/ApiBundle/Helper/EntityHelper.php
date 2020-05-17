<?php
declare(strict_types = 1);

namespace Sogedial\ApiBundle\Helper;

/**
 * Class EntityHelper
 *
 * @package Sogedial\ApiBundle
 */
class EntityHelper
{   
    public static function updateDatas($entity, $datas, $em){
       
        $classMetadata = $em->getClassMetadata(get_class($entity));

        foreach ($classMetadata->getAssociationMappings() as $association) {

            $fieldName = $association['fieldName'];

            $methodName = ucfirst($fieldName);

            $setter = 'set'.$methodName;

            if(method_exists($entity,$setter) && isset($datas[$fieldName])){

                $data = $datas[$fieldName];

                if( isset( $data['id'] ) ){

                    $id = $data['id'];

                    $subEntity = $em->getRepository( $association['targetEntity'] )->find( $id );
                    
                    if( $subEntity ){
                        $entity->$setter($subEntity);
                    }
                }
            }
        }
        
        foreach ($classMetadata->getColumnNames() as $name) {

            $methodName = $name;
            $methodName = preg_replace_callback("/(?:^|_)([a-z])/", function($matches) {                
                    return strtoupper($matches[1]);
                }, $methodName);
            
            $methodName = ucfirst($methodName);

            $setter = 'set'.$methodName;

            if(method_exists($entity,$setter) && isset($datas[$name])){                               

                $entity->$setter($datas[$name]);
            }
        }

        return $entity;
    }
}
