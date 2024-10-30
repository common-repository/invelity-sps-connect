<?php

//Webship API request and response data classes, enumerations. See Webship API doc for details.
class webServiceShipmentType {
    
    const TLAC = 0;
    const PREPRAVA = 1;    
}

class notifyType {
    
    const NONOTIFY = 0;
    const EMAIL    = 1;
    const SMS      = 2;
    const BOTH     = 3;    
}

class billingUnits {
    
    const KG   = "kg";
    const BOXA = "boxa";
    const BOXB = "boxb";
    const BOXC = "boxc";
    const WB3  = "winebox3";
    const WB6  = "winebox6";
    const WB12 = "winebox12";
}

class codAttribute {
    
    const CASH  = 0;
    const VIAMO = 3;
    const CARD  = 4;    
}

class deliveryType {
    
    const PT = "2PT";
    const PS = "2PS";    
}

class serviceName {
    
    const EXPRESS = "expres";
    const T0900   = "0900";
    const T1200   = "1200";
    const EXPORT  = "export";
}

class ShipmentPickup {
    
    function __construct( $pickupstartdetime, $pickupenddatetime ){
        $this->pickupstartdetime = $pickupstartdetime;
        $this->pickupenddatetime = $pickupenddatetime;        
    }
}

class Cod {
    
    function __construct( $codvalue , $codretbankacc = null, $codretbankcode = null ){
        
        $this->codvalue = $codvalue;
        $this->codretbankacc = $codretbankacc;
        $this->codretbankcode = $codretbankcode;               
    }    
}

class ShipmentAddress {
    
    function __construct( $city, $zip, $country, $street, $name, $contactPerson, $mobile, $email, $phone = null  ){
        
        $this->city    = trim( $city );
        $this->zip     = trim( $zip );
        $this->country = trim( $country );
        $this->street  = trim( $street );
        $this->name    = trim( $name );
        $this->contactPerson = trim( $contactPerson );
        $this->mobile = trim( $mobile );
        $this->email  = trim( $email );
        if ( isset ( $phone ) ){
            
            $this->phone = trim( $phone );
        }else {
            
            $this->phone = null;
        }
    }
}

class WebServicePackage {
    
    function __construct( $reffnr, $weight ){
        
        $this->reffnr = trim( $reffnr );
        $this->weight = trim( $weight );
    }
}

class WebServiceShipment {
    
    function __construct( Cod $cod = null, $insurvalue = null, $notifytype = null, $productdesc = null, bool $recipientpay = null, bool $returnshipment = null,
        bool $saturdayshipment = null, $servicename = null, bool $tel = null, $units, $packages, ShipmentAddress $pickupaddress = null, 
        ShipmentAddress $deliveryaddress, ShipmentPickup $shipmentpickup = null, $codattribute = null, $deliverytype = null ){
        
        $this->cod              = $cod;
        $this->insurvalue       = $insurvalue;
        $this->notifytype       = $notifytype;
        $this->productdesc      = $productdesc;
        $this->recipientpay     = $recipientpay;
        $this->returnshipment   = $returnshipment;
        $this->saturdayshipment = $saturdayshipment;
        $this->servicename      = $servicename;
        $this->tel              = $tel;
        $this->packages         = $packages;
        $this->units            = $units;
        $this->pickupaddress    = $pickupaddress;
        $this->deliveryaddress  = $deliveryaddress;
        $this->shipmentpickup   = $shipmentpickup; 
        $this->codattribute     = $codattribute;
        $this->deliverytype     = $deliverytype;
    }        
}

class createShipment {
        
  function __construct( $name, $password, $webServiceShipment, $webServiceShipmentType ){
        
        $this->name = $name;
        $this->password = $password;
        $this->webServiceShipment = $webServiceShipment;
        $this->webServiceShipmentType = $webServiceShipmentType;
    }    
}

//typo in WSDL
class WebServiceShipmnetResult {
    
    private $errors;
    private $warnings;
    
    public function getErrors() { return $this->errors; }
    public function getWarnings() { return $this->warnings; }    
}

class PackageInfo {
    
    private $refNr;
    private $shipNr;
    private $packageNo;
    
    public function getRefNr() { return $this->refNr; }
    public function getShipNr() { return $this->shipNr; }
    public function getPackageNo() { return $this->packageNo; }   

}

//SOAP reponse Object
class createShipmentResponse {

     private $createShipmentReturn;  
     public function getCreateShipmentReturn() { return $this->createShipmentReturn; }
 }


class createCifShipment {
    
    function __construct( $name, $password, $webServiceShipment, $webServiceShipmentType ){
        
        $this->name = $name;
        $this->password = $password;
        $this->webServiceShipment = $webServiceShipment;
        $this->webServiceShipmentType = $webServiceShipmentType;
    }    
}

class CreateCifShipmentResult {
    
    private $packageInfo;
    private $result; 
    
    public function getResult() { return $this->result; }  
    public function getPackageInfoCount() { 
        
        if ( is_array ( $this->packageInfo->item ) ){
            
            return count ( $this->packageInfo->item ); //2+
        }else {
            
            return 1;
        }        
    }
    public function getPackageInfo( $index) {
        
        $count = $this->getPackageInfoCount();      
        
        if ( $index < $count  && $index >= 0 ){
            
            if ( $count == 1 ){
             
                return  $this->packageInfo->item;
            }else {
                      
                return $this->packageInfo->item[$index];
            }
        }
    }
}

class createCifShipmentResponse {
   
    private $createCifShipmentReturn;
    
    public function getCreateCifShipmentReturn() { return $this->createCifShipmentReturn; }
    
}

class printShipmentLabels {
    
    function __construct( $aUserName, $aPassword ){
        
        $this->aUserName = $aUserName;
        $this->aPassword = $aPassword;
    }    
}

class printShipmentLabelsResponse {
    
    private $printShipmentLabelsReturn;
    public function getPrintShipmentLabelsReturn() { return $this->printShipmentLabelsReturn; }    
}


class printEndOfDay {
    
    function __construct( $aUserName, $aPassword ){
        
        $this->aUserName = $aUserName;
        $this->aPassword = $aPassword;
    }    
}

class printEndOfDayResponse {
    
    private $printEndOfDayReturn;
    public function getprintEndOfDayReturn() { return $this->printEndOfDayReturn; }    
}


class WebServicePrintResult {
    
    private $errors;
    private $documentUrl;
    
    public function getErrors() { return $this->errors; }
    public function getDocumentUrl() { return $this->documentUrl; }       
}


