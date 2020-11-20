<?php
/*
 ULS-Tuque-lib.php
*/


/*
   Constants
*/

$DATASTREAMS = array();
$DATASTREAMS['MODS'] = array(
		'Label' => 'MODS Record',
		'mimetype' => 'text/xml',
		);
$DATASTREAMS['DC'] = array(
		'Label' => 'DC Record',
		'mimetype' => 'text/xml',
		);
$DATASTREAMS['TN'] = array(
		'mimetype' => 'image/jpeg',
		);
$DATASTREAMS['TN_LARGE'] = array(
		'mimetype' => 'image/jpeg',
		);
$DATASTREAMS['DESC'] = array(
		'Label' => 'DESC Record',
		'mimetype' => 'text/html',
		);
$DATASTREAMS['OBJ'] = array(
		'Label' => 'OBJ',
		);
$DATASTREAMS['COLLECTION_POLICY'] = array(
		'Label' => 'Colleciton Policy',
		'mimetype' => 'text/xml',
		);
$DATASTREAMS['RELS-EXT'] = array(
		'Label' => 'External Relations',
		'mimetype' => 'application/rdf+xml',
		);


	


/*
   Functions
*/
function getRelationshipRELSEXT($object) {
        $relationships = $object->relationships;
        $relsext = $relationships->get('http://islandora.ca/ontology/relsext#');
        return $relsext;
}

function getRelationshipRELSINT($object) {
        $relationships = $object->relationships;
        $relsint = $relationships->get('http://islandora.ca/ontology/relsint#');
        return $relsint;
}

function addRelationship($object,$predicate_uri,$predicate,$relation,$type) {
        $relationships = $object->relationships;
        $relationships->add($predicate_uri,$predicate,$relation,$type);
}

function getRelationship($object) {
        $relationships = $object->relationships;
        $relations = $relationships->get('http://digital.library.pitt.edu/ontology/relations#');
        return $relations;
}

function addToSite($object,$site) {
        $predicate_uri = 'http://digital.library.pitt.edu/ontology/relations#';
        $predicate = 'isMemberOfSite';
        $type = 'TRUE';
        addRelationship($object,$predicate_uri,$predicate,$site,$type);
}

function removeRelationship($object,$predicate_uri,$predicate,$relation,$type) {
        $relationships = $object->relationships;
        $relationships->remove($predicate_uri,$predicate,$relation,$type);
}

function removeFromSite($object,$site) {
        $predicate_uri = 'http://digital.library.pitt.edu/ontology/relations#';
        $predicate = 'isMemberOfSite';
        $type = 'TRUE';
        removeRelationship($object,$predicate_uri,$predicate,$site,$type);
}

function addToCollection($object,$collection) {
        $predicate_uri = 'http://islandora.ca/ontology/relsext#';
        $predicate = 'isMemberOfCollection';
        $type = 'TRUE';
        addRelationship($object,$predicate_uri,$predicate,$collection,$type);
}

function removeFromCollection($object,$collection) {
        #$predicate_uri = 'http://islandora.ca/ontology/relsext#';
        $predicate_uri = 'info:fedora/fedora-system:def/relations-external#';
        $predicate = 'isMemberOfCollection';
        $type = 'TRUE';
        removeRelationship($object,$predicate_uri,$predicate,$collection,$type);
}

/*
   Relations
*/

// isMemberOfCollection
function add_isMemberOfCollection($object,$collection) {
        $predicate_uri = 'info:fedora/fedora-system:def/relations-external#';
        $predicate = 'isMemberOfCollection';
        $type = 'TRUE';
        addRelationship($object,$predicate_uri,$predicate,$collection,$type);
}

function delete_isMemberOfCollection($object,$collection) {
        $predicate_uri = 'info:fedora/fedora-system:def/relations-external#';
        $predicate = 'isMemberOfCollection';
        $type = 'TRUE';
        removeRelationship($object,$predicate_uri,$predicate,$collection,$type);
}


// isPageOf
function add_isPageOf($object,$book) {
	$predicate_uri = 'http://islandora.ca/ontology/relsext#';
	$predicate = 'isPageOf';
	$type = 'TRUE';
	addRelationship($object,$predicate_uri,$predicate,$book,$type);
}

function delete_isPageOf($object,$book) {
        $predicate_uri = 'http://islandora.ca/ontology/relsext#';
        $predicate = 'isPageOf';
        $type = 'TRUE';
        removeRelationship($object,$predicate_uri,$predicate,$book,$type);
}


// isPageNumber
function add_isPageNumber($object,$number) {
	$predicate_uri = 'http://islandora.ca/ontology/relsext#';
        $predicate = 'isPageNumber';
        $type = 'TRUE';
        addRelationship($object,$predicate_uri,$predicate,$number,$type);
}

function delete_isPageNumber($object,$number) {
        $predicate_uri = 'http://islandora.ca/ontology/relsext#';
        $predicate = 'isPageNumber';
        $type = 'TRUE';
        removeRelationship($object,$predicate_uri,$predicate,$number,$type);
}


// isSequenceNumber
function add_isSequenceNumber($object,$number) {
	$predicate_uri = 'http://islandora.ca/ontology/relsext#';
        $predicate = 'isSequenceNumber';
        $type = 'TRUE';
        addRelationship($object,$predicate_uri,$predicate,$number,$type);
}

function delete_isSequenceNumber($object,$number) {
        $predicate_uri = 'http://islandora.ca/ontology/relsext#';
        $predicate = 'isSequenceNumber';
        $type = 'TRUE';
        removeRelationship($object,$predicate_uri,$predicate,$number,$type);
}


// isMemberOf
function add_isMemberOf($object,$book) {
        $predicate_uri = 'info:fedora/fedora-system:def/relations-external#';
        $predicate = 'isMemberOf';
        $type = 'TRUE';
        addRelationship($object,$predicate_uri,$predicate,$number,$type);
}

function delete_isMemberOf($ojbect,$book) {
        $predicate_uri = 'http://islandora.ca/ontology/relsext#';
        $predicate = 'isSection';
        $type = 'TRUE';
        removeRelationship($object,$predicate_uri,$predicate,$book,$type);
}

// isSection
function add_isSection($object,$section) {
        $predicate_uri = 'http://islandora.ca/ontology/relsext#';
        $predicate = 'isSection';
        $type = 'TRUE';
        addRelationship($object,$predicate_uri,$predicate,$number,$type);
}

function delete_isSection($ojbect,$section) {
        $predicate_uri = 'http://islandora.ca/ontology/relsext#';
        $predicate = 'isSection';
        $type = 'TRUE';
        removeRelationship($object,$predicate_uri,$predicate,$section,$type);
}

// isMemberOfSite
function add_isMemberOfSite($object,$site) {
        $predicate_uri = 'http://digital.library.pitt.edu/ontology/relations#';
        $predicate = 'isMemberOfSite';
        $type = 'TRUE';
        addRelationship($object,$predicate_uri,$predicate,$site,$type);
}

function delete_isMemberOfSite($object,$site) {
        $predicate_uri = 'http://digital.library.pitt.edu/ontology/relations#';
        $predicate = 'isMemberOfSite';
        $type = 'TRUE';
        removeRelationship($object,$predicate_uri,$predicate,$site,$type);
}

// Originally From: https://github.com/Islandora/islandora_paged_content/blob/7.x/includes/utilities.inc#L286
/**
 * Sets a relationship removing any previous relationships.
 *
 * @param object $relationships
 *   The Fedora relationships to be modified, either RELS-EXT or RELS-INT.
 * @param string $predicate_uri
 *   The predicate URI.
 * @param string $predicate
 *   The predicate.
 * @param string $object
 *   The object.
 * @param bool $literal
 *   TRUE if the object is a literal, FALSE if it is an object.
 */
function set_relationship($relationships, $predicate_uri, $predicate, $object, $literal = FALSE) {
  $relationships->remove($predicate_uri, $predicate, NULL, $literal);
  $relationships->add($predicate_uri, $predicate, $object, $literal);
}


/*
   Model
*/

function add_hasModel($object,$model) {
        $predicate_uri = 'info:fedora/fedora-system:def/model#';
        $predicate = 'hasModel';
        $type = 'TRUE';
        addRelationship($object,$predicate_uri,$predicate,$model,$type);
}

function delete_hasModel($object,$model) {
        $predicate_uri = 'info:fedora/fedora-system:def/model#';
        $predicate = 'hasModel';
        $type = 'TRUE';
        removeRelationship($object,$predicate_uri,$predicate,$model,$type);
}

function get_hasModel($object) {
	$predicate_uri = 'info:fedora/fedora-system:def/model#';
	$predicate = 'hasModel';
        $relationships = $object->relationships;
	$type = 'TRUE';
	$junk = '';
        $hasModel = $relationships->get($predicate_uri,$predicate,$junk,$type);
	return($hasModel);
}


/*
   Solr
*/

function setupSolr() {
	// Setup Solr Connection.
        $solr_url = get_config_value('solr','url');
        $solr_proxy_username = get_config_value('solr','proxy_username');
        $solr_proxy_password = get_config_value('solr','proxy_password');
        $solr = str_replace('http://', "http://$solr_proxy_username:$solr_proxy_password@", $solr_url);
	return($solr);
}

function getPagePIDs($pid) {
	$result = array();
	$pid_in = str_replace(':', '\:', $pid);

	// Setup Solr Connection.
	$solr = setupSolr();

	// Solr Query...
	$json = file_get_contents("$solr/select?q=RELS_EXT_isMemberOf_uri_s:*$pid_in&fl=PID&wt=json&indent=yes&sort=PID+asc&rows=3000");
	if($json === FALSE) {
		print "Error talking with Solr.\n";
		exit(1);
	} else {
		$obj = json_decode($json);
		$num = $obj->response->numFound;
		$result['total'] = $num;
		$pids = array();
		foreach ($obj->response->docs as $item)
		{
			array_push($pids, $item->PID);
		}
		$result['pids'] = $pids;
		return($result);
	}	
}

function getPIDsOfModel($model) {
	$result = array();
	// Setup Solr Connection.
	$solr = setupSolr();
	// Solr Query...
	$json = file_get_contents("$solr/select?q=RELS_EXT_hasModel_uri_s:*$model&fl=PID&wt=json&indent=yes&sor=PID+asc&rows=2147483647");
	if($json === FALSE) {
		print "Error Talking with Solr.\n";
		exit(1);
	} else {
		$obj = json_decode($json);
		$num = $obj->response->numFound;
		$result['total'] = $num;
		$pids = array();
		foreach ($obj->resopnse->docs as $item);
		{
			array_push($pids, $item->PID);
		}
		$result['pids'] = $pids;
		return($result);
	}
}


/* 
   Objects
*/
function dumpObject($object) {
	$relationships = $object->relationships;
	print "-------------------------------------------------------------------\n";
	print "Relationships:\n";
	$relations = print_r(getRelationship($object),true);
	print $relations;

	print "-------------------------------------------------------------------\n";
	print "RELS_EXT:\n";
	$relsext = print_r(getRelationshipRELSEXT($object),true);
	print $relsext;


	print "-------------------------------------------------------------------\n";
	print "hasModel:\n";
	$has_model = print_r(get_hasModel($object));
	print $has_model;

	#print "-------------------------------------------------------------------\n";
	#print "Whole Object:\n";
	#print_r($object);
}


function deleteObject($repository,$pid) {
    $object = $repository->getObject($pid);
    if ($object) {
	$repository->purgeObject($object);
	return(TRUE);
    } else {
	return(FALSE);
    }
}

function createObject($repository,$pid) {
    $object = $repository->constructObject($pid);
    if ($object) {
	return($object);
    } else {
	return(FALSE);
    }
}

function ingestObject($repository,$object) {
    $result = $repository->ingestObject($object);
    return($result);
}

function getNumberOfDatastreams($object) {
    $retval = $object->count();
    return($retval);
}

function getObjectParents($object) {
    $retval = $object->getParents();
    return($retval);
}

function refreshObject($object) {
    $object->refresh();
}

function getObjectID($object) {
    $id = $object->id;
    return($id);
}

function getObjectLabel($object) {
    $label = $object->label;
    return($label);
}

function getObjectModels($object) {
    $models = $object->models;
    return($models);
}

function getObjectCreatedDate($object) {
    $date = $object->createdDate;
    return($date);
}

function getObjectLastModifiedDate($object) {
    $date = $object->lastModifiedDate;
    return($date);
}

function getObjectOwner($object) {
    $owner = $object->owner;
    return($owner);
}

function getObjectState($object) {
    $state = $object->state;
    return($state);
}

function getObjectRepository($object) {
    $repository = $object->repository;
    return($repository);
}

function getObjectRelationships($object) {
    $relsext = $object->relationships;
    return($relsext);
}

/*
   Datastreams
*/
function createDatastream($object,$dsid) {
    $datastream = $object->constructDatastream($dsid);
    if ($datastream) {
	return($datastream);
    } else {
	return(FALSE);
    }
}

function ingestDatastream($object,$dsid) {
    $result = $object->ingestDatastream($dsid, $object);
    return($result);
}

function deleteDatastream($object,$dsid) {
    $result = $object->purgeDatastream($dsid);
    return($result);
}

function getDatastreams($object) {
    $datastreams = array();
    foreach ($object as $datastream) {
	array_push($datastreams,$datastream);
    }
    return($datastreams);
}

function getDatastream($object,$dsid) {
    $datastream = $object[$dsid];
    return $datastream;
}

function getDatastreamToFile($object,$dsid,$file) {
    $datastream = $object["$dsid"];
    if ($datastream) {
        if ( ! is_writable(dirname($file))) {
            print "ERROR: ".dirname($file) . " must be writable!!!\n";
            return(FALSE);
        } else {
            $result = $datastream->getContent($file);
            if (! $result) {
                print "Get datastream failed.\n";
            } else {
                return(TRUE);
            }
        }
    } else {
        print "ERROR: Datastream: ".$dsid." does not exist.\n";
        return(FALSE);
    }
}

function getDatastreamID($datastream) {
    $id = $datastream->id;
    return($id);
}

function getDatastreamLabel($datastream) {
    $label = $datastream->label;
    return($label);
}

function getDatastreamCreatedDate($datastream) {
    $create_date = $datastream->createdDate;
    return($create_date);
}

function getDatastreamMimetype($datastream) {
    $mime_type = $datastream->mimetype;
    return($mime_type);
}

function getDatastreamSize($datastream) {
    $size = $datastream->size;
    return($size);
}

function isDatastreamVersionable($datastream) {
    $versionable = $datastream->versionable;
    return($versionable);
}

function getDatastreamControlGroup($datastream) {
    $control_group = $datastream->controlGroup;
    return($control_group);
}

function getDatastreamChecksum($datastream) {
    $checksum = $datastream->checksum;
    return($checksum);
}

function getDatastreamChecksumType($datastream) {
    $checksum_type = $datastream->checksumType;
    return($checksum_type);
}

function getMimetypeToFileExtension($mimetype) {
    $extensions = array(
	'text/xml' 		=> 'xml',
	'text/html'		=> 'html',
	'image/jpeg'		=> 'jpg',
	'image/jpg'		=> 'jpg',
	'image/tiff'		=> 'tif',
	'image/jp2'		=> 'jp2',
	'image/png'		=> 'png',
	'audio/mpeg'		=> 'mp3',
	'application/rdf+xml'	=> 'xml',
	'application/xml'	=> 'xml',
	'video/mp4'		=> 'mp4',
	'video/x-matroska'	=> 'mkv',
	'application/pdf'	=> 'pdf',
	'audio/x-wav'		=> 'wav',
	'text/plain'		=> 'txt'
    );
    return $extensions[$mimetype];
}

/* 
   Setup
*/

function getRepositoryConnection() {
    $fedora_username = get_config_value('fedora','username');
    $fedora_password = get_config_value('fedora','password');
    $fedora_url = get_config_value('fedora','url');
    $connection = new RepositoryConnection($fedora_url, $fedora_username, $fedora_password);
    if ($connection) {
	return $connection;
    } else {
	return(FALSE);
    }
}

function getRepository($connection) {
    $api = new FedoraApi($connection);
    if ($api) {
        $repository = new FedoraRepository($api, new simpleCache());
        if ($repository) {
	    return($repository);
        } else {
            return(FALSE);
        }
    } else {
        return(FALSE);
    }
}

function getAPIA($repository) {
    $api_a = $repository->api->a;
    if ($api_a) {
	return($api_a);
    } else {
	return(FALSE);
    }
}

function getAPIM($repository) {
    $api_m = $repository->api->m;
    if ($api_m) {
        return($api_m);
    } else {
        return(FALSE);
    }
}

function getRI($repository) {
    $ri = $repository->ri;
    if ($ri) {
	return($ri);
    } else {
	return(FALSE);
    }
}

function getObject($repository,$pid) {
    $object = FALSE;
    try {
        $object = $repository->getObject($pid);
    }
    catch (Exception $e) {
        $object = FALSE;
    }
    return($object);
}


    

/*
   Utilities
*/


/*
   get_config_value
*/
function get_config_value($section,$key)
{
    if ( file_exists('/opt/islandora_cron/uls-tuque.ini') )
    {
        $ini_array = parse_ini_file('/opt/islandora_cron/uls-tuque.ini', true);
        if (isset($ini_array[$section][$key]))
        {
            $value = $ini_array[$section][$key];
            return ($value);
        } else {
            return ("");
        }
    } else {
        return(0);
    }
}

?>
