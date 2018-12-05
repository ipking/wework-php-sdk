<?php

use WeWork\Core\ServiceProviderAPI;
use WeWork\Model\ServiceProvider\GetRegisterCodeReq;
use WeWork\Model\ServiceProvider\SetAgentScopeReq;

include_once ('../autoload.php');
 
try {
    $ServiceProviderAPI = new ServiceProviderAPI(
        "CORPID", 
        "PROVIDER_SECRET"
    );
    //

    //
    $GetRegisterCodeReq = new GetRegisterCodeReq();
    {
        $GetRegisterCodeReq->template_id = "template_id";
        $GetRegisterCodeReq->corp_name = "corp_name";
    }
    $register_code = $ServiceProviderAPI->GetRegisterCode($GetRegisterCodeReq);
    var_dump($register_code);

    //
    $GetLoginInfoRsp = $ServiceProviderAPI->GetLoginInfo("xxxxxxxxxxxxxx");
} catch (Exception $e) { 
    echo $e->getMessage() . "\n";
}

try {
    $ServiceProviderAPI = new ServiceProviderAPI();

    $access_token = "xxxxxxxxxxxxxx";
    //
    $SetAgentScopeReq = new SetAgentScopeReq();
    {
        $SetAgentScopeReq->agentid = 11111111;
    }
    $SetAgentScopeRsp = $ServiceProviderAPI->SetAgentScope($access_token, $SetAgentScopeReq);
    var_dump($SetAgentScopeRsp);

    //
    $ServiceProviderAPI->SetContactSyncSuccess($access_token);
} catch (Exception $e) { 
    echo $e->getMessage() . "\n";
}

