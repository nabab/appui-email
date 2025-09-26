<bbn-router :nav="true">
	<bbns-container component="appui-email-webmail"
                 :label="_('Home')"
                 :source="source"
                 url="home"
                 :closable="false"
                 :pinned="true"/>
</bbn-router>