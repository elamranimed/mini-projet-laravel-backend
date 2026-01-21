<?php

namespace App\Http\Controllers;

class SoapServerController extends Controller
{
    public function handle()
    {
        $server = new \SoapServer(null, [
            'uri' => 'urn:BookService',
            'encoding' => 'UTF-8',
            'cache_wsdl' => WSDL_CACHE_NONE
        ]);
        $server->setObject(new BookSoapController());
        
        ob_start();
        $server->handle();
        return response(ob_get_clean(), 200)->header('Content-Type', 'text/xml; charset=utf-8');
    }

    public function wsdl()
    {
        $soapUrl = url('/soap');
        $wsdl = <<<WSDL
<?xml version="1.0" encoding="UTF-8"?>
<definitions xmlns="http://schemas.xmlsoap.org/wsdl/"
             xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/"
             xmlns:tns="urn:BookService"
             xmlns:xsd="http://www.w3.org/2001/XMLSchema"
             targetNamespace="urn:BookService">

    <types>
        <xsd:schema targetNamespace="urn:BookService">
            <xsd:complexType name="Book">
                <xsd:sequence>
                    <xsd:element name="id" type="xsd:int"/>
                    <xsd:element name="title" type="xsd:string"/>
                    <xsd:element name="author" type="xsd:string" minOccurs="0"/>
                    <xsd:element name="published_year" type="xsd:int" minOccurs="0"/>
                    <xsd:element name="genre" type="xsd:string" minOccurs="0"/>
                    <xsd:element name="created_at" type="xsd:string" minOccurs="0"/>
                    <xsd:element name="updated_at" type="xsd:string" minOccurs="0"/>
                </xsd:sequence>
            </xsd:complexType>
        </xsd:schema>
    </types>

    <message name="getAllBooksRequest"/>
    <message name="getAllBooksResponse">
        <part name="return" type="xsd:string"/>
    </message>

    <message name="getBookRequest">
        <part name="id" type="xsd:int"/>
    </message>
    <message name="getBookResponse">
        <part name="return" type="xsd:string"/>
    </message>

    <message name="getBooksByAuthorRequest">
        <part name="author" type="xsd:string"/>
    </message>
    <message name="getBooksByAuthorResponse">
        <part name="return" type="xsd:string"/>
    </message>

    <message name="createBookRequest">
        <part name="title" type="xsd:string"/>
        <part name="author" type="xsd:string"/>
        <part name="published_year" type="xsd:int"/>
        <part name="genre" type="xsd:string"/>
    </message>
    <message name="createBookResponse">
        <part name="return" type="xsd:string"/>
    </message>

    <message name="updateBookRequest">
        <part name="id" type="xsd:int"/>
        <part name="title" type="xsd:string"/>
        <part name="author" type="xsd:string"/>
        <part name="published_year" type="xsd:int"/>
        <part name="genre" type="xsd:string"/>
    </message>
    <message name="updateBookResponse">
        <part name="return" type="xsd:string"/>
    </message>

    <message name="deleteBookRequest">
        <part name="id" type="xsd:int"/>
    </message>
    <message name="deleteBookResponse">
        <part name="return" type="xsd:string"/>
    </message>

    <portType name="BookServicePortType">
        <operation name="getAllBooks">
            <input message="tns:getAllBooksRequest"/>
            <output message="tns:getAllBooksResponse"/>
        </operation>
        <operation name="getBook">
            <input message="tns:getBookRequest"/>
            <output message="tns:getBookResponse"/>
        </operation>
        <operation name="getBooksByAuthor">
            <input message="tns:getBooksByAuthorRequest"/>
            <output message="tns:getBooksByAuthorResponse"/>
        </operation>
        <operation name="createBook">
            <input message="tns:createBookRequest"/>
            <output message="tns:createBookResponse"/>
        </operation>
        <operation name="updateBook">
            <input message="tns:updateBookRequest"/>
            <output message="tns:updateBookResponse"/>
        </operation>
        <operation name="deleteBook">
            <input message="tns:deleteBookRequest"/>
            <output message="tns:deleteBookResponse"/>
        </operation>
    </portType>

    <binding name="BookServiceBinding" type="tns:BookServicePortType">
        <soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>
        <operation name="getAllBooks">
            <soap:operation soapAction="urn:BookService#getAllBooks"/>
            <input><soap:body use="encoded" namespace="urn:BookService" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input>
            <output><soap:body use="encoded" namespace="urn:BookService" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output>
        </operation>
        <operation name="getBook">
            <soap:operation soapAction="urn:BookService#getBook"/>
            <input><soap:body use="encoded" namespace="urn:BookService" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input>
            <output><soap:body use="encoded" namespace="urn:BookService" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output>
        </operation>
        <operation name="getBooksByAuthor">
            <soap:operation soapAction="urn:BookService#getBooksByAuthor"/>
            <input><soap:body use="encoded" namespace="urn:BookService" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input>
            <output><soap:body use="encoded" namespace="urn:BookService" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output>
        </operation>
        <operation name="createBook">
            <soap:operation soapAction="urn:BookService#createBook"/>
            <input><soap:body use="encoded" namespace="urn:BookService" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input>
            <output><soap:body use="encoded" namespace="urn:BookService" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output>
        </operation>
        <operation name="updateBook">
            <soap:operation soapAction="urn:BookService#updateBook"/>
            <input><soap:body use="encoded" namespace="urn:BookService" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input>
            <output><soap:body use="encoded" namespace="urn:BookService" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output>
        </operation>
        <operation name="deleteBook">
            <soap:operation soapAction="urn:BookService#deleteBook"/>
            <input><soap:body use="encoded" namespace="urn:BookService" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input>
            <output><soap:body use="encoded" namespace="urn:BookService" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output>
        </operation>
    </binding>

    <service name="BookService">
        <port name="BookServicePort" binding="tns:BookServiceBinding">
            <soap:address location="$soapUrl"/>
        </port>
    </service>
</definitions>
WSDL;

        return response($wsdl, 200)
            ->header('Content-Type', 'text/xml; charset=utf-8');
    }
}
