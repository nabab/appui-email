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
    <ul>
      <li v-for="account in sync.running">
        {{account.name}}
        <ul>
          <li v-for="folder in account.folders">
            {{folder.folder_name}}
            <bbn-progressbar class="progress_bar"
                             :showValue="false"
                             :max="folder.msg"
                             :value="folder.db_msg"
                             :step="1"></bbn-progressbar>
            {{folder.db_msg + '/' + folder.msg}}
          </li>
        </ul>
      </li>
    </ul>
  </div>
  <hr>
  <h2>
    Sync folder
  </h2>
  <div class="bbn-w-70 " >
    <ul>
      <li v-for="account in sync.finished">
        {{account.name}}
        <ul>
          <li v-for="folder in account.folders">
            {{folder.folder_name}}
            <bbn-progressbar class="progress_bar"
                             :showValue="false"
                             :max="folder.msg"
                             :value="folder.db_msg"
                             :step="1"></bbn-progressbar>
            {{folder.db_msg + '/' + folder.msg}}
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
    <ul>
      <li v-for="account in sync['not started']">
        {{account.name}}
        <ul>
          <li v-for="folder in account.folders">
            {{folder.folder_name}}
            <bbn-progressbar class="progress_bar"
                             :showValue="false"
                             :max="folder.msg"
                             :value="folder.db_msg"
                             :step="1"></bbn-progressbar>
            {{folder.db_msg + '/' + folder.msg}}
          </li>
        </ul>
      </li>
    </ul>
  </div>
</div>