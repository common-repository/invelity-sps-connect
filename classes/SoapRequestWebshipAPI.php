<?php

require_once("WebServiceAPIClasses.php");

//Webship API wrapper class, See WebShip api documentation for details.
class SoapRequestWebshipAPI {
    
    function __construct(){
        
        $this->options = array (
                       
            'classmap' => array(
                //WSDL => PHP 
                'createShipmentResponse' => 'createShipmentResponse',
                'createCifShipmentResponse' => 'createCifShipmentResponse',
                'printShipmentLabelsResponse' => 'printShipmentLabelsResponse',
                'printEndOfDayResponse' => 'printEndOfDayResponse',
                'WebServicePrintResult' => 'WebServicePrintResult',
                'CreateCifShipmentResult' => 'CreateCifShipmentResult',
                'WebServiceShipmnetResult'=>'WebServiceShipmnetResult',
                'PackageInfo' => 'PackageInfo',
            ),                                
        );
    }   
    
    public function createShipment(createShipment  $createShipment ) {
       
       $result = "";
       try {      
            
           $client =  @new SoapClient(wsdlLink,  $this->options );	             
       }catch( SoapFault $e ){           

           return $e;
       }catch( Exception $e ){           

           $result = new SoapFault( $e->getCode(), $e->getMessage(), "", $e->getTraceAsString() );
           return $result;
       }
       try {          
                  
           $result = $client->createShipment( $createShipment );
       }catch( SoapFault $e){          
         
           return $e;
       }catch( Exception $e) {
         
           $result = new SoapFault( $e->getCode(), $e->getMessage(), "", $e->getTraceAsString() );
           return  $result;           
       }
       return $result;               
    }
       
    public function  createCifShipment( createCifShipment $creatCifShipment) {
        
        $result = "";
        try {
            
            $client =  @new SoapClient( wsdlLink, $this->options );
        }catch (SoapFault $e ){
            
            return $e;
        }catch ( Exception $e ){
            
            $result = new SoapFault( $e->getCode(), $e->getMessage(), "", $e->getTraceAsString() );
            return $result;
        }
        try {
            
            $result = $client->createCifShipment($creatCifShipment);
        }catch (SoapFault $e){
            
            return $e;
        }catch (Exception $e) {
            
            $result = new SoapFault($e->getCode(), $e->getMessage(), "", $e->getTraceAsString() );
            return  $result;
        }
        return $result;                             
    }
    
    public function  printShipmentLabels( $printShipmentLabels ){
        
        $result = "";
        try {
            
            $client =  @new SoapClient( wsdlLink, $this->options );
        }catch( SoapFault $e ){
            
            return $e;
        }catch( Exception $e ){
            
            $result = new SoapFault( $e->getCode(), $e->getMessage(), "", $e->getTraceAsString() );
            return $result;
        }
        try {
            
            $result = $client->printShipmentLabels( $printShipmentLabels );
        }catch( SoapFault $e ){
            
            return $e;
        }catch( Exception $e ) {
            
            $result = new SoapFault( $e->getCode(), $e->getMessage(), "", $e->getTraceAsString() );
            return  $result;
        }
        return $result;                        
    } 
    
    public function printEndOfDay( $printEndOfDay ){
        
        $result = "";
        try {
            
            $client =  @new SoapClient( wsdlLink, $this->options );
        }catch( SoapFault $e ){
            
            return $e;
        }catch( Exception $e ){
            
            $result = new SoapFault( $e->getCode(), $e->getMessage(), "", $e->getTraceAsString() );
            return $result;
        }
        try {
            
            $result = $client->printEndOfDay( $printEndOfDay );
        }catch( SoapFault $e ){
            
            return $e;
        }catch( Exception $e ) {
            
            $result = new SoapFault( $e->getCode(), $e->getMessage(), "", $e->getTraceAsString() );
            return  $result;
        }
        return $result;
    }        
    
    
    
    
    
}

