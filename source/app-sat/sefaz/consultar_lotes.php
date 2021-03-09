<?php
    $data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
    xmlns:cfec="http://www.fazenda.sp.gov.br/sat/wsdl/CfeConsultaLotes">
        <soapenv:Header>
            <cfec:cfeCabecMsg>
                <!--Optional:-->
                <cfec:cUF>35</cfec:cUF>
                <!--Optional:-->
                <cfec:versaoDados>0.06</cfec:versaoDados>
            </cfec:cfeCabecMsg>
        </soapenv:Header>
        <soapenv:Body>
        <cfec:CfeConsultarLotes>
            <!--Optional:-->
            <cfec:cfeDadosMsg>
            <![CDATA[
            <consLote xmlns="http://www.fazenda.sp.gov.br/sat" versao="0.06">
            <nserieSAT>000230839</nserieSAT>
            <dhInicial>01032020000000</dhInicial>
            <dhFinal>31032020004400</dhFinal>
            <chaveSeguranca>fcbb10c2-958e-425c-b607-b4885e03fbd2</chaveSeguranca>
            </consLote>
            ]]>
        </cfec:cfeDadosMsg>
        </cfec:CfeConsultarLotes>
        </soapenv:Body>
    </soapenv:Envelope>';
    
    
    $client = new SoapClient("https://wssatsp.fazenda.sp.gov.br/CfeConsultarLotes/CfeConsultarLotes.asmx?WSDL");

    $param = array ('');
    
    $result = $client->cfeCabecMsg($param);
    print_r($result);
?>