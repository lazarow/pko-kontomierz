<?php
require __DIR__ . '/common.php';

$rules = include __DIR__ . '/rules.php';
$categories = [
	'food' => [
		'name' => 'Jedzenie',
		'sum' => 0,
		'items' => []
	],
	'drugs_and_cosmetics' => [
		'name' => 'Lekarstwa i kosmetyki (ew. kosmetyczka)',
		'sum' => 0,
		'items' => []
	],
	'clothes' => [
		'name' => 'Ubrania',
		'sum' => 0,
		'items' => []
	],
	'charges' => [
		'name' => 'Opłaty',
		'sum' => 0,
		'items' => []
	],
	'cash_machine' => [
		'name' => 'Bankomat',
		'sum' => 0,
		'items' => []
	],
	'other' => [
		'name' => 'Inne (nieskasyfikowane)',
		'sum' => 0,
		'items' => []
	],
];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_FILES['abstract']) && $_FILES['abstract']['error'] === UPLOAD_ERR_OK) {
		$handle = fopen($_FILES['abstract']['tmp_name'], 'r');
		if ($handle) {
			$first = true;
			while (($line = fgets($handle)) !== false) {
				$line = w1250_to_utf8($line);
				if (mb_strlen($line) === 0) {
					continue;
				}
				if ($first) {
					$first = false;
					continue;
				}
				$category = 'other';
				$data = array_map('trim', str_getcsv($line, ',', '"'));
				if ((float) $data[3] >= 0) {
					continue;
				}
				$line = mb_strtolower($line);
				foreach ($rules['match'] as $pattern => $target) {
					if (strpos($line, $pattern) !== false) {
						$category = $target;
					}
				}
				$categories[$category]['sum'] += abs((float) $data[3]);
				$categories[$category]['items'][] = [
					'date' => $data[0],
					'amount' => (float) $data[3],
					'description' => $data[7] . ' ' . $data[8]
				];
			}
			fclose($handle);
		}
	}
}
?><!DOCTYPE html>
<html lang="pl">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="icon" href="assets/favicon.ico">
	<title>Kontomierz</title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<link rel="stylesheet" href="assets/css/theme.css">
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<form class="form-inline" method="post" enctype="multipart/form-data">
					<div class="form-group">
						<input type="file" id="file-input" name="abstract" class="form-control">
					</div>
					<button type="submit" class="btn btn-primary">Wyślij wyciąg</button>
				</form>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<div class="panel panel-default">
					<div class="panel-heading">Podsumowanie</div>
					<table class="table">
						<tbody>
							<?php
							foreach ($categories as $category) {
								echo '<tr><td>' . $category['name'] . '</td>';
								echo '<td style="width: 1%">' . number_format($category['sum'], 2, ',', '') . '&nbsp;zł</td>';
								echo '<td style="width: 1%">' . count($category['items']) . '</td></tr>';
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php
		function cmp($a, $b) {
			if ($a['amount'] == $b['amount']) {
				return 0;
			}
			return ($a['amount'] < $b['amount']) ? -1 : 1;
		}
		foreach ($categories as $category) {
			echo '<div class="panel panel-default"><div class="panel-heading">' . $category['name'] . '</div>';
			echo '<table class="table"><tbody>';
			usort($category['items'], "cmp");
			foreach ($category['items'] as $item) {
				echo '<tr><td style="width: 1%; white-space: nowrap">' . $item['date'] . '</td>';
				echo '<td style="width: 1%">' . number_format($item['amount'], 2, ',', '') . '&nbsp;zł</td>';
				echo '<td>' . $item['description'] . '</td></tr>';
			}
			echo '</tbody></table>';
			echo '</div>';
		}
		?>
	</div>
</body>
</html>