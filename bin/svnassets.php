<?php
$status = simplexml_load_file("php://stdin");

$total = count($status->target->entry);

$matches = array(
	'/\.css$/',
	'/\.js$/',
	'/\.png$/',
	'/\.jpg$/',
	'/\.gif$/'
);

$items = array();

for ($i = 0; $i < $total; $i++) {
	$entry = $status->target->entry[$i];
	$path = (string) $entry['path'];

	if(substr($path, 0, 6) != 'public'){
		continue;
	}

	$path = str_replace('public/', '', $path);

	$found = false;

	foreach ($matches as $match) {
		if (preg_match($match, $path)) {
			$found = true;
			break;
		}
	}

	if ($found) {
		$children = $entry->children();

		if (count($children) == 0 ) {
			continue;
		}

		$wc_status = $children[0];

		$commit = $wc_status->commit;

		if ($commit) {
			$revision = $commit['revision'];
			$modified = $wc_status['item'] == 'modified';

			$items[$path] = (string) $revision;
		}
	}
}

$sorted_items = array();

$paths = array_keys($items);
sort($paths, SORT_STRING);

foreach ($paths as $path) {
	$fullpath = '/'.$path;
	$path_parts = pathinfo($fullpath);
	$versionned = $path_parts['dirname'] . '/' . $path_parts['filename'] . '.v' . $items[$path] . '.' . $path_parts['extension'];
	$sorted_items[$fullpath] = $versionned;
}

echo '<?php $assetVersions = '.var_export($sorted_items, true).';';
