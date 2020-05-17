<?php

namespace Sogedial\ApiBundle\Exception;

/**
 *
 * @author elodie
 */
class ParametersException extends Exception {
    
    protected $message="Parameters %s are mandatories, please have a look on the documentation";
    protected $messageSing="Parameter %s is mandatory, please have a look on the documentation";
	
    
    const STATUS_CODE = 500;


    public function __construct(array $parameters, string $message = '') {
        if($message=='')
			$message= (count($parameters)>1)?$this->message:$this->messageSing;
		
		$parametersStr = implode(',',$parameters);
		if(strrpos($parametersStr, ',') !== false)
			$parametersStr = substr_replace($parametersStr, ' and ', strrpos($parametersStr, ','), 1);
		$message = sprintf($message,$parametersStr);
        
        parent::__construct($message, self::STATUS_CODE);
        
    }
    
}
