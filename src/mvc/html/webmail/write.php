<!-- HTML Document -->
<appui-email-webmail-write :source="source.email"
                           :subject="source.subject"
                           :to="source.to"
                           :account="source.email?.id_account"
                           :accounts="source.accounts"
                           :signatures="source.signatures"
                           :attachment="source.attachments"
                           :is-reply="source.isReply"
                           :reply-to="source.reply_to"
                           :references="source.references"
                           :entities="source.entities"/>

