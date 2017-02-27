<?php

/*
 * This file is part of the PaylineTools package.
 *
 * (c) Monext <http://www.monext.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PaylineTools;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use SoapClient;
use SoapVar;
use PaylineTools\CustomPaymentPageCode;
use PaylineTools\CustomPaymentPageCodeList;
use PaylineTools\Contract;
use PaylineTools\ContractList;
use PaylineTools\PointOfSale;
//$vendorPath = realpath(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . DIRECTORY_SEPARATOR;
$vendorPath = realpath(dirname(dirname(dirname((__FILE__))))) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR;
//$classesPath = $vendorPath . 'monext' . DIRECTORY_SEPARATOR . 'payline-tools' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'PaylineTools' . DIRECTORY_SEPARATOR;
$classesPath = realpath(dirname(dirname(dirname((__FILE__))))) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'PaylineTools' . DIRECTORY_SEPARATOR;
require_once $vendorPath . 'autoload.php';
require_once $classesPath . 'Contract.class.php';
require_once $classesPath . 'ContractList.class.php';
require_once $classesPath . 'CustomPaymentPageCode.class.php';
require_once $classesPath . 'PointOfSale.class.php';

class PaylineToolsClient
{
    /**
     * WSDL file name
     */
    const WSDL = 'PaylineTools_v1.0.wsdl';

    /**
     * Monolog\Logger instance
     */
    private $logger;

    function __construct($proxy_host, $proxy_port, $proxy_login, $proxy_password, $pathLog = null, $logLevel = Logger::INFO)
    {
        date_default_timezone_set("Europe/Paris");
        $this->logger = new Logger('PaylineToolsClient');
        if (is_null($pathLog)) {
            $this->logger->pushHandler(new StreamHandler(realpath(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . date('Y-m-d') . '.log', $logLevel)); // set default log folder
        } elseif (strlen($pathLog) > 0) {
            $this->logger->pushHandler(new StreamHandler($pathLog . date('Y-m-d') . '.log', $logLevel)); // set custom log folder
        }
        
        $this->logger->addInfo('__construct', array(
            'proxy_host' => $proxy_host,
            'proxy_port' => $proxy_port,
            'proxy_login' => $proxy_login,
            'proxy_password' => $this->hideChars($proxy_password, 1, 1)
        ));
        $this->soapclient_options = array();
        if ($proxy_host != '') {
            $this->soapclient_options['proxy_host'] = $proxy_host;
            $this->soapclient_options['proxy_port'] = $proxy_port;
            $this->soapclient_options['proxy_login'] = $proxy_login;
            $this->soapclient_options['proxy_password'] = $proxy_password;
        }
        $this->soapclient_options['style'] = SOAP_DOCUMENT;
        $this->soapclient_options['use'] = SOAP_LITERAL;
    }

    /**
     * make an array from a payline server response object.
     *
     * @param object $response
     *            response from payline
     * @return array representation of the object
     *        
     */
    private static function responseToArray($response)
    {
        $array = array();
        foreach ($response as $k => $v) {
            if (is_object($v) || is_array($v)) {
                $array[$k] = PaylineToolsClient::responseToArray($v);
            } else {
                $array[$k] = $v;
            }
        }
        return $array;
    }
    
    /**
     * Hide characters in a string
     *
     * @param String $inString
     *            the string to hide
     * @param int $n1
     *            number of characters shown at the begining of the string
     * @param int $n2
     *            number of characters shown at end begining of the string
     */
    private function hideChars($inString, $n1, $n2)
    {
        $inStringLength = strlen($inString);
        if ($inStringLength < ($n1 + $n2)) {
            return $inString;
        }
        $outString = substr($inString, 0, $n1);
        $outString .= substr("********************", 0, $inStringLength - ($n1 + $n2));
        $outString .= substr($inString, - ($n2));
        return $outString;
    }

    /**
     * TODO doc
     */
    public function getPaymentMeans()
    {
        try {
            $client = new SoapClient(dirname(__FILE__) . '/' . PaylineToolsClient::WSDL, $this->soapclient_options);
            $WSresponse = $client->getPaymentMeans();
            $response = PaylineToolsClient::responseToArray($WSresponse);
            $this->logger->addInfo('getPaymentMeansResponse', array(
                'result' => $response['result']
            ));
            return $response;
        } catch (Exception $e) {
            $this->logger->addError('Exception occured at getPaymentMeans call', array(
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ));
            $ERROR = array();
            $ERROR['result']['code'] = PaylineToolsClient::ERR_CODE;
            $ERROR['result']['longMessage'] = $e->getMessage();
            $ERROR['result']['shortMessage'] = $e->getMessage();
            return $ERROR;
        }
    }

    /**
     * Inserts merchant data in configurator
     *
     * @param
     *            merchant data $merchantSettings returned by getMerchantSettings
     *            
     */
    public function doWebConfig($merchantSettings)
    {
        $listPointOfSale = array();
        foreach ($merchantSettings['POS'] as $merchantPointOfSale) {
            $insertPOS = new PointOfSale();
            $insertPOS->label = $merchantPointOfSale['label'];
            $insertPOS->webmasterEmail = $merchantPointOfSale['webmasterEmail'];
            $insertPOS->webstoreURL = $merchantPointOfSale['webstoreURL'];
            $insertPOS->contractList = new ContractList();
            foreach ($merchantPointOfSale['contracts'] as $merchantContract) {
                $insertContract = new Contract();
                $insertContract->cardType = $merchantContract['cardType'];
                $insertContract->contractNumber = $merchantContract['contractNumber'];
                $insertContract->enrolment3DS = 0; // TODO
                $insertContract->label = $merchantContract['label'];
                $insertPOS->contracts->contract[] = new SoapVar($insertContract, SOAP_ENC_OBJECT, 'contract', 'PaylineTools');
            }
            $insertPOS->customPaymentPageCodeList = new CustomPaymentPageCodeList();
            foreach ($merchantPointOfSale['customPageCode'] as $merchantCustomPageCode) {
                $insertCustomPageCode = new CustomPaymentPageCode();
                $insertCustomPageCode->code = $merchantCustomPageCode['code'];
                $insertCustomPageCode->label = $merchantCustomPageCode['label'];
                $insertCustomPageCode->type = $merchantCustomPageCode['type'];
                $insertPOS->customPaymentPageCodeList->customPaymentPageCode[] = new SoapVar($insertCustomPageCode, SOAP_ENC_OBJECT, 'customPaymentPageCode', 'PaylineTools');
            }
            $listPointOfSale[] = new SoapVar($insertPOS, SOAP_ENC_OBJECT, 'pointOfSale', 'PaylineTools');
        }
        $WSRequest = array(
            'cryptedMerchantID' => hash("SHA256", $this->header_soap['login']),
            'environment' => $this->environment,
            'listPointOfSale' => $listPointOfSale
        );
        try {
            $client = new SoapClient(dirname(__FILE__) . '/' . PaylineToolsClient::WSDL, $this->soapclient_options);
            $WSresponse = $client->insertMerchantSettings($WSRequest);
            $response = PaylineToolsClient::responseToArray($WSresponse);
            $this->writeTrace($response['code']);
            return $response;
        } catch (Exception $e) {
            $this->writeTrace("Exception : " . $e->getMessage());
            $ERROR = array();
            $ERROR['result']['code'] = PaylineToolsClient::ERR_CODE;
            $ERROR['result']['longMessage'] = $e->getMessage();
            $ERROR['result']['shortMessage'] = $e->getMessage();
            return $ERROR;
        }
    }

    /**
     * returns payment ways created in the configurator
     *
     * @param
     *            $merchantId
     * @param
     *            $environment
     *            
     */
    public function getPaymentWays($merchantId, $environment)
    {
        $this->logger->addInfo('getPaymentMeansRequest', array(
            'merchantId' => $this->hideChars($merchantId, 6, 1),
            'environment' => $environment
        ));
        try {
            $client = new SoapClient(dirname(__FILE__) . '/' . PaylineToolsClient::WSDL, $this->soapclient_options);
            $getPaymentWaysRequest = array(
                'cryptedMerchantID' => hash("SHA256", $merchantId),
                'environment' => $environment
            );
            $WSresponse = $client->getPaymentWays($getPaymentWaysRequest);
            $response = PaylineToolsClient::responseToArray($WSresponse);
            $this->logger->addInfo('getPaymentWaysResponse', array(
                'result' => $response['result']
            ));
            return $response;
        } catch (Exception $e) {
            $this->writeTrace("Exception : " . $e->getMessage());
            $ERROR = array();
            $ERROR['result']['code'] = PaylineToolsClient::ERR_CODE;
            $ERROR['result']['longMessage'] = $e->getMessage();
            $ERROR['result']['shortMessage'] = $e->getMessage();
            return $ERROR;
        }
    }
}