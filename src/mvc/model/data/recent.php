<?php

/** @var $this \bbn\Mvc\Model*/
$mailings = new \bbn\Appui\Mailing($model->db);
return ['last' => $mailings->getLasts(), 'next' => $mailings->getNexts(), 'sendings' => $mailings->getSendings()];