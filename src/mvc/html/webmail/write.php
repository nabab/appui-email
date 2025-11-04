<!-- HTML Document -->
<appui-email-write :source="source.email"
                  :subject="source.subject"
                  :to="source.to"
                  :account="source.email?.id_account"
                  :accounts="source.accounts"
                  :signatures="source.signatures"
                  :attachment="source.attachment"
                  :is-reply="source.isReply"
                  :reply-to="source.reply_to"
                  :references="source.references"/>

