<?php
/*
* Copyright (c) 2011 Vantiv eCommerce Inc.
*
* Permission is hereby granted, free of charge, to any person
* obtaining a copy of this software and associated documentation
* files (the "Software"), to deal in the Software without
* restriction, including without limitation the rights to use,
* copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the
* Software is furnished to do so, subject to the following
* conditions:
*
* The above copyright notice and this permission notice shall be
* included in all copies or substantial portions of the Software.
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND
* EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
* OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
* NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
* HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
* WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
* FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
* OTHER DEALINGS IN THE SOFTWARE.
*/
namespace cnp\sdk\Test\functional;

use cnp\sdk\CnpOnlineRequest;
use cnp\sdk\CommManager;
use cnp\sdk\XmlParser;

class SaleFunctionalTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        CommManager::reset();
    }

    public function test_simple_sale_with_card()
    {
        $hash_in = array(
            'card' => array('type' => 'VI',
                'number' => '4100000000000000',
                'expDate' => '1213',
                'cardValidationNum' => '1213'),
            'id' => '1211',
            'orderId' => '2111',
            'reportGroup' => 'Planets',
            'orderSource' => 'ecommerce',
            'amount' => '123');

        $initialize = new CnpOnlineRequest();
        $saleResponse = $initialize->saleRequest($hash_in);
        $response = XmlParser::getNode($saleResponse, 'response');
        $this->assertEquals('000', $response);
        $location = XmlParser::getNode($saleResponse, 'location');
        $this->assertEquals('sandbox', $location);
    }

    public function test_simple_sale_with_paypal()
    {
        $hash_in = array(
            'paypal' => array("payerId" => '123', "token" => '12321312',
                "transactionId" => '123123'),
            'id' => '1211',
            'orderId' => '2111',
            'reportGroup' => 'Planets',
            'orderSource' => 'ecommerce',
            'amount' => '123');

        $initialize = new CnpOnlineRequest();
        $saleResponse = $initialize->saleRequest($hash_in);
        $response = XmlParser::getNode($saleResponse, 'response');
        $this->assertEquals('000', $response);
        $location = XmlParser::getNode($saleResponse, 'location');
        $this->assertEquals('sandbox', $location);
    }

    public function test_illegal_orderSource()
    {
        $hash_in = array(
            'paypal' => array("payerId" => '123', "token" => '12321312',
                "transactionId" => '123123'),
            'id' => '1211',
            'orderId' => '2111',
            'reportGroup' => 'Planets',
            'orderSource' => 'notecommerce',
            'amount' => '123');
        $initialize = new CnpOnlineRequest();
        $this->setExpectedException('PHPUnit_Framework_Error_Warning');
        $saleResponse = $initialize->saleRequest($hash_in);
        $message = XmlParser::getAttribute($saleResponse, 'cnpOnlineResponse', 'message');
    }

    public function test_illegal_card_type()
    {
        $hash_in = array(
            'card' => array('type' => 'DK',
                'number' => '4100000000000000',
                'expDate' => '1213',
                'cardValidationNum' => '1213'),
            'id' => '1211',
            'orderId' => '2111',
            'reportGroup' => 'Planets',
            'orderSource' => 'ecommerce',
            'amount' => '123');
        $this->setExpectedException('PHPUnit_Framework_Error_Warning');
        $initialize = new CnpOnlineRequest();
        $saleResponse = $initialize->saleRequest($hash_in);
        $message = XmlParser::getAttribute($saleResponse, 'cnpOnlineResponse', 'message');
    }

    public function no_reportGroup()
    {
        $hash_in = array(
            'card' => array('type' => 'VI',
                'number' => '4100000000000000',
                'expDate' => '1213',
                'cardValidationNum' => '1213'),
            'id' => '1211',
            'orderId' => '2111',
            'reportGroup' => 'Planets',
            'orderSource' => 'ecommerce',
            'amount' => '123');

        $initialize = new CnpOnlineRequest();
        $saleResponse = $initialize->saleRequest($hash_in);
        $response = XmlParser::getNode($saleResponse, 'response');
        $this->assertEquals('000', $response);
        $location = XmlParser::getNode($saleResponse, 'location');
        $this->assertEquals('sandbox', $location);
    }

    public function test_fields_out_of_order()
    {
        $hash_in = array(
            'paypal' => array("payerId" => '123', "token" => '12321312',
                "transactionId" => '123123'),
            'id' => '1211',
            'orderId' => '2111',
            'reportGroup' => 'Planets',
            'orderSource' => 'ecommerce',
            'amount' => '123');

        $initialize = new CnpOnlineRequest();
        $saleResponse = $initialize->saleRequest($hash_in);
        $response = XmlParser::getNode($saleResponse, 'response');
        $this->assertEquals('000', $response);
        $location = XmlParser::getNode($saleResponse, 'location');
        $this->assertEquals('sandbox', $location);
    }

    public function test_invalid_field()
    {
        $hash_in = array(
            'paypal' => array("payerId" => '123', "token" => '12321312',
                "transactionId" => '123123'),
            'id' => '1211',
            'orderId' => '2111',
            'nonexistant' => 'novalue',
            'reportGroup' => 'Planets',
            'orderSource' => 'ecommerce',
            'amount' => '123');

        $initialize = new CnpOnlineRequest();
        $saleResponse = $initialize->saleRequest($hash_in);
        $message = XmlParser::getNode($saleResponse, 'message');
        $this->assertEquals('Approved', $message);
        $location = XmlParser::getNode($saleResponse, 'location');
        $this->assertEquals('sandbox', $location);
    }

    public function test_simple_sale_with_applepay()
    {
        $hash_in = array(
            'applepay' => array(
                'data' => 'string data here',
                'header' => array('applicationData' => '454657413164',
                    'ephemeralPublicKey' => '1',
                    'publicKeyHash' => '1234',
                    'transactionId' => '12345'),
                'signature' => 'signature',
                'version' => 'version 1'),
            'id' => '1211',
            'orderId' => '2111',
            'reportGroup' => 'Planets',
            'orderSource' => 'ecommerce',
            'amount' => '123');

        $initialize = new CnpOnlineRequest();
        $saleResponse = $initialize->saleRequest($hash_in);
        $response = XmlParser::getNode($saleResponse, 'response');
        $this->assertEquals('123', $response);
        $location = XmlParser::getNode($saleResponse, 'location');
        $this->assertEquals('sandbox', $location);
    }

    public function test_simple_sale_with_applepay_insufficient_funds()
    {
        $hash_in = array(
            'applepay' => array(
                'data' => 'string data here',
                'header' => array('applicationData' => '454657413164',
                    'ephemeralPublicKey' => '1',
                    'publicKeyHash' => '1234',
                    'transactionId' => '12345'),
                'signature' => 'signature',
                'version' => 'version 1'),
            'id' => '1211',
            'orderId' => '2111',
            'reportGroup' => 'Planets',
            'orderSource' => 'ecommerce',
            'amount' => '1110');

        $initialize = new CnpOnlineRequest();
        $saleResponse = $initialize->saleRequest($hash_in);
        $response = XmlParser::getNode($saleResponse, 'response');
        $this->assertEquals('110', $response);
        $location = XmlParser::getNode($saleResponse, 'location');
        $this->assertEquals('sandbox', $location);
    }

    public function test_sale_with_processingType()
    {
        $hash_in = array(
            'card'=>array('type'=>'VI',
                'number'=>'4100200300011000',
                'expDate'=>'0521',),
            'id' => '1211',
            'orderId'=> '2111',
            'amount'=>'4999',
            'orderSource' => 'ecommerce',
            'processingType' => 'initialRecurring');
        $initilaize = new CnpOnlineRequest();
        $saleResponse = $initilaize->saleRequest($hash_in);
        $response = XmlParser::getNode($saleResponse,'response');
        $this->assertEquals('000',$response);
        $message = XmlParser::getNode($saleResponse,'message');
        $this->assertEquals('Approved',$message);
        $networkTransactionId = XmlParser::getNode($saleResponse,'networkTransactionId');
        $this->assertNotNull($networkTransactionId);
        $location = XmlParser::getNode($saleResponse, 'location');
        $this->assertEquals('sandbox', $location);
    }

    public function test_sale_with_processingTypeCOF()
    {
        $hash_in = array(
            'card'=>array('type'=>'VI',
                'number'=>'4100200300011000',
                'expDate'=>'0521',),
            'id' => '1211',
            'orderId'=> '2111',
            'amount'=>'4999',
            'orderSource' => 'ecommerce',
            'processingType' => 'initialCOF');
        $initilaize = new CnpOnlineRequest();
        $saleResponse = $initilaize->saleRequest($hash_in);
        $response = XmlParser::getNode($saleResponse,'response');
        $this->assertEquals('000',$response);
        $message = XmlParser::getNode($saleResponse,'message');
        $this->assertEquals('Approved',$message);
        $networkTransactionId = XmlParser::getNode($saleResponse,'networkTransactionId');
        $this->assertNotNull($networkTransactionId);
        $location = XmlParser::getNode($saleResponse, 'location');
        $this->assertEquals('sandbox', $location);
    }

    public function test_sale_with_processingTypeCOF1()
    {
        $hash_in = array(
            'card'=>array('type'=>'VI',
                'number'=>'4100200300011000',
                'expDate'=>'0521',),
            'id' => '1211',
            'orderId'=> '2111',
            'amount'=>'4999',
            'orderSource' => 'ecommerce',
            'processingType' => 'merchantInitiatedCOF');
        $initilaize = new CnpOnlineRequest();
        $saleResponse = $initilaize->saleRequest($hash_in);
        $response = XmlParser::getNode($saleResponse,'response');
        $this->assertEquals('000',$response);
        $message = XmlParser::getNode($saleResponse,'message');
        $this->assertEquals('Approved',$message);
        $networkTransactionId = XmlParser::getNode($saleResponse,'networkTransactionId');
        $this->assertNotNull($networkTransactionId);
        $location = XmlParser::getNode($saleResponse, 'location');
        $this->assertEquals('sandbox', $location);
    }

    public function test_sale_with_processingTypeCOF2()
    {
        $hash_in = array(
            'card'=>array('type'=>'VI',
                'number'=>'4100200300011000',
                'expDate'=>'0521',),
            'id' => '1211',
            'orderId'=> '2111',
            'amount'=>'4999',
            'orderSource' => 'ecommerce',
            'processingType' => 'cardholderInitiatedCOF');
        $initilaize = new CnpOnlineRequest();
        $saleResponse = $initilaize->saleRequest($hash_in);
        $response = XmlParser::getNode($saleResponse,'response');
        $this->assertEquals('000',$response);
        $message = XmlParser::getNode($saleResponse,'message');
        $this->assertEquals('Approved',$message);
        $networkTransactionId = XmlParser::getNode($saleResponse,'networkTransactionId');
        $this->assertNotNull($networkTransactionId);
        $location = XmlParser::getNode($saleResponse, 'location');
        $this->assertEquals('sandbox', $location);
    }

    public function test_simple_sale_with_AdvancedFraudCheckWithCustomAttribute()
    {
        $hash_in = array(
            'card' => array('type' => 'VI',
                'number' => '4100000000000000',
                'expDate' => '1213',
                'cardValidationNum' => '1213'),
            'id' => '654',
            'orderId' => '2111',
            'orderSource' => 'ecommerce',
            'amount' => '123',
            'advancedFraudChecks' => array(
                'threatMetrixSessionId' => 'abc123',
                'customAttribute1' => '1',
                'customAttribute2' => '2',
                'customAttribute3' => '3',
                'customAttribute4' => '4',
                'customAttribute5' => '5',
            ));
        $initialize = new CnpOnlineRequest();
        $saleResponse = $initialize->saleRequest($hash_in);
        $response = XmlParser::getNode($saleResponse, 'response');
        $this->assertEquals('000', $response);
        $location = XmlParser::getNode($saleResponse, 'location');
        $this->assertEquals('sandbox', $location);
    }

    public function test_simple_sale_with_sepaDirectDebit()
    {
        $hash_in = array(
            'sepaDirectDebit' => array(
                'mandateProvider' => 'Merchant',
                'sequenceType' => 'FirstRecurring',
                'mandateReference' => 'some string here',
                'mandateUrl' => 'some string here',
                //'mandateSignatureDate'=>'2017-01-24T09:00:00',
                'iban' => 'string with min of 15 char',
                'preferredLanguage' => 'USA'
            ),
            'id' => '1211',
            'orderId' => '2111',
            'reportGroup' => 'Planets',
            'orderSource' => 'ecommerce',
            'amount' => '123');

        $initialize = new CnpOnlineRequest();
        $saleResponse = $initialize->saleRequest($hash_in);
        $response = XmlParser::getNode($saleResponse, 'response');
        $this->assertEquals('000', $response);
        $location = XmlParser::getNode($saleResponse, 'location');
        $this->assertEquals('sandbox', $location);
    }


    public function test_simple_sale_with_networkTransactionId()
    {
        $hash_in = array(
            'card' => array('type' => 'VI',
                'number' => '4100000000000000',
                'expDate' => '1213',
                'cardValidationNum' => '1213'),
            'id' => '1211',
            'orderId' => '2111',
            'reportGroup' => 'Planets',
            'orderSource' => 'ecommerce',
            'amount' => '123');


        $initialize = new CnpOnlineRequest();
        $saleResponse = $initialize->saleRequest($hash_in);
        $this->assertEquals("000", XmlParser::getNode($saleResponse, 'response'));
        $this->assertEquals("Approved", XmlParser::getNode($saleResponse, 'message'));
        $this->assertEquals("sandbox", XmlParser::getNode($saleResponse, 'location'));
    }

    public function test_sale_with_detail_tax_multiple()
    {
        $sale_info = array(
            'id' => '1',
            'orderId' => '1',
            'amount' => '10010',
            'orderSource'=>'ecommerce',
            'billToAddress'=>array(
                'name' => 'John Smith',
                'addressLine1' => '1 Main St.',
                'city' => 'Burlington',
                'state' => 'MA',
                'zip' => '01803-3747',
                'country' => 'US'),
            'card'=>array(
                'number' =>'5112010000000003',
                'expDate' => '0112',
                'cardValidationNum' => '349',
                'type' => 'MC'
            ),
            'enhancedData' => array(
                'detailTax' => array(
                    'taxAmount' => 300,
                    'taxIncludedInTotal' => true
                ),
                'salesTax' => 500,
                'taxExempt' => false
            ),
        );
        $initialize = new CnpOnlineRequest();
        $saleResponse = $initialize->saleRequest($sale_info);
        #display results
        $response = XmlParser::getNode($saleResponse, 'response');
        $this->assertEquals('000', $response);
        $location = XmlParser::getNode($saleResponse, 'location');
        $this->assertEquals('sandbox', $location);
    }

    public function test_sale_with_billToAddress()
    {
        $sale_info = array(
            'id' => '1',
            'orderId' => '1',
            'amount' => '10010',
            'orderSource'=>'ecommerce',
            'billToAddress'=>array(
                'name' => 'John Smith',
                'addressLine1' => '1 Main St.',
                'city' => 'Burlington',
                'state' => 'MA',
                'zip' => '01803-3747',
                'country' => 'US',
                'sellerId' => '12345678912345',
                'url' => 'https://dummyurl.com'),
            'card'=>array(
                'number' =>'5112010000000003',
                'expDate' => '0112',
                'cardValidationNum' => '349',
                'type' => 'MC'
            ),
            'enhancedData' => array(
                'detailTax' => array(
                    'taxAmount' => 300,
                    'taxIncludedInTotal' => true
                ),
                'salesTax' => 500,
                'taxExempt' => false
            ),
        );
        $initialize = new CnpOnlineRequest();
        $saleResponse = $initialize->saleRequest($sale_info);
        #display results
        $response = XmlParser::getNode($saleResponse, 'response');
        $this->assertEquals('000', $response);
        $location = XmlParser::getNode($saleResponse, 'location');
        $this->assertEquals('sandbox', $location);
    }

    public function test_sale_with_Ideal()
    {
        $hash_in = array(
            'ideal' => array('preferredLanguage' => 'AD'),
            'id' => '1211',
            'orderId' => '2111',
            'reportGroup' => 'Planets',
            'orderSource' => 'ecommerce',
            'amount' => '123');

        $initialize = new CnpOnlineRequest();
        $saleResponse = $initialize->saleRequest($hash_in);
        $response = XmlParser::getNode($saleResponse, 'response');
        $this->assertEquals('000', $response);
        $this->assertEquals('http://redirect.url.vantiv.com', XmlParser::getNode($saleResponse, 'redirectUrl'));
        $this->assertEquals('sandbox', XmlParser::getNode($saleResponse, 'location'));
    }

    public function test_sale_with_Giropay()
    {
        $hash_in = array(
            'giropay' => array('preferredLanguage' => 'AD'),
            'id' => '1211',
            'orderId' => '2111',
            'reportGroup' => 'Planets',
            'orderSource' => 'ecommerce',
            'amount' => '123');

        $initialize = new CnpOnlineRequest();
        $saleResponse = $initialize->saleRequest($hash_in);
        $response = XmlParser::getNode($saleResponse, 'response');
        $this->assertEquals('000', $response);
        // re-implement when sandbox supports this payment type
//        $this->assertEquals('http://redirect.url.vantiv.com', XmlParser::getNode($saleResponse, 'redirectUrl'));
    }

    public function test_sale_with_Sofort()
    {
        $hash_in = array(
            'sofort' => array('preferredLanguage' => 'AD'),
            'id' => '1211',
            'orderId' => '2111',
            'reportGroup' => 'Planets',
            'orderSource' => 'ecommerce',
            'amount' => '123');

        $initialize = new CnpOnlineRequest();
        $saleResponse = $initialize->saleRequest($hash_in);
        $response = XmlParser::getNode($saleResponse, 'response');
        $this->assertEquals('000', $response);
        // re-implement when sandbox supports this payment type
//        $this->assertEquals('http://redirect.url.vantiv.com', XmlParser::getNode($saleResponse, 'redirectUrl'));
    }

    public function test_simple_sale_with_card_skip_realtime_au_true()
    {
        $hash_in = array(
            'card' => array('type' => 'VI',
                'number' => '4100000000000000',
                'expDate' => '1213',
                'cardValidationNum' => '1213'),
            'id' => '1211',
            'orderId' => '2111',
            'reportGroup' => 'Planets',
            'orderSource' => 'ecommerce',
            'amount' => '123',
            'skipRealtimeAU' => 'true');

        $initialize = new CnpOnlineRequest();
        $saleResponse = $initialize->saleRequest($hash_in);
        $response = XmlParser::getNode($saleResponse, 'response');
        $this->assertEquals('000', $response);
        $location = XmlParser::getNode($saleResponse, 'location');
        $this->assertEquals('sandbox', $location);
    }

    public function test_simple_sale_with_card_skip_realtime_au_false()
    {
        $hash_in = array(
            'card' => array('type' => 'VI',
                'number' => '4100000000000000',
                'expDate' => '1213',
                'cardValidationNum' => '1213'),
            'id' => '1211',
            'orderId' => '2111',
            'reportGroup' => 'Planets',
            'orderSource' => 'ecommerce',
            'amount' => '123',
            'skipRealtimeAU' => 'false');

        $initialize = new CnpOnlineRequest();
        $saleResponse = $initialize->saleRequest($hash_in);
        $response = XmlParser::getNode($saleResponse, 'response');
        $this->assertEquals('000', $response);
        $location = XmlParser::getNode($saleResponse, 'location');
        $this->assertEquals('sandbox', $location);
    }

    public function test_simple_sale_with_card_with_MerchantCategoryCode()
    {
        $hash_in = array(
            'card' => array('type' => 'VI',
                'number' => '4100000000000000',
                'expDate' => '1213',
                'cardValidationNum' => '1213'),
            'id' => '1211',
            'orderId' => '2111',
            'reportGroup' => 'Planets',
            'orderSource' => 'ecommerce',
            'amount' => '123',
            'merchantCategoryCode' => '6770');

        $initialize = new CnpOnlineRequest();
        $saleResponse = $initialize->saleRequest($hash_in);
        $response = XmlParser::getNode($saleResponse, 'response');
        $this->assertEquals('000', $response);
        $location = XmlParser::getNode($saleResponse, 'location');
        $this->assertEquals('sandbox', $location);
    }

    public function test_simple_sale_with_paypal_with_MerchantCategoryCode()
    {
        $hash_in = array(
            'paypal' => array("payerId" => '123', "token" => '12321312',
                "transactionId" => '123123'),
            'id' => '1211',
            'orderId' => '2111',
            'reportGroup' => 'Planets',
            'orderSource' => 'ecommerce',
            'amount' => '123',
            'merchantCategoryCode' => '6770');

        $initialize = new CnpOnlineRequest();
        $saleResponse = $initialize->saleRequest($hash_in);
        $response = XmlParser::getNode($saleResponse, 'response');
        $this->assertEquals('000', $response);
        $location = XmlParser::getNode($saleResponse, 'location');
        $this->assertEquals('sandbox', $location);
    }

    public function test_illegal_orderSource_with_MerchantCategoryCode()
    {
        $hash_in = array(
            'paypal' => array("payerId" => '123', "token" => '12321312',
                "transactionId" => '123123'),
            'id' => '1211',
            'orderId' => '2111',
            'reportGroup' => 'Planets',
            'orderSource' => 'notecommerce',
            'amount' => '123',
            'merchantCategoryCode' => '6770');
        $initialize = new CnpOnlineRequest();
        $this->setExpectedException('PHPUnit_Framework_Error_Warning');
        $saleResponse = $initialize->saleRequest($hash_in);
        $message = XmlParser::getAttribute($saleResponse, 'cnpOnlineResponse', 'message');
    }

    public function test_illegal_card_type_with_MerchantCategoryCode()
    {
        $hash_in = array(
            'card' => array('type' => 'DK',
                'number' => '4100000000000000',
                'expDate' => '1213',
                'cardValidationNum' => '1213'),
            'id' => '1211',
            'orderId' => '2111',
            'reportGroup' => 'Planets',
            'orderSource' => 'ecommerce',
            'amount' => '123',
            'merchantCategoryCode' => '6770');
        $this->setExpectedException('PHPUnit_Framework_Error_Warning');
        $initialize = new CnpOnlineRequest();
        $saleResponse = $initialize->saleRequest($hash_in);
        $message = XmlParser::getAttribute($saleResponse, 'cnpOnlineResponse', 'message');
    }

    public function no_reportGroup_with_MerchantCategoryCode()
    {
        $hash_in = array(
            'card' => array('type' => 'VI',
                'number' => '4100000000000000',
                'expDate' => '1213',
                'cardValidationNum' => '1213'),
            'id' => '1211',
            'orderId' => '2111',
            'reportGroup' => 'Planets',
            'orderSource' => 'ecommerce',
            'amount' => '123',
            'merchantCategoryCode' => '6770');

        $initialize = new CnpOnlineRequest();
        $saleResponse = $initialize->saleRequest($hash_in);
        $response = XmlParser::getNode($saleResponse, 'response');
        $this->assertEquals('000', $response);
        $location = XmlParser::getNode($saleResponse, 'location');
        $this->assertEquals('sandbox', $location);
    }



    public function test_simple_sale_with_applepay_with_MerchantCategoryCode()
    {
        $hash_in = array(
            'applepay' => array(
                'data' => 'string data here',
                'header' => array('applicationData' => '454657413164',
                    'ephemeralPublicKey' => '1',
                    'publicKeyHash' => '1234',
                    'transactionId' => '12345'),
                'signature' => 'signature',
                'version' => 'version 1'),
            'id' => '1211',
            'orderId' => '2111',
            'reportGroup' => 'Planets',
            'orderSource' => 'ecommerce',
            'amount' => '123',
            'merchantCategoryCode' => '6770');

        $initialize = new CnpOnlineRequest();
        $saleResponse = $initialize->saleRequest($hash_in);
        $response = XmlParser::getNode($saleResponse, 'response');
        $this->assertEquals('123', $response);
        $location = XmlParser::getNode($saleResponse, 'location');
        $this->assertEquals('sandbox', $location);
    }



    public function test_sale_with_processingType_with_MerchantCategoryCode()
    {
        $hash_in = array(
            'card'=>array('type'=>'VI',
                'number'=>'4100200300011000',
                'expDate'=>'0521',),
            'id' => '1211',
            'orderId'=> '2111',
            'amount'=>'4999',
            'orderSource' => 'ecommerce',
            'processingType' => 'initialRecurring',
            'merchantCategoryCode' => '6770');
        $initilaize = new CnpOnlineRequest();
        $saleResponse = $initilaize->saleRequest($hash_in);
        $response = XmlParser::getNode($saleResponse,'response');
        $this->assertEquals('000',$response);
        $message = XmlParser::getNode($saleResponse,'message');
        $this->assertEquals('Approved',$message);
        $networkTransactionId = XmlParser::getNode($saleResponse,'networkTransactionId');
        $this->assertNotNull($networkTransactionId);
        $location = XmlParser::getNode($saleResponse, 'location');
        $this->assertEquals('sandbox', $location);
    }




    public function test_simple_sale_with_AdvancedFraudCheckWithCustomAttribute_with_MerchantCategoryCode()
    {
        $hash_in = array(
            'card' => array('type' => 'VI',
                'number' => '4100000000000000',
                'expDate' => '1213',
                'cardValidationNum' => '1213'),
            'id' => '654',
            'orderId' => '2111',
            'orderSource' => 'ecommerce',
            'amount' => '123',
            'advancedFraudChecks' => array(
                'threatMetrixSessionId' => 'abc123',
                'customAttribute1' => '1',
                'customAttribute2' => '2',
                'customAttribute3' => '3',
                'customAttribute4' => '4',
                'customAttribute5' => '5',
            ),
            'merchantCategoryCode' => '6770');
        $initialize = new CnpOnlineRequest();
        $saleResponse = $initialize->saleRequest($hash_in);
        $response = XmlParser::getNode($saleResponse, 'response');
        $this->assertEquals('000', $response);
        $location = XmlParser::getNode($saleResponse, 'location');
        $this->assertEquals('sandbox', $location);
    }




    public function test_simple_sale_with_networkTransactionId_with_MerchantCategoryCode()
    {
        $hash_in = array(
            'card' => array('type' => 'VI',
                'number' => '4100000000000000',
                'expDate' => '1213',
                'cardValidationNum' => '1213'),
            'id' => '1211',
            'orderId' => '2111',
            'reportGroup' => 'Planets',
            'orderSource' => 'ecommerce',
            'amount' => '123',
            'merchantCategoryCode' => '6770');


        $initialize = new CnpOnlineRequest();
        $saleResponse = $initialize->saleRequest($hash_in);
        $this->assertEquals("000", XmlParser::getNode($saleResponse, 'response'));
        $this->assertEquals("Approved", XmlParser::getNode($saleResponse, 'message'));
        $this->assertEquals("sandbox", XmlParser::getNode($saleResponse,'location'));
    }

    public function test_sale_with_detail_tax_multiple_with_MerchantCategoryCode()
    {
        $sale_info = array(
            'id' => '1',
            'orderId' => '1',
            'amount' => '10010',
            'orderSource'=>'ecommerce',
            'billToAddress'=>array(
                'name' => 'John Smith',
                'addressLine1' => '1 Main St.',
                'city' => 'Burlington',
                'state' => 'MA',
                'zip' => '01803-3747',
                'country' => 'US'),
            'card'=>array(
                'number' =>'5112010000000003',
                'expDate' => '0112',
                'cardValidationNum' => '349',
                'type' => 'MC'
            ),
            'enhancedData' => array(
                'detailTax' => array(
                    'taxAmount' => 300,
                    'taxIncludedInTotal' => true
                ),
                'salesTax' => 500,
                'taxExempt' => false
            ),
            'merchantCategoryCode' => '6770'
        );
        $initialize = new CnpOnlineRequest();
        $saleResponse = $initialize->saleRequest($sale_info);
        #display results
        $response = XmlParser::getNode($saleResponse, 'response');
        $this->assertEquals('000', $response);
        $location = XmlParser::getNode($saleResponse, 'location');
        $this->assertEquals('sandbox', $location);
    }

    public function test_simple_sale_with_business_indicator()
    {
        $hash_in = array(
            'card' => array('type' => 'VI',
                'number' => '4100000000000000',
                'expDate' => '1213',
                'cardValidationNum' => '1213'),
            'id' => '1211',
            'orderId' => '2111',
            'reportGroup' => 'Planets',
            'orderSource' => 'ecommerce',
            'amount' => '123',
            'businessIndicator' => 'consumerBillPayment');

        $initialize = new CnpOnlineRequest();
        $saleResponse = $initialize->saleRequest($hash_in);
        $response = XmlParser::getNode($saleResponse, 'response');
        $this->assertEquals('000', $response);
        $location = XmlParser::getNode($saleResponse, 'location');
        $this->assertEquals('sandbox', $location);
    }

    public function test_simple_sale_with_interac()
    {
        $hash_in = array(
            'card' => array('type' => 'IC',
                'number' => '4100000000000000',
                'expDate' => '1213',
                'cardValidationNum' => '1213'),
            'cardholderAuthentication' => array(
                /// base64 value for dummy number '123456789012345678901234567890123456789012345678901234567890'
                /// System should accept the request with length 60 of authenticationValueType
                'authenticationValue' => 'MTIzNDU2Nzg5MDEyMzQ1Njc4OTAxMjM0NTY3ODkwMTIzNDU2Nzg5MDEyMzQ1Njc4OTAxMjM0NTY3ODkw'
            ),
            'id' => '1211',
            'orderId' => '2111',
            'reportGroup' => 'Planets',
            'orderSource' => 'ecommerce',
            'amount' => '123');

        $initialize = new CnpOnlineRequest();
        $saleResponse = $initialize->saleRequest($hash_in);
        $response = XmlParser::getNode($saleResponse, 'response');
        $this->assertEquals('000', $response);
        $location = XmlParser::getNode($saleResponse, 'location');
        $this->assertEquals('sandbox', $location);
    }
    public function test_sale_with_additionalCOFData()
    {
        $hash_in = array(
            'card' => array('type' => 'VI',
                'number' => '4100000000000000',
                'expDate' => '1213',
                'cardValidationNum' => '1213'),
            'id' => '1211',
            'orderId' => '2111',
            'reportGroup' => 'Planets',
            'orderSource' => 'ecommerce',
            'amount' => '123',
            'merchantCategoryCode' => '6770',
            'billToAddress' => array(
                'name' => 'David Berman A',
                'addressLine1' => '10 Main Street',
                'city' => 'San Jose',
                'state' => 'ca',
                'zip' => '95032',
                'country' => 'USA',
                'email' => 'dberman@phoenixProcessing.com',
                'phone' => '781-270-1111',
                'sellerId' => '21234234A1',
                'url' => 'www.google.com',
            ),
            'shipToAddress' => array(
                'name' => 'Raymond J. Johnson Jr. B',
                'addressLine1' => '123 Main Street',
                'city' => 'McLean',
                'state' => 'VA',
                'zip' => '22102',
                'country' => 'USA',
                'email' => 'ray@rayjay.com',
                'phone' => '978-275-0000',
                'sellerId' => '21234234A2',
                'url' => 'www.google.com',
            ),
            'crypto' => 'true',
            'retailerAddress' => array(
                'name' => 'John doe',
                'addressLine1' => '123 Main Street',
                'addressLine2' => '123 Main Street',
                'addressLine3' => '123 Main Street',
                'city' => 'Cincinnati',
                'state' => 'OH',
                'zip' => '45209',
                'country' => 'USA',
                'email' => 'noone@abc.com',
                'phone' => '1234562783',
                'sellerId' => '21234234A',
                'companyName' => 'Google INC',
                'url' => 'www.google.com',
            ),
            'additionalCOFData' => array(
                'totalPaymentCount' => 'ND',
                'paymentType' => 'Fixed Amount',
                'uniqueId' => '234GTYH654RF13',
                'frequencyOfMIT' => 'Annually',
                'validationReference' => 'ANBH789UHY564RFC@EDB',
                'sequenceIndicator' => '86',
            ),
            'orderChannel' => 'IN_STORE_KIOSK',
            'fraudCheckStatus' => 'CLOSE',

        );

        $initialize = new CnpOnlineRequest();
        //print_r($hash_in);
        $authorizationResponse = $initialize->saleRequest($hash_in);
        $response = XmlParser::getNode($authorizationResponse, 'response');
        $this->assertEquals('000', $response);
        $location = XmlParser::getNode($authorizationResponse, 'location');
        $this->assertEquals('sandbox', $location);
    }


}
