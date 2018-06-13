<?php

class xtecmailerror extends Exception {
}

class xtecmail {

    private $allowed_senders = array(
        'xtec' => 'correus_aplicacions.educacio@xtec.cat',
        'gencat' => 'correus_aplicacions.educacio@gencat.cat',
        'educacio' => 'apligest@correueducacio.xtec.cat',
    );


    private $allowed_environments = array(
        'INT' => 'http://integracio.bus.ensenyament.intranet.gencat.cat/event/ServeisComuns/intern/EnviaCorreu/a1/EnviaCorreu',
        'PRE' => 'http://preproduccio.bus.ensenyament.intranet.gencat.cat/event/ServeisComuns/intern/EnviaCorreu/a1/EnviaCorreu',
        'PRO' => 'http://bus.ensenyament.intranet.gencat.cat/event/ServeisComuns/intern/EnviaCorreu/a1/EnviaCorreu'
    );


    private $idApp;
    private $sender;
    private $environment;
    private $wsdl;

    function __construct($idApp, $sender='educacio', $environment='PRO') {
        if (!isset($this->allowed_senders[$sender])) {
            throw new xtecmailerror('invalid sender');
        }
        if (!isset($this->allowed_environments[$environment])) {
            throw new xtecmailerror('invalid environment');
        }
        $this->idApp = $idApp;
        $this->sender = $this->allowed_senders[$sender];
        $this->environment = $this->allowed_environments[$environment];
        $this->wsdl = dirname(__FILE__) . '/wsdl-' . $environment . '.wsdl';
    }

    public function test() {
        $request = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"';
        $request .= ' xmlns:cor="http://www.gencat.cat/educacio/sscc/correu">';
        $request .= '<soapenv:Body>';
        $request .= '<cor:PeticioDisponibilitat>';
        $request .= '<from>'.$this->sender.'</from>';
        $request .= '</cor:PeticioDisponibilitat>';
        $request .= '</soapenv:Body>';
        $request .= '</soapenv:Envelope>';
        try {
            $response = $this->soap_call($request, 'disponibilitat');
        } catch (xtecmailerror $e) {
            return false;
        }
        $response = $response->RespostaDisponibilitat[0]->children();
        return $response->status == 'OK';
    }

    public function send($to=array(), $cc=array(), $bcc=array(), $replyAddress, $subject='',
                  $bodyContent='', $bodyType='text/plain', $attachments=array()) {
        $request = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"';
        $request .= ' xmlns:cor="http://www.gencat.cat/educacio/sscc/correu">';
        $request .= '<soapenv:Body>';
        $request .= '<cor:PeticioEnviament>';
        $request .= '<idApp>'.$this->idApp.'</idApp>';
        $request .= '<correu>';
        $request .= '<from>'.$this->sender.'</from>';
        $request .= '<replyAddresses><address>'.$replyAddress.'</address></replyAddresses>';
        $request .= '<destinationAddresses>';
        foreach ($to as $address) {
            $request .= '<destination><address>'.$address.'</address><type>TO</type></destination>';
        }
        foreach ($cc as $address) {
            $request .= '<destination><address>'.$address.'</address><type>CC</type></destination>';
        }
        foreach ($bcc as $address) {
            $request .= '<destination><address>'.$address.'</address><type>BCC</type></destination>';
        }
        $request .= '</destinationAddresses>';
        $request .= '<subject><![CDATA['.$subject.']]></subject>';
        $request .= '<mailBody>';
        $request .= '<bodyType>'.$bodyType.'</bodyType>';
        $request .= '<bodyContent><![CDATA['.$bodyContent.']]></bodyContent>';
        $request .= '</mailBody>';
        if ($attachments) {
            $request .= '<attachments>';
            foreach ($attachments as $attachment) {
                $request .= '<attachment>';
                $request .= '<fileName><![CDATA['.$attachment['filename'].']]></fileName>';
                $request .= '<attachmentContent>';
                $request .= '<fileContent>'.base64_encode($attachment['content']).'</fileContent>';
                $request .= '<mimeType>'.$attachment['mimetype'].'</mimeType>';
                $request .= '</attachmentContent>';
                $request .= '</attachment>';
            }
            $request .= '</attachments>';
        }
        $request .= '</correu>';
        $request .= '</cor:PeticioEnviament>';
        $request .= '</soapenv:Body>';
        $request .= '</soapenv:Envelope>';
        $response = $this->soap_call($request, 'enviament');
        $response = $response->RespostaEnviament[0]->children();
        if ($response->status == 'KO') {
            throw new xtecmailerror(!empty($response->message) ? $response->message : '');
        }
        foreach ($response->respostaCorreu as $r) {
            if ($r->status == 'KO') {
                throw new xtecmailerror(!empty($r->message) ? $r->message : '');
            }
        }
    }

    private function soap_call($request, $action) {
        $loadentities = libxml_disable_entity_loader(false);
        try {
            $client = new SoapClient($this->wsdl, array('trace' => 1, 'connection_timeout' => 120));
            $response = $client->__doRequest($request, $this->environment.'?wsdl', $action, SOAP_1_1, 0);
        } catch (SoapFault $e) {
            throw new xtecmailerror($e->faultstring);
        }
        libxml_disable_entity_loader($loadentities);
        if (!$response) {
            throw new xtecmailerror('empty response');
        }
        $response = simplexml_load_string($response);
        if ($response === false) {
            throw new xtecmailerror('invalid response');
        }
        $ns = $response->getNamespaces(true);
        $ns = array_keys($ns);
        $response = $response->children($ns[0], true);
        if (isset($response->Body[0]->Fault[0])) {
            throw new xtecmailerror($response->Body[0]->Fault[0]->children()->faultstring);
        }
        return $response->Body[0]->children($ns[1], true);
    }
}
