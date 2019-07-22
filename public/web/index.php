<?php
require_once __DIR__.'/../vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$container = new \Slim\Container;

$config = [
    'settings' => [
        'displayErrorDetails' => true
    ],
];
$app = new \Slim\App($config);
//$app = new \Slim\App;

function wkb_to_json($wkb) {
    $geom = geoPHP::load($wkb,'wkb');
    return $geom->out('json');
}

// $dsn = getenv('MYSQL_DSN');
// $user = getenv('MYSQL_USER');
// $password = getenv('MYSQL_PASSWORD');
// if (!isset($dsn, $user) || false === $password) {
//       	throw new Exception('Set MYSQL_DSN, MYSQL_USER, and MYSQL_PASSWORD environment variables');
//   	}

$container = $app->getContainer();
$container['pdo'] = function ($container) {
	// Connect to database using PDO
	try
	{
    	// $pdo = new PDO('mysql:unix_socket=/cloudsql/qlue-database:us-central1:qlue-master;dbname=qluein;charset=utf8', 'root', 'qlue7654');
    	$pdo = new PDO('mysql:host=130.211.200.48;dbname=qluein;charset=utf8', 'root', 'qlue7654');
	}
	catch (PDOException $e)
	{
	    echo 'Error: ' . $e->getMessage();
	    exit();
	}
	return $pdo;

};

/**
 * @api {get} /adm_0/list Request List
 * @apiName GetCountryList
 * @apiGroup Country
 */

$app->get('/ivan', function ($request, $response) {
		echo "Test";
});

$app->get('/adm_0', function (Request $request, Response $response, array $args) {
	$lat = $request->getQueryParam('lat');
	$lng = $request->getQueryParam('lng');

	$pdo = $this->get('pdo');

	// Run Query
	$sql = "SELECT code_adm_0,adm_0,iso_3digit,center_lat,center_lng FROM qlue_adm_0 WHERE ST_Contains(SHAPE, ST_GeomFromText('POINT(".$lng." ".$lat.")', 4326))";
	$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

	// Error check statement
	if (!$result) {
	    echo "\nPDO::errorInfo():\n";
	    print_r($pdo->errorInfo());
	}
	// Execute query
	$result->execute();
	//print_r($result->errorInfo());
	if ($row = $result->fetch()) {
		$data = array(	'status' => 'OK',
						'code_adm_0' => $row['code_adm_0'],
						'adm_0' => $row['adm_0'],
						'iso_3digit' => $row['iso_3digit'],
						'center_lat' => $row['center_lat'],
						'center_lng' => $row['center_lng']
					);
	} else {
		$data = array('status' => 'ZERO_RESULTS');
	}
	// Close connection
	$pdo = null;

    return $response->withJson($data);;
});


/**
 * @api {get} /adm_0/center Center Latitude and Longitude
 * @apiName GetCountryCenterLatLng
 * @apiGroup Country
 *
 * @apiParam {String} name Name of the country
 * @apiParam (Query string) {String} name Name of the country
 */

$app->get('/adm_0/center', function (Request $request, Response $response, array $args) {
	$country = $request->getQueryParam('name');

	$pdo = $this->get('pdo');

	// Run Query
	$sql = "SELECT code_adm_0,adm_0,iso_3digit,center_lat,center_lng FROM qlue_adm_0 WHERE adm_0 LIKE '%".$country."%' LIMIT 1";
	$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

	// Error check statement
	if (!$result) {
	    echo "\nPDO::errorInfo():\n";
	    print_r($pdo->errorInfo());
	}

	// Execute query
	$result->execute();
	//print_r($result->errorInfo());
	if ($row = $result->fetch()) {
		$data = array(	'status' => 'OK',
						'code_adm_0' => $row['code_adm_0'],
						'adm_0' => $row['adm_0'],
						'iso_3digit' => $row['iso_3digit'],
						'center_lat' => $row['center_lat'],
						'center_lng' => $row['center_lng']
					);
	} else {
		$data = array('status' => 'ZERO_RESULTS');
	}

	// Close connection
	$pdo = null;

    return $response->withJson($data);;
});

$app->get('/adm_0/list', function (Request $request, Response $response, array $args) {
	$pdo = $this->get('pdo');

	// Run Query
	$sql = "SELECT code_adm_0,adm_0,iso_3digit,center_lat,center_lng FROM qlue_adm_0 WHERE code_adm_0 IS NOT NULL";
	$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

	// Error check statement
	if (!$result) {
	    echo "\nPDO::errorInfo():\n";
	    print_r($pdo->errorInfo());
	}

	// Execute query
	$result->execute();
	//print_r($result->errorInfo());
	if ($row = $result->fetch()) {
		do {
		$country[] = array(	'code_adm_0' => $row['code_adm_0'],
							'adm_0' => $row['adm_0'],
							'iso_3digit' => $row['iso_3digit'],
							'center_lat' => $row['center_lat'],
							'center_lng' => $row['center_lng']
					);
		} while ($row = $result->fetch());

		$data = array(	'status' => 'OK',
						'result' => $country
					);
	} else {
		$data = array('status' => 'ZERO_RESULTS');
	}
	// Close connection
	$pdo = null;
    return $response->withJson($data);;
});

$app->get('/adm_0/search', function (Request $request, Response $response, array $args) {
	$pdo = $this->get('pdo');

	// Run Query
	$sql = "SELECT code_adm_0,adm_0,iso_3digit FROM qlue_adm_0 WHERE code_adm_0 = '".$code_adm_0."'";
	$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

	// Error check statement
	if (!$result) {
	    echo "\nPDO::errorInfo():\n";
	    print_r($pdo->errorInfo());
	}

	// Execute query
	$result->execute();
	//print_r($result->errorInfo());
	if ($row = $result->fetch()) {
		do {
		$country[] = array(	'code_adm_0' => $row['code_adm_0'],
							'adm_0' => $row['adm_0'],
							'iso_3digit' => $row['iso_3digit']
					);
		} while ($row = $result->fetch());

		$data = array(	'status' => 'OK',
						'result' => $country
					);
	} else {
		$data = array('status' => 'ZERO_RESULTS');
	}
	// Close connection
	$pdo = null;
    return $response->withJson($data);;
});

$app->get('/adm_0/geojson', function (Request $request, Response $response, array $args) {
	$country = $request->getQueryParam('name');

	$pdo = $this->get('pdo');

	// Run Query
	$sql = "SELECT code_adm_0,adm_0,AsWKB(SHAPE) AS wkb FROM qlue_adm_0 WHERE adm_0 LIKE '%".$country."%' LIMIT 1";
	//$sql = "SELECT AsWKB(SHAPE) AS wkb FROM qlue_adm_1 LIMIT 1";

	# Build GeoJSON feature collection array
	$geojson = array(
	   'type'      => 'FeatureCollection',
	   'features'  => array(),

	);

	$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

	// Error check statement
	if (!$result) {
	    echo "\nPDO::errorInfo():\n";
	    print_r($pdo->errorInfo());
	}

	// Execute query
	$result->execute();

	# Loop through rows to build feature arrays
	while ($row = $result->fetch()) {
	    $properties = $row;
	   	$properties = array(
	       'code_adm_0'     => $row['code_adm_0'],
	       'adm_0'  => $row['adm_0'],

	    );

	    # Remove wkb and geometry fields from properties
	    unset($properties['wkb']);
	    unset($properties['SHAPE']);
	    $feature = array(
	         'type' => 'Feature',
	         'geometry' => json_decode(wkb_to_json($row['wkb'])),
	         'properties' => $properties
	    );
	    # Add feature arrays to feature collection array
	    array_push($geojson['features'], $feature);
	}

	// Close connection
	$pdo = null;

	if(function_exists('ob_gzhandler')) ob_start('ob_gzhandler');else ob_start();
    return $response->withJson($geojson,200,JSON_NUMERIC_CHECK);
    ob_end_flush();
});

// $app->get('/adm_1/list', function (Request $request, Response $response, array $args) {
// 	$code_adm_0 = $request->getQueryParam('code_adm_0');

// 	$pdo = $this->get('pdo');

// 	// Run Query
// 	//$sql = "SELECT code_adm_1,adm_1,center_lat,center_lng FROM qlue_adm_1 WHERE code_adm_0 = '".$code_adm_0."'";
// 	$sql = "SELECT code_adm_1,adm_1 FROM qlue_adm_geom WHERE code_adm_0 = '".$code_adm_0."' GROUP BY code_adm_1";
// 	//echo $sql;
// 	$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

// 	// Error check statement
// 	if (!$result) {
// 	    echo "\nPDO::errorInfo():\n";
// 	    print_r($pdo->errorInfo());
// 	}

// 	// Execute query
// 	$result->execute();
// 	//print_r($result->errorInfo());
// 	if ($row = $result->fetch()) {
// 		do {
// 		$adm_1[] = array(	'code_adm_1' => $row['code_adm_1'],
// 							'adm_1' => $row['adm_1'],
// 							//'center_lat' => $row['center_lat'],
// 							//'center_lng' => $row['center_lng']
// 					);
// 		} while ($row = $result->fetch());

// 		$data = array(	'status' => 'OK',
// 						'result' => $adm_1
// 					);
// 	} else {
// 		$data = array('status' => 'ZERO_RESULTS');
// 	}
// 	// Close connection
// 	$pdo = null;
//     return $response->withJson($data);
// });

$app->get('/adm_1/list', function (Request $request, Response $response, array $args) {
	$code_adm_0 = $request->getQueryParam('code_adm_0');

	$pdo = $this->get('pdo');

	// Run Query
	$sql = "SELECT code_adm_1,adm_1,subscribe,subscribe_start,subscribe_end FROM qlue_adm_1 WHERE code_adm_0 = '".$code_adm_0."'";
	$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

	// Error check statement
	if (!$result) {
	    echo "\nPDO::errorInfo():\n";
	    print_r($pdo->errorInfo());
	}

	// Execute query
	$result->execute();
	//print_r($result->errorInfo());
	if ($row = $result->fetch()) {
		do {
		$adm_1[] = array(	'code_adm_1' => $row['code_adm_1'],
							'adm_1' => $row['adm_1'],
							'subscribe' => $row['subscribe'],
							'subscribe_start' => $row['subscribe_start'],
							'subscribe_end' => $row['subscribe_end']
					);
		} while ($row = $result->fetch());

		$data = array(	'status' => 'OK',
						'result' => $adm_1
					);
	} else {
		$data = array('status' => 'ZERO_RESULTS');
	}
	// Close connection
	$pdo = null;
    return $response->withJson($data);
});

$app->get('/qlue/adm_1/list', function (Request $request, Response $response, array $args) {
	$code_adm_0 = $request->getQueryParam('code_adm_0');

	$pdo = $this->get('pdo');

	// Run Query
	$sql = "SELECT code_adm_1,adm_1,division_id FROM adm_1 WHERE code_adm_0 = '".$code_adm_0."'";
	$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

	// Error check statement
	if (!$result) {
	    echo "\nPDO::errorInfo():\n";
	    print_r($pdo->errorInfo());
	}

	// Execute query
	$result->execute();
	//print_r($result->errorInfo());
	if ($row = $result->fetch()) {
		do {
		$adm_1[] = array(	'code_adm_1' => $row['code_adm_1'],
							'adm_1' => $row['adm_1'],
							'division_id' => $row['division_id']
					);
		} while ($row = $result->fetch());

		$data = array(	'status' => 'OK',
						'result' => $adm_1
					);
	} else {
		$data = array('status' => 'ZERO_RESULTS');
	}
	// Close connection
	$pdo = null;
    return $response->withJson($data);
});

$app->get('/adm_1/search', function (Request $request, Response $response, array $args) {
	$code_adm_1 = $request->getQueryParam('code_adm_1');

	$pdo = $this->get('pdo');

	// Run Query
	$sql = "SELECT code_adm_1,adm_1 FROM qlue_adm_1 WHERE code_adm_1 = '".$code_adm_1."' GROUP BY code_adm_1";
	$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

	// Error check statement
	if (!$result) {
	    echo "\nPDO::errorInfo():\n";
	    print_r($pdo->errorInfo());
	}

	// Execute query
	$result->execute();
	//print_r($result->errorInfo());
	if ($row = $result->fetch()) {
		do {
		$adm_1[] = array(	'code_adm_1' => $row['code_adm_1'],
							'adm_1' => $row['adm_1']
					);
		} while ($row = $result->fetch());

		$data = array(	'status' => 'OK',
						'result' => $adm_1
					);
	} else {
		$data = array('status' => 'ZERO_RESULTS');
	}
	// Close connection
	$pdo = null;
    return $response->withJson($data);
});

$app->get('/qlue/adm_1/search', function (Request $request, Response $response, array $args) {
	$code_adm_1 = $request->getQueryParam('code_adm_1');

	$pdo = $this->get('pdo');

	// Run Query
	$sql = "SELECT code_adm_1,adm_1,division_id FROM adm_1 WHERE code_adm_1 = '".$code_adm_1."' GROUP BY code_adm_1";
	$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

	// Error check statement
	if (!$result) {
	    echo "\nPDO::errorInfo():\n";
	    print_r($pdo->errorInfo());
	}

	// Execute query
	$result->execute();
	//print_r($result->errorInfo());
	if ($row = $result->fetch()) {
		do {
		$adm_1[] = array(	'code_adm_1' => $row['code_adm_1'],
							'adm_1' => $row['adm_1'],
							'division_id' => $row['division_id']
					);
		} while ($row = $result->fetch());

		$data = array(	'status' => 'OK',
						'result' => $adm_1
					);
	} else {
		$data = array('status' => 'ZERO_RESULTS');
	}
	// Close connection
	$pdo = null;
    return $response->withJson($data);
});

$app->put('/adm_1', function (Request $request, Response $response, array $args) {
	$code_adm_1 = $request->getQueryParam('code_adm_1');
	$subscribe = $request->getQueryParam('subscribe');
	$subscribe_start = $request->getQueryParam('subscribe_start');
	$subscribe_end = $request->getQueryParam('subscribe_end');

	$pdo = $this->get('pdo');

	// Run Query
	$sql = "UPDATE qlue_adm_1 SET subscribe = '".$subscribe."',subscribe_start = '".$subscribe_start."',subscribe_end =  '".$subscribe_end."' WHERE code_adm_1 = '".$code_adm_1."'";

	//echo $sql;
	$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

	// Error check statement
	if (!$result) {
	    echo "\nPDO::errorInfo():\n";
	    print_r($pdo->errorInfo());
	}

	// Execute query
	$result->execute();
	$data = array(	'status' => 'OK'
				);

	// Close connection
	$pdo = null;
    return $response->withJson($data);
});

$app->get('/qlue/adm_1', function (Request $request, Response $response, array $args) {
	$lat = $request->getQueryParam('lat');
	$lng = $request->getQueryParam('lng');

	$pdo = $this->get('pdo');

	// Run Query
	$sql = "SELECT code_adm_0,code_adm_1,adm_1,division_id,center_lat,center_lng  FROM adm_1 WHERE ST_Contains(geometry, ST_GeomFromText('POINT(".$lng." ".$lat.")', 4326))";
	$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

	// Error check statement
	if (!$result) {
	    echo "\nPDO::errorInfo():\n";
	    print_r($pdo->errorInfo());
	}

	// Execute query
	$result->execute();
	//print_r($result->errorInfo());
	if ($row = $result->fetch()) {
		$data = array(	'status' => 'OK',
						'code_adm_0' => $row['code_adm_0'],
						//'adm_0' => $row['adm_0'],
						'code_adm_1' => $row['code_adm_1'],
						'adm_1' => $row['adm_1'],
						'division_id' => $row['division_id'],
						'center_lat' => $row['center_lat'],
						'center_lng' => $row['center_lng']
					);
	} else {
		// FIND CLOSEST POLYGON
		// Run Query
		$sql = "SELECT code_adm_0,code_adm_1,adm_1,division_id,center_lat,center_lng,ST_DISTANCE(geometry, ST_GeomFromText('POINT(".$lng." ".$lat.")', 4326)) FROM adm_1 ORDER BY ST_DISTANCE(geometry, ST_GeomFromText('POINT(".$lng." ".$lat.")', 4326)) ASC limit 1";

		$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

		// Error check statement
		if (!$result) {
		    echo "\nPDO::errorInfo():\n";
		    print_r($pdo->errorInfo());
		}

		// Execute query
		$result->execute();
		//print_r($result->errorInfo());
		if ($row = $result->fetch()) {
			$data = array(	'status' => 'OK',
							'code_adm_0' => $row['code_adm_0'],
							//'adm_0' => $row['adm_0'],
							'code_adm_1' => $row['code_adm_1'],
							'adm_1' => $row['adm_1'],
							'division_id' => $row['division_id'],
							'center_lat' => $row['center_lat'],
							'center_lng' => $row['center_lng']
						);
		} else	{
			$data = array('status' => 'ZERO_RESULTS');
		}
	}
	//echo $result->fetchColumn();
	// while ($row = $result->fetch())
	// {
	// 	echo $row['adm_4'];
	// }
	// Close connection
	$pdo = null;

    return $response->withJson($data);;
});


$app->get('/adm_2/list', function (Request $request, Response $response, array $args) {
	$code_adm_1 = $request->getQueryParam('code_adm_1');

	$pdo = $this->get('pdo');

	// Run Query
	$sql = "SELECT code_adm_2,adm_2,subscribe,subscribe_start,subscribe_end FROM qlue_adm_2 WHERE code_adm_1 = '".$code_adm_1."'";

	//echo $sql;
	$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

	// Error check statement
	if (!$result) {
	    echo "\nPDO::errorInfo():\n";
	    print_r($pdo->errorInfo());
	}

	// Execute query
	$result->execute();
	//print_r($result->errorInfo());
	if ($row = $result->fetch()) {
		do {
		$adm_2[] = array(	'code_adm_2' => $row['code_adm_2'],
							'adm_2' => $row['adm_2'],
							'subscribe' => $row['subscribe'],
							'subscribe_start' => $row['subscribe_start'],
							'subscribe_end' => $row['subscribe_end']
					);
		} while ($row = $result->fetch());

		$data = array(	'status' => 'OK',
						'result' => $adm_2
					);
	} else {
		$data = array('status' => 'ZERO_RESULTS');
	}
	// Close connection
	$pdo = null;
    return $response->withJson($data);
});

$app->get('/qlue/adm_2/list', function (Request $request, Response $response, array $args) {
	$code_adm_1 = $request->getQueryParam('code_adm_1');

	$pdo = $this->get('pdo');

	// Run Query
	$sql = "SELECT code_adm_2,adm_2,division_id FROM adm_2 WHERE code_adm_1 = '".$code_adm_1."'";

	//echo $sql;
	$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

	// Error check statement
	if (!$result) {
	    echo "\nPDO::errorInfo():\n";
	    print_r($pdo->errorInfo());
	}

	// Execute query
	$result->execute();
	//print_r($result->errorInfo());
	if ($row = $result->fetch()) {
		do {
		$adm_2[] = array(	'code_adm_2' => $row['code_adm_2'],
							'adm_2' => $row['adm_2'],
							'division_id' => $row['division_id']
					);
		} while ($row = $result->fetch());

		$data = array(	'status' => 'OK',
						'result' => $adm_2
					);
	} else {
		$data = array('status' => 'ZERO_RESULTS');
	}
	// Close connection
	$pdo = null;
    return $response->withJson($data);
});

$app->get('/adm_2/search', function (Request $request, Response $response, array $args) {
	$code_adm_2 = $request->getQueryParam('code_adm_2');

	$pdo = $this->get('pdo');

	// Run Query
	$sql = "SELECT code_adm_2,adm_2 FROM qlue_adm_2 WHERE code_adm_2 = '".$code_adm_2."' GROUP BY code_adm_2";

	//echo $sql;
	$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

	// Error check statement
	if (!$result) {
	    echo "\nPDO::errorInfo():\n";
	    print_r($pdo->errorInfo());
	}

	// Execute query
	$result->execute();
	//print_r($result->errorInfo());
	if ($row = $result->fetch()) {
		do {
		$adm_2[] = array(	'code_adm_2' => $row['code_adm_2'],
							'adm_2' => $row['adm_2']
					);
		} while ($row = $result->fetch());

		$data = array(	'status' => 'OK',
						'result' => $adm_2
					);
	} else {
		$data = array('status' => 'ZERO_RESULTS');
	}
	// Close connection
	$pdo = null;
    return $response->withJson($data);
});

$app->get('/qlue/adm_2/search', function (Request $request, Response $response, array $args) {
	$code_adm_2 = $request->getQueryParam('code_adm_2');

	$pdo = $this->get('pdo');

	// Run Query
	$sql = "SELECT code_adm_2,adm_2,division_id FROM adm_2 WHERE code_adm_2 = '".$code_adm_2."' GROUP BY code_adm_2";

	//echo $sql;
	$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

	// Error check statement
	if (!$result) {
	    echo "\nPDO::errorInfo():\n";
	    print_r($pdo->errorInfo());
	}

	// Execute query
	$result->execute();
	//print_r($result->errorInfo());
	if ($row = $result->fetch()) {
		do {
		$adm_2[] = array(	'code_adm_2' => $row['code_adm_2'],
							'adm_2' => $row['adm_2'],
							'division_id' => $row['division_id']
					);
		} while ($row = $result->fetch());

		$data = array(	'status' => 'OK',
						'result' => $adm_2
					);
	} else {
		$data = array('status' => 'ZERO_RESULTS');
	}
	// Close connection
	$pdo = null;
    return $response->withJson($data);
});


$app->put('/adm_2', function (Request $request, Response $response, array $args) {
	$code_adm_2 = $request->getQueryParam('code_adm_2');
	$subscribe = $request->getQueryParam('subscribe');
	$subscribe_start = $request->getQueryParam('subscribe_start');
	$subscribe_end = $request->getQueryParam('subscribe_end');

	$pdo = $this->get('pdo');

	// Run Query
	$sql = "UPDATE qlue_adm_2 SET subscribe = '".$subscribe."',subscribe_start = '".$subscribe_start."',subscribe_end =  '".$subscribe_end."' WHERE code_adm_2 = '".$code_adm_2."'";

	//echo $sql;
	$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

	// Error check statement
	if (!$result) {
	    echo "\nPDO::errorInfo():\n";
	    print_r($pdo->errorInfo());
	}

	// Execute query
	$result->execute();
	$data = array(	'status' => 'OK'
				);

	// Close connection
	$pdo = null;
    return $response->withJson($data);
});

$app->get('/qlue/adm_2', function (Request $request, Response $response, array $args) {
	$lat = $request->getQueryParam('lat');
	$lng = $request->getQueryParam('lng');

	$pdo = $this->get('pdo');

	// Run Query
	$sql = "SELECT code_adm_0,code_adm_1,adm_1,code_adm_2,adm_2,division_id,center_lat,center_lng  FROM adm_2 WHERE ST_Contains(geometry, ST_GeomFromText('POINT(".$lng." ".$lat.")', 4326))";
	$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

	// Error check statement
	if (!$result) {
	    echo "\nPDO::errorInfo():\n";
	    print_r($pdo->errorInfo());
	}

	// Execute query
	$result->execute();
	//print_r($result->errorInfo());
	if ($row = $result->fetch()) {
		$data = array(	'status' => 'OK',
						'code_adm_0' => $row['code_adm_0'],
						//'adm_0' => $row['adm_0'],
						'code_adm_1' => $row['code_adm_1'],
						'adm_1' => $row['adm_1'],
						'code_adm_2' => $row['code_adm_2'],
						'adm_2' => $row['adm_2'],
						'division_id' => $row['division_id'],
						'center_lat' => $row['center_lat'],
						'center_lng' => $row['center_lng']
					);
	} else {
		// FIND CLOSEST POLYGON
		// Run Query
		$sql = "SELECT code_adm_0,code_adm_1,adm_1,code_adm_2,adm_2,division_id,center_lat,center_lng,ST_DISTANCE(geometry, ST_GeomFromText('POINT(".$lng." ".$lat.")', 4326)) FROM adm_2 ORDER BY ST_DISTANCE(geometry, ST_GeomFromText('POINT(".$lng." ".$lat.")', 4326)) ASC limit 1";

		$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

		// Error check statement
		if (!$result) {
		    echo "\nPDO::errorInfo():\n";
		    print_r($pdo->errorInfo());
		}

		// Execute query
		$result->execute();
		//print_r($result->errorInfo());
		if ($row = $result->fetch()) {
			$data = array(	'status' => 'OK',
							'code_adm_0' => $row['code_adm_0'],
							//'adm_0' => $row['adm_0'],
							'code_adm_1' => $row['code_adm_1'],
							'adm_1' => $row['adm_1'],
							'code_adm_2' => $row['code_adm_2'],
							'adm_2' => $row['adm_2'],
							'division_id' => $row['division_id'],
							'center_lat' => $row['center_lat'],
							'center_lng' => $row['center_lng']
						);
		} else	{
			$data = array('status' => 'ZERO_RESULTS');
		}
	}
	//echo $result->fetchColumn();
	// while ($row = $result->fetch())
	// {
	// 	echo $row['adm_4'];
	// }
	// Close connection
	$pdo = null;

    return $response->withJson($data);;
});

// $app->get('/adm_2/list', function (Request $request, Response $response, array $args) {
// 	$code_adm_1 = $request->getQueryParam('code_adm_1');

// 	$pdo = $this->get('pdo');

// 	// Run Query
// 	//$sql = "SELECT code_adm_2,adm_2 FROM qlue_adm_2 WHERE code_adm_1 = '".$code_adm_1."'";
// 	$sql = "SELECT code_adm_2,adm_2 FROM qlue_adm_geom WHERE code_adm_1 = '".$code_adm_1."' GROUP BY code_adm_2";

// 	//echo $sql;
// 	$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

// 	// Error check statement
// 	if (!$result) {
// 	    echo "\nPDO::errorInfo():\n";
// 	    print_r($pdo->errorInfo());
// 	}

// 	// Execute query
// 	$result->execute();
// 	//print_r($result->errorInfo());
// 	if ($row = $result->fetch()) {
// 		do {
// 		$adm_2[] = array(	'code_adm_2' => $row['code_adm_2'],
// 							'adm_2' => $row['adm_2']
// 					);
// 		} while ($row = $result->fetch());

// 		$data = array(	'status' => 'OK',
// 						'result' => $adm_2
// 					);
// 	} else {
// 		$data = array('status' => 'ZERO_RESULTS');
// 	}
// 	// Close connection
// 	$pdo = null;
//     return $response->withJson($data);
// });

$app->get('/mendagri/adm_3/list', function (Request $request, Response $response, array $args) {
	$code_adm_2 = $request->getQueryParam('code_adm_2');

	$pdo = $this->get('pdo');

	// Run Query
	//$sql = "SELECT code_adm_2,adm_2 FROM qlue_adm_2 WHERE code_adm_1 = '".$code_adm_1."'";
	$sql = "SELECT code_adm_3,adm_3,center_lat,center_lng FROM qlue_adm_3 WHERE code_adm_2 = '".$code_adm_2."' GROUP BY code_adm_3";

	//echo $sql;
	$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

	// Error check statement
	if (!$result) {
	    echo "\nPDO::errorInfo():\n";
	    print_r($pdo->errorInfo());
	}

	// Execute query
	$result->execute();
	//print_r($result->errorInfo());
	if ($row = $result->fetch()) {
		do {
		$adm_3[] = array(	'code_adm_3' => $row['code_adm_3'],
							'adm_3' => $row['adm_3'],
							'center_lat' => $row['center_lat'],
							'center_lng' => $row['center_lng']

					);
		} while ($row = $result->fetch());

		$data = array(	'status' => 'OK',
						'result' => $adm_3
					);
	} else {
		$data = array('status' => 'ZERO_RESULTS');
	}
	// Close connection
	$pdo = null;
    return $response->withJson($data);
});

$app->get('/adm_3/list', function (Request $request, Response $response, array $args) {
	$code_adm_2 = $request->getQueryParam('code_adm_2');

	$pdo = $this->get('pdo');

	// Run Query
	//$sql = "SELECT code_adm_2,adm_2 FROM qlue_adm_2 WHERE code_adm_1 = '".$code_adm_1."'";
	$sql = "SELECT code_adm_3,adm_3 FROM qlue_adm_geom WHERE code_adm_2 = '".$code_adm_2."' GROUP BY code_adm_3";

	//echo $sql;
	$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

	// Error check statement
	if (!$result) {
	    echo "\nPDO::errorInfo():\n";
	    print_r($pdo->errorInfo());
	}

	// Execute query
	$result->execute();
	//print_r($result->errorInfo());
	if ($row = $result->fetch()) {
		do {
		$adm_3[] = array(	'code_adm_3' => $row['code_adm_3'],
							'adm_3' => $row['adm_3']
					);
		} while ($row = $result->fetch());

		$data = array(	'status' => 'OK',
						'result' => $adm_3
					);
	} else {
		$data = array('status' => 'ZERO_RESULTS');
	}
	// Close connection
	$pdo = null;
    return $response->withJson($data);
});

$app->get('/qlue/adm_3/list', function (Request $request, Response $response, array $args) {
	$code_adm_2 = $request->getQueryParam('code_adm_2');

	$pdo = $this->get('pdo');

	// Run Query
	//$sql = "SELECT code_adm_2,adm_2 FROM qlue_adm_2 WHERE code_adm_1 = '".$code_adm_1."'";
	$sql = "SELECT code_adm_3,adm_3 FROM adm_3 WHERE code_adm_2 = '".$code_adm_2."' GROUP BY code_adm_3";

	//echo $sql;
	$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

	// Error check statement
	if (!$result) {
	    echo "\nPDO::errorInfo():\n";
	    print_r($pdo->errorInfo());
	}

	// Execute query
	$result->execute();
	//print_r($result->errorInfo());
	if ($row = $result->fetch()) {
		do {
		$adm_3[] = array(	'code_adm_3' => $row['code_adm_3'],
							'adm_3' => $row['adm_3']
					);
		} while ($row = $result->fetch());

		$data = array(	'status' => 'OK',
						'result' => $adm_3
					);
	} else {
		$data = array('status' => 'ZERO_RESULTS');
	}
	// Close connection
	$pdo = null;
    return $response->withJson($data);
});

$app->get('/adm_3/search', function (Request $request, Response $response, array $args) {
	$code_adm_3 = $request->getQueryParam('code_adm_3');

	$pdo = $this->get('pdo');

	// Run Query
	//$sql = "SELECT code_adm_2,adm_2 FROM qlue_adm_2 WHERE code_adm_1 = '".$code_adm_1."'";
	$sql = "SELECT code_adm_3,adm_3 FROM qlue_adm_geom WHERE code_adm_3 = '".$code_adm_3."' GROUP BY code_adm_3";

	//echo $sql;
	$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

	// Error check statement
	if (!$result) {
	    echo "\nPDO::errorInfo():\n";
	    print_r($pdo->errorInfo());
	}

	// Execute query
	$result->execute();
	//print_r($result->errorInfo());
	if ($row = $result->fetch()) {
		do {
		$adm_3[] = array(	'code_adm_3' => $row['code_adm_3'],
							'adm_3' => $row['adm_3']
					);
		} while ($row = $result->fetch());

		$data = array(	'status' => 'OK',
						'result' => $adm_3
					);
	} else {
		$data = array('status' => 'ZERO_RESULTS');
	}
	// Close connection
	$pdo = null;
    return $response->withJson($data);
});

$app->get('/qlue/adm_3/search', function (Request $request, Response $response, array $args) {
	$code_adm_3 = $request->getQueryParam('code_adm_3');

	$pdo = $this->get('pdo');

	// Run Query
	//$sql = "SELECT code_adm_2,adm_2 FROM qlue_adm_2 WHERE code_adm_1 = '".$code_adm_1."'";
	$sql = "SELECT code_adm_3,adm_3 FROM adm_3 WHERE code_adm_3 = '".$code_adm_3."' GROUP BY code_adm_3";

	//echo $sql;
	$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

	// Error check statement
	if (!$result) {
	    echo "\nPDO::errorInfo():\n";
	    print_r($pdo->errorInfo());
	}

	// Execute query
	$result->execute();
	//print_r($result->errorInfo());
	if ($row = $result->fetch()) {
		do {
		$adm_3[] = array(	'code_adm_3' => $row['code_adm_3'],
							'adm_3' => $row['adm_3']
					);
		} while ($row = $result->fetch());

		$data = array(	'status' => 'OK',
						'result' => $adm_3
					);
	} else {
		$data = array('status' => 'ZERO_RESULTS');
	}
	// Close connection
	$pdo = null;
    return $response->withJson($data);
});

$app->get('/adm_4/list', function (Request $request, Response $response, array $args) {
	$code_adm_3 = $request->getQueryParam('code_adm_3');

	$pdo = $this->get('pdo');

	// Run Query
	//$sql = "SELECT code_adm_2,adm_2 FROM qlue_adm_2 WHERE code_adm_1 = '".$code_adm_1."'";
	$sql = "SELECT code_adm_4,adm_4 FROM qlue_adm_geom WHERE code_adm_3 = '".$code_adm_3."' GROUP BY code_adm_4";

	//echo $sql;
	$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

	// Error check statement
	if (!$result) {
	    echo "\nPDO::errorInfo():\n";
	    print_r($pdo->errorInfo());
	}

	// Execute query
	$result->execute();
	//print_r($result->errorInfo());
	if ($row = $result->fetch()) {
		do {
		$adm_4[] = array(	'code_adm_4' => $row['code_adm_4'],
							'adm_4' => $row['adm_4']
					);
		} while ($row = $result->fetch());

		$data = array(	'status' => 'OK',
						'result' => $adm_4
					);
	} else {
		$data = array('status' => 'ZERO_RESULTS');
	}
	// Close connection
	$pdo = null;
    return $response->withJson($data);
});

$app->get('/qlue/adm_4/list', function (Request $request, Response $response, array $args) {
	$code_adm_3 = $request->getQueryParam('code_adm_3');

	$pdo = $this->get('pdo');

	// Run Query
	//$sql = "SELECT code_adm_2,adm_2 FROM qlue_adm_2 WHERE code_adm_1 = '".$code_adm_1."'";
	$sql = "SELECT code_adm_4,adm_4 FROM adm_4 WHERE code_adm_3 = '".$code_adm_3."' GROUP BY code_adm_4";

	//echo $sql;
	$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

	// Error check statement
	if (!$result) {
	    echo "\nPDO::errorInfo():\n";
	    print_r($pdo->errorInfo());
	}

	// Execute query
	$result->execute();
	//print_r($result->errorInfo());
	if ($row = $result->fetch()) {
		do {
		$adm_4[] = array(	'code_adm_4' => $row['code_adm_4'],
							'adm_4' => $row['adm_4']
					);
		} while ($row = $result->fetch());

		$data = array(	'status' => 'OK',
						'result' => $adm_4
					);
	} else {
		$data = array('status' => 'ZERO_RESULTS');
	}
	// Close connection
	$pdo = null;
    return $response->withJson($data);
});

$app->get('/adm_4/search', function (Request $request, Response $response, array $args) {
	$code_adm_4 = $request->getQueryParam('code_adm_4');

	$pdo = $this->get('pdo');

	// Run Query
	//$sql = "SELECT code_adm_2,adm_2 FROM qlue_adm_2 WHERE code_adm_1 = '".$code_adm_1."'";
	$sql = "SELECT code_adm_4,adm_4 FROM qlue_adm_geom WHERE code_adm_4 = '".$code_adm_4."' GROUP BY code_adm_4";

	//echo $sql;
	$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

	// Error check statement
	if (!$result) {
	    echo "\nPDO::errorInfo():\n";
	    print_r($pdo->errorInfo());
	}

	// Execute query
	$result->execute();
	//print_r($result->errorInfo());
	if ($row = $result->fetch()) {
		do {
		$adm_4[] = array(	'code_adm_4' => $row['code_adm_4'],
							'adm_4' => $row['adm_4']
					);
		} while ($row = $result->fetch());

		$data = array(	'status' => 'OK',
						'result' => $adm_4
					);
	} else {
		$data = array('status' => 'ZERO_RESULTS');
	}
	// Close connection
	$pdo = null;
    return $response->withJson($data);
});


$app->get('/qlue/adm_4/search', function (Request $request, Response $response, array $args) {
	$code_adm_4 = $request->getQueryParam('code_adm_4');

	$pdo = $this->get('pdo');

	// Run Query
	//$sql = "SELECT code_adm_2,adm_2 FROM qlue_adm_2 WHERE code_adm_1 = '".$code_adm_1."'";
	$sql = "SELECT code_adm_4,adm_4 FROM adm_4 WHERE code_adm_4 = '".$code_adm_4."' GROUP BY code_adm_4";

	//echo $sql;
	$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

	// Error check statement
	if (!$result) {
	    echo "\nPDO::errorInfo():\n";
	    print_r($pdo->errorInfo());
	}

	// Execute query
	$result->execute();
	//print_r($result->errorInfo());
	if ($row = $result->fetch()) {
		do {
		$adm_4[] = array(	'code_adm_4' => $row['code_adm_4'],
							'adm_4' => $row['adm_4']
					);
		} while ($row = $result->fetch());

		$data = array(	'status' => 'OK',
						'result' => $adm_4
					);
	} else {
		$data = array('status' => 'ZERO_RESULTS');
	}
	// Close connection
	$pdo = null;
    return $response->withJson($data);
});

$app->get('/adm_4', function (Request $request, Response $response, array $args) {
	$lat = $request->getQueryParam('lat');
	$lng = $request->getQueryParam('lng');

	$pdo = $this->get('pdo');

	// Run Query
	$sql = "SELECT code_adm_0,adm_0,code_adm_1,adm_1,code_adm_2,adm_2,code_adm_3,adm_3,code_adm_4,adm_4,center_lat,center_lng  FROM qlue_adm_geom WHERE ST_Contains(SHAPE, ST_GeomFromText('POINT(".$lng." ".$lat.")', 4326))";
	$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

	// Error check statement
	if (!$result) {
	    echo "\nPDO::errorInfo():\n";
	    print_r($pdo->errorInfo());
	}

	// Execute query
	$result->execute();
	//print_r($result->errorInfo());
	if ($row = $result->fetch()) {
		$data = array(	'status' => 'OK',
						'code_adm_0' => $row['code_adm_0'],
						'adm_0' => $row['adm_0'],
						'code_adm_1' => $row['code_adm_1'],
						'adm_1' => $row['adm_1'],
						'code_adm_2' => $row['code_adm_2'],
						'adm_2' => $row['adm_2'],
						'code_adm_3' => $row['code_adm_3'],
						'adm_3' => $row['adm_3'],
						'code_adm_4' => $row['code_adm_4'],
						'adm_4' => $row['adm_4'],
						'center_lat' => $row['center_lat'],
						'center_lng' => $row['center_lng']
					);
	} else {
		// FIND CLOSEST POLYGON
		// Run Query
		$sql = "SELECT code_adm_0,adm_0,code_adm_1,adm_1,code_adm_2,adm_2,code_adm_3,adm_3,code_adm_4,adm_4,center_lat,center_lng,ST_DISTANCE(SHAPE, ST_GeomFromText('POINT(".$lng." ".$lat.")', 4326)) FROM qlue_adm_geom ORDER BY ST_DISTANCE(SHAPE, ST_GeomFromText('POINT(".$lng." ".$lat.")', 4326)) ASC limit 1";

		$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

		// Error check statement
		if (!$result) {
		    echo "\nPDO::errorInfo():\n";
		    print_r($pdo->errorInfo());
		}

		// Execute query
		$result->execute();
		//print_r($result->errorInfo());
		if ($row = $result->fetch()) {
			$data = array(	'status' => 'OK',
							'code_adm_0' => $row['code_adm_0'],
							'adm_0' => $row['adm_0'],
							'code_adm_1' => $row['code_adm_1'],
							'adm_1' => $row['adm_1'],
							'code_adm_2' => $row['code_adm_2'],
							'adm_2' => $row['adm_2'],
							'code_adm_3' => $row['code_adm_3'],
							'adm_3' => $row['adm_3'],
							'code_adm_4' => $row['code_adm_4'],
							'adm_4' => $row['adm_4'],
							'center_lat' => $row['center_lat'],
							'center_lng' => $row['center_lng']
						);
		} else	{
			$data = array('status' => 'ZERO_RESULTS');
		}
	}
	//echo $result->fetchColumn();
	// while ($row = $result->fetch())
	// {
	// 	echo $row['adm_4'];
	// }
	// Close connection
	$pdo = null;

    return $response->withJson($data);;
});

$app->get('/qlue/adm_4', function (Request $request, Response $response, array $args) {
	$lat = $request->getQueryParam('lat');
	$lng = $request->getQueryParam('lng');

	$pdo = $this->get('pdo');

	// Run Query
	$sql = "SELECT adm_4.code_adm_0,adm_4.code_adm_1,adm_4.adm_1,adm_4.code_adm_2,adm_4.adm_2,adm_4.code_adm_3,adm_4.adm_3,adm_4.code_adm_4,adm_4.adm_4,adm_4.center_lat,adm_4.center_lng,adm_2.division_id  FROM adm_4 LEFT JOIN adm_2 ON adm_4.code_adm_2 = adm_2.code_adm_2 WHERE ST_Contains(adm_4.geometry, ST_GeomFromText('POINT(".$lng." ".$lat.")', 4326))";
	$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement
	//echo $sql;
	// Error check statement
	if (!$result) {
	    echo "\nPDO::errorInfo():\n";
	    print_r($pdo->errorInfo());
	}

	// Execute query
	$result->execute();
	//print_r($result->errorInfo());
	if ($row = $result->fetch()) {
		$data = array(	'status' => 'OK',
						'code_adm_0' => $row['code_adm_0'],
						'code_adm_1' => $row['code_adm_1'],
						'adm_1' => $row['adm_1'],
						'code_adm_2' => $row['code_adm_2'],
						'adm_2' => $row['adm_2'],
						'code_adm_3' => $row['code_adm_3'],
						'adm_3' => $row['adm_3'],
						'code_adm_4' => $row['code_adm_4'],
						'adm_4' => $row['adm_4'],
						'division_id' => $row['division_id'],
						'center_lat' => $row['center_lat'],
						'center_lng' => $row['center_lng']
					);
	} else {
		// FIND CLOSEST POLYGON
		// Run Query
		$sql = "SELECT adm_4.code_adm_0,adm_4.code_adm_1,adm_4.adm_1,adm_4.code_adm_2,adm_4.adm_2,adm_4.code_adm_3,adm_4.adm_3,adm_4.code_adm_4,adm_4.adm_4,adm_4.center_lat,adm_4.center_lng,adm_2.division_id ST_DISTANCE(adm_4.geometry, ST_GeomFromText('POINT(".$lng." ".$lat.")', 4326)) FROM adm_4 LEFT JOIN adm_2 ON adm_4.code_adm_2 = adm_2.code_adm_2 ORDER BY ST_DISTANCE(adm_4.geometry, ST_GeomFromText('POINT(".$lng." ".$lat.")', 4326)) ASC limit 1";

		$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

		// Error check statement
		if (!$result) {
		    echo "\nPDO::errorInfo():\n";
		    print_r($pdo->errorInfo());
		}

		// Execute query
		$result->execute();
		//print_r($result->errorInfo());
		if ($row = $result->fetch()) {
			$data = array(	'status' => 'OK',
							'code_adm_0' => $row['code_adm_0'],
							'code_adm_1' => $row['code_adm_1'],
							'adm_1' => $row['adm_1'],
							'code_adm_2' => $row['code_adm_2'],
							'adm_2' => $row['adm_2'],
							'code_adm_3' => $row['code_adm_3'],
							'adm_3' => $row['adm_3'],
							'code_adm_4' => $row['code_adm_4'],
							'adm_4' => $row['adm_4'],
							'division_id' => $row['division_id'],
							'center_lat' => $row['center_lat'],
							'center_lng' => $row['center_lng']
						);
		} else	{
			$data = array('status' => 'ZERO_RESULTS');
		}
	}
	//echo $result->fetchColumn();
	// while ($row = $result->fetch())
	// {
	// 	echo $row['adm_4'];
	// }
	// Close connection
	$pdo = null;

    return $response->withJson($data);;
});

$app->get('/mendagri/adm_4', function (Request $request, Response $response, array $args) {
	$lat = $request->getQueryParam('lat');
	$lng = $request->getQueryParam('lng');

	$pdo = $this->get('pdo');

	// Run Query
	$sql = "SELECT code_adm_0,adm_0,code_adm_1,adm_1,code_adm_2,adm_2,code_adm_3,adm_3,code_adm_4,adm_4,center_lat,center_lng  FROM qlue_adm_4 WHERE ST_Contains(SHAPE, ST_GeomFromText('POINT(".$lng." ".$lat.")', 4326))";
	$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

	// Error check statement
	if (!$result) {
	    echo "\nPDO::errorInfo():\n";
	    print_r($pdo->errorInfo());
	}

	// Execute query
	$result->execute();
	//print_r($result->errorInfo());
	if ($row = $result->fetch()) {
		$data = array(	'status' => 'OK',
						'code_adm_0' => $row['code_adm_0'],
						'adm_0' => $row['adm_0'],
						'code_adm_1' => $row['code_adm_1'],
						'adm_1' => $row['adm_1'],
						'code_adm_2' => $row['code_adm_2'],
						'adm_2' => $row['adm_2'],
						'code_adm_3' => $row['code_adm_3'],
						'adm_3' => $row['adm_3'],
						'code_adm_4' => $row['code_adm_4'],
						'adm_4' => $row['adm_4'],
						'center_lat' => $row['center_lat'],
						'center_lng' => $row['center_lng']
					);
	} else {
		// FIND CLOSEST POLYGON
		// Run Query
		$sql = "SELECT code_adm_0,adm_0,code_adm_1,adm_1,code_adm_2,adm_2,code_adm_3,adm_3,code_adm_4,adm_4,center_lat,center_lng,ST_DISTANCE(SHAPE, ST_GeomFromText('POINT(".$lng." ".$lat.")', 4326)) FROM qlue_adm_4 ORDER BY ST_DISTANCE(SHAPE, ST_GeomFromText('POINT(".$lng." ".$lat.")', 4326)) ASC limit 1";

		$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

		// Error check statement
		if (!$result) {
		    echo "\nPDO::errorInfo():\n";
		    print_r($pdo->errorInfo());
		}

		// Execute query
		$result->execute();
		//print_r($result->errorInfo());
		if ($row = $result->fetch()) {
			$data = array(	'status' => 'OK',
							'code_adm_0' => $row['code_adm_0'],
							'adm_0' => $row['adm_0'],
							'code_adm_1' => $row['code_adm_1'],
							'adm_1' => $row['adm_1'],
							'code_adm_2' => $row['code_adm_2'],
							'adm_2' => $row['adm_2'],
							'code_adm_3' => $row['code_adm_3'],
							'adm_3' => $row['adm_3'],
							'code_adm_4' => $row['code_adm_4'],
							'adm_4' => $row['adm_4'],
							'center_lat' => $row['center_lat'],
							'center_lng' => $row['center_lng']
						);
		} else	{
			$data = array('status' => 'ZERO_RESULTS');
		}
	}
	//echo $result->fetchColumn();
	// while ($row = $result->fetch())
	// {
	// 	echo $row['adm_4'];
	// }
	// Close connection
	$pdo = null;

    return $response->withJson($data);;
});


$app->get('/pol/nearest', function (Request $request, Response $response, array $args) {
	$lat = $request->getQueryParam('lat');
	$lng = $request->getQueryParam('lng');

	$pdo = $this->get('pdo');

	// Run Query
	$sql =
		'SELECT polsek.nama as polsek, polres.nama as polres, polda.nama as polda,
				polsek.alamat as alamatsek, polres.alamat as alamatres, polda.alamat as alamatda,
				polsek.telp as telpsek, polres.telp as telpres, polda.telp as telpda,
				polsek.lat as latsek, polres.lat as latres, polda.lat as latda,
				polsek.lng as lngsek, polres.lng as lngres, polda.lng as lngda
			FROM
			(SELECT	nama, alamat, telp, lat, lng,
						SQRT( POW( 69.1 * ( lat - '.$lat.') , 2 ) +
	            		POW( 69.1 * ( '.$lng.' - lng ) * COS( lat / 57.3 ) , 2 ) ) AS distance
			FROM
					qlue_polsek
			WHERE 	lat BETWEEN (('.$lat.') - 3.17) AND (('.$lat.') + 3.17) AND
					lng BETWEEN (('.$lng.') - 3.17) AND (('.$lng.') + 3.17)
			HAVING distance < 30 ORDER BY distance ASC limit 0,1) AS polsek,
			(SELECT	nama, alamat, telp, lat, lng,
						SQRT( POW( 69.1 * ( lat - '.$lat.') , 2 ) +
	            		POW( 69.1 * ( '.$lng.' - lng ) * COS( lat / 57.3 ) , 2 ) ) AS distance
			FROM
					qlue_polres
			WHERE 	lat BETWEEN (('.$lat.') - 3.17) AND (('.$lat.') + 3.17) AND
					lng BETWEEN (('.$lng.') - 3.17) AND (('.$lng.') + 3.17)
			HAVING distance < 30 ORDER BY distance ASC limit 0,1) AS polres,
			(SELECT	nama, alamat, telp, lat, lng,
						SQRT( POW( 69.1 * ( lat - '.$lat.') , 2 ) +
	            		POW( 69.1 * ( '.$lng.' - lng ) * COS( lat / 57.3 ) , 2 ) ) AS distance
			FROM
					qlue_polda
			WHERE 	lat BETWEEN (('.$lat.') - 3.17) AND (('.$lat.') + 3.17) AND
					lng BETWEEN (('.$lng.') - 3.17) AND (('.$lng.') + 3.17)
			HAVING distance < 30 ORDER BY distance ASC limit 0,1) AS polda';
	//echo $sql;

	$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

	// Error check statement
	if (!$result) {
	    echo "\nPDO::errorInfo():\n";
	    print_r($pdo->errorInfo());
	}


	// Execute query
	$result->execute();
	//print_r($result->errorInfo());
		//print_r($result->errorInfo());
	if ($row = $result->fetch()) {
		$data = array(	'status' => 'OK',
						'polsek' => $row['polsek'],
						'alamat_polsek' => $row['alamatsek'],
						'telp_polsek' => $row['telpsek'],
						'lat_polsek' => $row['latsek'],
						'lng_polsek' => $row['lngsek'],

						'polres' => $row['polres'],
						'alamat_polres' => $row['alamatres'],
						'telp_polres' => $row['telpres'],
						'lat_polres' => $row['latres'],
						'lng_polres' => $row['lngres'],

						'polda' => $row['polda'],
						'alamat_polda' => $row['alamatda'],
						'telp_polda' => $row['telpda'],
						'lat_polda' => $row['latda'],
						'lng_polda' => $row['lngda'],
					);
	} else {
		$data = array('status' => 'ZERO_RESULTS');

	}
	//echo $result->fetchColumn();
	// while ($row = $result->fetch())
	// {
	// 	echo $row['adm_4'];
	// }
	// Close connection
	$pdo = null;

    return $response->withJson($data);

});

$app->get('/pol/adm_4', function (Request $request, Response $response, array $args) {
	$lat = $request->getQueryParam('lat');
	$lng = $request->getQueryParam('lng');

	$pdo = $this->get('pdo');

	// Run Query
	$sql = "SELECT qlue_adm_geom.code_adm_0,qlue_adm_geom.adm_0,qlue_adm_geom.code_adm_1,qlue_adm_geom.adm_1,qlue_adm_geom.code_adm_2,qlue_adm_geom.adm_2,qlue_adm_geom.code_adm_3,qlue_adm_geom.adm_3,qlue_adm_geom.code_adm_4,qlue_adm_geom.adm_4,qlue_adm_geom.center_lat,qlue_adm_geom.center_lng,qlue_adm_3.code_adm_pol, qlue_adm_polisi.level, qlue_adm_polisi.name FROM qlue_adm_geom JOIN qlue_adm_3 ON qlue_adm_geom.code_adm_3 = qlue_adm_3.code_adm_3 AND ST_Contains(qlue_adm_geom.SHAPE, ST_GeomFromText('POINT(".$lng." ".$lat.")', 4326)) JOIN qlue_adm_polisi ON qlue_adm_3.code_adm_pol = qlue_adm_polisi.code_adm_pol";
	$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

	// Error check statement
	if (!$result) {
	    echo "\nPDO::errorInfo():\n";
	    print_r($pdo->errorInfo());
	}

	//echo $sql;
	//exit;

	// Execute query
	$result->execute();
	//print_r($result->errorInfo());
	if ($row = $result->fetch()) {
		//If code polsek
		$code_polsek = $row['code_adm_pol'];
		$code_exp = explode('-', $code_polsek);
		$code_polda  =  $code_exp[0].'-'.$code_exp[1];
		$code_polres =  $code_exp[0].'-'.$code_exp[1].'-'.$code_exp[2];
		$sql2 = "SELECT code_adm_pol,level,name FROM qlue_adm_polisi WHERE code_adm_pol IN ('".$code_polsek."','".$code_polres."','".$code_polda."')";

		$result2 = $pdo->prepare($sql2);

		// Execute query
		$result2->execute();

		$code_polda= '';
		$polda = '';
		$code_polres = '';
		$polres = '';
		$code_polsek= '';
		$polsek = '';

		//print_r($result->errorInfo());
		if ($row2 = $result2->fetch()) {
			do {
				switch ($row2['level']) {
					case 'Polda'	:	$code_polda = $row2['code_adm_pol'];
										$polda = $row2['name'];
										break;
					case 'Polres'	:	$code_polres= $row2['code_adm_pol'];
										$polres = $row2['name'];
										break;
					case 'Polsek'	:	$code_polsek = $row2['code_adm_pol'];
										$polsek = $row2['name'];
										break;
				}
			} while ($row2 = $result2->fetch());
		}

		$data = array(	'status' => 'OK',
						'code_adm_0' => $row['code_adm_0'],
						'adm_0' => $row['adm_0'],
						'code_adm_1' => $row['code_adm_1'],
						'adm_1' => $row['adm_1'],
						'code_adm_2' => $row['code_adm_2'],
						'adm_2' => $row['adm_2'],
						'code_adm_3' => $row['code_adm_3'],
						'adm_3' => $row['adm_3'],
						'code_adm_4' => $row['code_adm_4'],
						'adm_4' => $row['adm_4'],

						'code_polda' => $code_polda,
						'polda' => $polda,
						'code_polres' => $code_polres,
						'polres' => $polres,
						'code_polsek' => $code_polsek,
						'polsek' => $polsek,

						'center_lat' => $row['center_lat'],
						'center_lng' => $row['center_lng']
					);
	} else {
		// FIND CLOSEST POLYGON
		// Run Query
		$sql = "SELECT code_adm_0,adm_0,code_adm_1,adm_1,code_adm_2,adm_2,code_adm_3,adm_3,code_adm_4,adm_4,center_lat,center_lng,ST_DISTANCE(SHAPE, ST_GeomFromText('POINT(".$lng." ".$lat.")', 4326)) FROM qlue_adm_geom ORDER BY ST_DISTANCE(SHAPE, ST_GeomFromText('POINT(".$lng." ".$lat.")', 4326)) ASC limit 1";

		$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

		// Error check statement
		if (!$result) {
		    echo "\nPDO::errorInfo():\n";
		    print_r($pdo->errorInfo());
		}

		// Execute query
		$result->execute();
		//print_r($result->errorInfo());
		if ($row = $result->fetch()) {
			$data = array(	'status' => 'OK',
							'code_adm_0' => $row['code_adm_0'],
							'adm_0' => $row['adm_0'],
							'code_adm_1' => $row['code_adm_1'],
							'adm_1' => $row['adm_1'],
							'code_adm_2' => $row['code_adm_2'],
							'adm_2' => $row['adm_2'],
							'code_adm_3' => $row['code_adm_3'],
							'adm_3' => $row['adm_3'],
							'code_adm_4' => $row['code_adm_4'],
							'adm_4' => $row['adm_4'],
							'center_lat' => $row['center_lat'],
							'center_lng' => $row['center_lng']
						);
		} else	{
			$data = array('status' => 'ZERO_RESULTS');
		}
	}
	//echo $result->fetchColumn();
	// while ($row = $result->fetch())
	// {
	// 	echo $row['adm_4'];
	// }
	// Close connection
	$pdo = null;

    return $response->withJson($data);;
});


$app->get('/adm_4/geojson', function (Request $request, Response $response, array $args) {
	$id = $request->getQueryParam('id');

	$pdo = $this->get('pdo');

	// Run Query
	$sql = "SELECT code_adm_4,adm_4,AsWKB(SHAPE) AS wkb FROM qlue_adm_geom WHERE code_adm_4 LIKE '%".$id."%' LIMIT 1";
	//$sql = "SELECT AsWKB(SHAPE) AS wkb FROM qlue_adm_1 LIMIT 1";

	# Build GeoJSON feature collection array
	$geojson = array(
	   'type'      => 'FeatureCollection',
	   'features'  => array(),

	);

	$result = $pdo->prepare($sql); // Prevent MySQl injection. $stmt means statement

	// Error check statement
	if (!$result) {
	    echo "\nPDO::errorInfo():\n";
	    print_r($pdo->errorInfo());
	}

	// Execute query
	$result->execute();

	# Loop through rows to build feature arrays
	while ($row = $result->fetch()) {
	    $properties = $row;
	   	$properties = array(
	       'code_adm_4'     => $row['code_adm_4'],
	       'adm_4'  => $row['adm_4'],

	    );

	    # Remove wkb and geometry fields from properties
	    unset($properties['wkb']);
	    unset($properties['SHAPE']);
	    $feature = array(
	         'type' => 'Feature',
	         'geometry' => json_decode(wkb_to_json($row['wkb'])),
	         'properties' => $properties
	    );
	    # Add feature arrays to feature collection array
	    array_push($geojson['features'], $feature);
	}

	// Close connection
	$pdo = null;

	if(function_exists('ob_gzhandler')) ob_start('ob_gzhandler');else ob_start();
    return $response->withJson($geojson,200,JSON_NUMERIC_CHECK);
    ob_end_flush();
});


$app->run();
