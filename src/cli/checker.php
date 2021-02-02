<?php
/**
 *  Describe what it does or you're a pussy
 *
 **/
$user = false;
$id_accounts = $ctrl->inc->options->fromCode('accounts', 'email', 'appui');

$accounts = $ctrl->inc->pref->getAll($id_accounts);