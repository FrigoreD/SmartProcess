<?php

if (!check_bitrix_sessid()) {
    return;
}

if ($ex = $APPLICATION->GetException()) {
    echo CAdminMessage::ShowMessage([
        'TYPE'   =>'ERROR',
        'MESSAGE'=>GetMessage('MOD_INST_ERR'),
        'DETAILS'=>$ex->GetString(),
        'HTML'   =>true,
    ]);
} else {
    echo CAdminMessage::ShowNote('OK');
}

?>
<form action='<?= $APPLICATION->GetCurPage()?>'>
    <input type='hidden' name='lang' value='<?= LANG?>'>
    <input type='submit' value='<?= GetMessage('MOD_BACK')?>'>
    <form>
