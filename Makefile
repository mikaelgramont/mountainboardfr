CSS_PATH=public/css/default/
JS_PATH=public/js/
SITE=http://www.mountainboard.fr/

up: update minifycss compilejs assets
	svn commit $(CSS_PATH)style.full.css $(CSS_PATH)style.full.min.css $(JS_PATH)general.js -m'make up commit'
	curl "$(SITE)admin/generate-asset-versions?authCheck=ra45HuiB@"

update:
	svn up

assets:
	cd public && git ls-tree -r master |php ../bin/gitassets.php > ../data/lookupTable.php

cleancache:
	curl "$(SITE)admin/clear-memcache?authCheck=ra45HuiB@&mode=user"
	curl "$(SITE)admin/clear-memcache?authCheck=ra45HuiB@&mode=opcode"
	rm -rf data/cache/z* data/cache/z*
	rm -rf data/cache/z* data/cache/app/z*


minifycss:
	rm $(CSS_PATH)style.full.css
	cat $(CSS_PATH)main.css $(CSS_PATH)slider.css $(CSS_PATH)jquery-ui.css $(CSS_PATH)jquery-ui-custom.css > $(CSS_PATH)style.full.css

minifyjs:
	java -jar library/yuicompressor/build/yuicompressor-2.4.2.jar --type js -o $(JS_PATH)autocomplete.min.js $(JS_PATH)autocomplete.js
	java -jar library/yuicompressor/build/yuicompressor-2.4.2.jar --type js -o $(JS_PATH)jquery.tablesorter.min.js $(JS_PATH)jquery.tablesorter.js
	java -jar library/yuicompressor/build/yuicompressor-2.4.2.jar --type js -o $(JS_PATH)jquery.tagbox.min.js $(JS_PATH)jquery.tagbox.js
	java -jar library/yuicompressor/build/yuicompressor-2.4.2.jar --type js -o $(JS_PATH)libFacebookUpload.min.js $(JS_PATH)libFacebookUpload.js
	java -jar library/yuicompressor/build/yuicompressor-2.4.2.jar --type js -o $(JS_PATH)libForm.min.js $(JS_PATH)libForm.js
	java -jar library/yuicompressor/build/yuicompressor-2.4.2.jar --type js -o $(JS_PATH)libMaps.min.js $(JS_PATH)libMaps.js
	java -jar library/yuicompressor/build/yuicompressor-2.4.2.jar --type js -o $(JS_PATH)libChat.min.js $(JS_PATH)libChat.js
	java -jar library/yuicompressor/build/yuicompressor-2.4.2.jar --type js -o $(JS_PATH)jquery.cookie.min.js $(JS_PATH)jquery.cookie.js

compilejs: minifyjs
	cat $(JS_PATH)swfobject/2.1/swfobject.js \
		\
		$(JS_PATH)uploadify/2.1.0/uploadify.min.js \
		\
		$(JS_PATH)libForm.min.js \
		\
		$(JS_PATH)autocomplete.min.js \
		\
		$(JS_PATH)jquery.tablesorter.min.js \
		\
		$(JS_PATH)jquery.tagbox.min.js \
		\
		$(JS_PATH)libFacebookUpload.min.js \
		\
                $(JS_PATH)libChat.min.js \
                \
		$(JS_PATH)yepnope.min.js \
		\
		$(JS_PATH)jquery.cookie.min.js \
			> $(JS_PATH)general.js	
	
full-backup: files-backup sql-backup

files-backup:
	tar czvf ./data/backups/files.tgz application bin library public tests tools

sql-backup:
	mysqldump --user=mountainboardfr -p --database mountainboardfr_production > ./data/backups/dump.sql
	
