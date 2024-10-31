<?php
function randomize_css_process_tpl($tpl) {
	static $pattern;
	static $replacement;
	if (!isset($pattern)) {
		$pattern = array(
			'#\[COLOR\]#e',
			'#\[HEX([1-6]?)-([\da-fA-F]{1,6})-([\da-fA-F]{1,6})\]#e',
			'#\[HEX1\]#e',
			'#\[HEX2\]#e',
			'#\[HEX3\]#e',
		);

		$replacement = array(
			'sprintf("#%06x", rand(0, 0xffffff))',
			'sprintf("%0$1x", rand(0x$2, 0x$3))',
			'sprintf("%x", rand(0x0, 0xf))',
			'sprintf("%02x", rand(0x0, 0xff))',
			'sprintf("%03x", rand(0x0, 0xfff))',
		);
	}
	$style = '';
	$pattern2 = array();
	$replacement2 = array();
	$colorsets = array();
	$colordet_names = array();
	$lines = explode("\n", $tpl);
	foreach ($lines as $line) {
		if (strpos($line, 'def PALETTE') === 0) {
			list($def, $set) = explode('=', $line, 2);
			$name = substr($def, 4);
			preg_match_all('#(([^{:,\\s]+)\\s*:\\s*([^,:}\\s]+))#', $set, $matches, PREG_SET_ORDER);
#var_dump($matches);
			$colorset_names[] = $name;
			$colorsets[$name] = array();
			foreach ($matches as $match) {
				$colorsets[$name]['pattern'][] = '#\[PALETTE.' . preg_quote($match[2]) . '\]#';
				$colorsets[$name]['replacement'][] = $match[3];
			}
			#$pattern2[] = '#\[' . preg_quote($key) . '\]#';
			#$replacement2[] = preg_replace($pattern, $replacement, $val);
		}
		elseif (strpos($line, 'def COLOR') === 0) {
			list($def, $val) = explode('=', $line, 2);
			$key = substr($def, 4);
			$pattern2[] = '#\[' . preg_quote($key) . '\]#';
			$replacement2[] = preg_replace($pattern, $replacement, $val);
		}
		else {
			$style .= $line . "\n";
		}
	}
#var_dump($colorsets);
#echo $colorset;
#var_dump($pattern2);
#var_dump($replacement2);
#echo $tpl2;
#exit;
	if (count($colorsets)) {
		$colorset = $colorset_names[rand(0, count($colorsets)-1)];
		$style = preg_replace($colorsets[$colorset]['pattern'], $colorsets[$colorset]['replacement'], $style);
	}
	$style = preg_replace($pattern2, $replacement2, $style);
	$style = preg_replace($pattern, $replacement, $style);
	return $style;
}

function randomize_css_process_tpl_dir() {
	$files = array();
	$dh = opendir('templates/css');
	while (false !== ($file = readdir($dh))) {
		if (!preg_match('#\.css\.tpl$#', $file))
			continue;
		$files[] = 'templates/css/' . $file;
	}
	closedir($dh);
	sort($files);

	$style = '';
	foreach ($files as $file) {
		$tpl = file_get_contents($file);
		$style .= randomize_css_process_tpl($tpl);
	}
	return $style;
}
