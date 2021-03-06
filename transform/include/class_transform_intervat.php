<?php

/*
 * Copyright (C) 2014 dany
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
require_once 'class_transform_declarant.php';
require_once 'class_transform_representative.php';
require_once 'class_transform_client.php';


/**
 * Description of class_intervat
 *
 * @author dany
 */
class Transform_Intervat
{

    /**
     * Content the XML Document
     * @var DOMDocument
     */
    var $domdoc;

    function __construct()
    {
        $this->domdoc = new DOMDocument('1.0', 'ISO-8859-1');
        $this->ns = 'http://www.minfin.fgov.be/ClientListingConsignment';
        $this->domdoc->formatOutput = true;
    }
    
    function append_root()
    {
        $ns = 'http://www.minfin.fgov.be/ClientListingConsignment';
        $t = $this->domdoc->createElementNS($this->ns, 'ns2:ClientListingConsignment');

        $root = $this->domdoc->appendChild($t);
        $xmls = $root->setAttribute("xmlns", "http://www.minfin.fgov.be/InputCommon");

        $listing = $this->domdoc->appendChild($root);

        $nb = $this->domdoc->createAttribute('ClientListingsNbr');
        $nb->value = 1;
        $listing->appendChild($nb);

        $this->domdoc->appendChild($root);
    }

    /**
     * Creation du mandataire
     @code
      	<ns2:Representative>
			<RepresentativeID identificationType="TIN" issuedBy="BE">0000000097</RepresentativeID>
			<Name>Gevolmachtigde- Mandataire TEST NV-SA </Name>
			<Street>AV Test-laan 8</Street>
			<PostCode>9999</PostCode>
			<City> TESTCITY</City>
			<CountryCode>BE</CountryCode>
			<EmailAddress>something@something.be</EmailAddress>
			<Phone>02555555</Phone>
		</ns2:Representative>
     @endcode
     */
    function append_representative(Transform_Representative $p_representative)
    {
        /*
         * Variables
         */
        

        $representative = $this->domdoc->createElementNS($this->ns, "ns2:Representative");
        $rep_id = $this->domdoc->createElement("RepresentativeID");
        $at = $rep_id->setAttribute('identificationType', $p_representative->type);
        $ai = $rep_id->setAttribute('issuedBy', $p_representative->issued);
        $value = $this->domdoc->createTextNode($p_representative->id);
        $rep_id->appendChild($value);
        $representative->appendChild($rep_id);
        $representative->appendChild($this->domdoc->createElement("Name", $p_representative->name));
        $representative->appendChild($this->domdoc->createElement("Street", $p_representative->street));
        $representative->appendChild($this->domdoc->createElement("PostCode", $p_representative->postcode));
        $representative->appendChild($this->domdoc->createElement("City", $p_representative->city));
        $representative->appendChild($this->domdoc->createElement("CountryCode", $p_representative->countrycode));
        $representative->appendChild($this->domdoc->createElement("EmailAddress", $p_representative->email));
        $representative->appendChild($this->domdoc->createElement("Phone", $p_representative->phone));

        $l = $this->domdoc->getElementsByTagNameNS($this->ns, "ClientListingConsignment");
        $nb = $l->length;
        if ($nb <> 1)
            die('erreur non trouvé');

        $root = $l->item(0);
        $root->appendChild($representative);
    }
    /**
     * Add the listing
     * 
     @code 
      <ns2:ClientListing VATAmountSum="00.00" TurnOverSum="1000.72" ClientsNbr="2" SequenceNumber="1">
     @endcode
     * 
     */
    function append_client_listing(Transform_Declarant $p_declarant)
    {
        /*
         * Fetch from intervat_client
         */
        $client=new Transform_Client;
        $client->compute_value($p_declarant->data->d_id);
        
        // variable
        $vat_amount_sum = $client->vat_amount_sum;
        $turnoversum = $client->turnoversum;
        $clientnb = $client->nb;
        $seqnb = 1;
        $periode = $p_declarant->data->d_periode;
        $commentaire="";

        $decl = $this->domdoc->createElementNS($this->ns, "ns2:ClientListing");
        $ai = $decl->setAttribute('VATAmountSum', sprintf('%8.2f',$vat_amount_sum));
        $ai = $decl->setAttribute('TurnOverSum', sprintf("%8.2f",$turnoversum));
        $ai = $decl->setAttribute('ClientsNbr', $clientnb);
        $ai = $decl->setAttribute('SequenceNumber', $seqnb);



        $this->append_declarant($decl, $p_declarant);

        $periode = $this->domdoc->createElementNS($this->ns, "ns2:Period", $p_declarant->year);
        $decl->appendChild($periode);

        $this->append_listing($decl, $client);


        $l = $this->domdoc->getElementsByTagNameNS($this->ns, "ClientListingConsignment");
        $nb = $l->length;
        if ($nb <> 1)
            die('erreur non trouvé');

        $root = $l->item(0);
        $root->appendChild($decl);
        $decomment = $this->domdoc->createElementNS($this->ns, "ns2:Comment",$commentaire);
        $decl->appendChild($decomment);
    }
    /*
     * Add the "Declarant"
     * @code
     <ns2:Declarant>
            <VATNumber>0000000097</VATNumber>
            <Name>TEST NV-SA</Name>
            <Street>Av Test-laan 16</Street>
            <PostCode>9999</PostCode>
            <City>TESTCITY</City>
            <CountryCode>BE</CountryCode>
            <EmailAddress>cedric@francis.be</EmailAddress>
            <Phone>025555555</Phone>
        </ns2:Declarant>
     @endcode
     */
    function append_declarant(DOMElement $p_dom, Transform_Declarant $p_declarant)
    {
  

        $declarant = $this->domdoc->createElementNS($this->ns, "ns2:Declarant");
        $declarant->appendChild($this->domdoc->createElement("VATNumber", $p_declarant->vatnumber));
        $declarant->appendChild($this->domdoc->createElement("Name", $p_declarant->name));
        $declarant->appendChild($this->domdoc->createElement("Street", $p_declarant->street));
        $declarant->appendChild($this->domdoc->createElement("PostCode", $p_declarant->postcode));
        $declarant->appendChild($this->domdoc->createElement("City", $p_declarant->city));
        $declarant->appendChild($this->domdoc->createElement("CountryCode", $p_declarant->countrycode));
        $declarant->appendChild($this->domdoc->createElement("EmailAddress", $p_declarant->email));
        $declarant->appendChild($this->domdoc->createElement("Phone", $p_declarant->phone));
        $p_dom->appendChild($declarant);
    }
    /** 
     * Add all the customers
     * @code
        <ns2:Client SequenceNumber="1">
          <ns2:CompanyVATNumber issuedBy="BE">0000000097</ns2:CompanyVATNumber>
          <ns2:TurnOver>500.36</ns2:TurnOver>
          <ns2:VATAmount>0.00</ns2:VATAmount>
          </ns2:Client>
          <ns2:Client SequenceNumber="2">
          <ns2:CompanyVATNumber issuedBy="BE">0000000097</ns2:CompanyVATNumber>
          <ns2:TurnOver>500.36</ns2:TurnOver>
          <ns2:VATAmount>0.00</ns2:VATAmount>
          </ns2:Client>
     * @endcode
     * @param DOMElement $p_dom
     * @param type $p_array
     */

    function append_listing(DOMElement $p_dom, Transform_Client $p_client)
    {
        /*
         * Client are in array $p_client->array
         */
        
        for ($i = 0; $i < $p_client->nb; $i++)
        {
            $client=$p_client->array[$i];
            $dom_client = $this->domdoc->createElementNS($this->ns, "ns2:Client");
            $ai = $dom_client->setAttribute('SequenceNumber', $i+1);
            $company = $this->domdoc->createElementNS($this->ns, "ns2:CompanyVATNumber");
            $company->setAttribute('issuedBy', $client['c_issuedby']);
            $de_vat_number = $this->domdoc->createTextNode($client['c_vatnumber']);
            $company->appendChild($de_vat_number);
            $dom_client->appendChild($company);
            $dom_client->appendChild($this->domdoc->createElementNS($this->ns,"ns2:TurnOver", sprintf("%8.2f",$client['c_amount_novat'])));
            $dom_client->appendChild($this->domdoc->createElementNS($this->ns,"ns2:VATAmount", sprintf("%8.2f",$client['c_amount_vat'])));
            $p_dom->appendChild($dom_client);
        }
        
    }
    function toxml()
    {
        $str=$this->domdoc->saveXML();
        return $str;
    }

}
