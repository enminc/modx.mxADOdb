<?php
$mxadodb = $modx->getService('mxadodb','mxadodb',MODX_CORE_PATH.'components/mxadodb/model/mxadodb/',array('debug'=>true));
$myconn  = $mxadodb->init();

$modx->toPlaceholder('mxadodb_connections',$modx->toJSON($mxadodb->listConnections()));

$contexts = $myconn->GetArray('SELECT * FROM  `modx_context` LIMIT 0 , 30');

$modx->toPlaceholder('mxadodb_contexts',$modx->toJSON($contexts));

return '';