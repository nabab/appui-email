<!-- HTML Document -->

<?php
/* Static classes xx and st are available as aliases of bbn\X and bbn\Str respectively */
?>

<div class="sync">
  <hr>
  <h2>
    Running folder
  </h2>
  <div class="bbn-w-70 " v-for="folder in sync.running">
    <div>
      <span>{{folder.account_name}}</span>
      <span>{{folder.folder_name}}</span>
    </div>
    <bbn-progressbar :max="folder.msg"
                     :value="folder.db_msg"
                     :step="1"></bbn-progressbar>
  </div>
  <hr>
  <h2>
    Finished folder
  </h2>
  <div class="bbn-w-70 " >
    <ul>
      <li v-for="account in sync.finished">
      	{{account.name}}
        <ul>
          <li v-for="folder in account.folders">
          	{{folder.folder_name}}
            <bbn-progressbar :max="folder.msg"
                     :value="folder.db_msg"
                     :step="1"
   									 height="10px"></bbn-progressbar>
          </li>
        </ul>
      </li>
    </ul>
  </div>
  <hr>
  <h2>
    Not started folder
  </h2>
  <div class="bbn-w-70 " v-for="folder in sync['not started']">
    <div>
      <span>{{folder.account_name}}</span>
      <span>{{folder.folder_name}}</span>
    </div>
    <bbn-progressbar :max="folder.msg"
                     :value="folder.db_msg"
                     :step="1"></bbn-progressbar>
  </div>
</div>