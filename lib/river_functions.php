<?php

function elgg_list_entities_including(array $options = array()) {
	global $autofeed;
	$autofeed = true;

	$defaults = array(
		'offset'     => (int) max(get_input('offset', 0), 0),
		'limit'      => (int) max(get_input('limit', 20), 0),
		'pagination' => TRUE,
		'list_class' => 'elgg-river',
		'type' => 'object',
		'subtype' => '',
	);

	$options = array_merge($defaults, $options);

	$owner = get_entity($options['subject_guid']);

	$options['owner_guid'] = 0;

	$options['count'] = TRUE;
	$count = get_entities_including($options['including'], $options['type'], $options['subtype'], $options['owner_guid'], "", $options['limit'], $options['offset'], true);

	$options['count'] = FALSE;
	$items = get_entities_including($options['including'], $options['type'], $options['subtype'], $options['owner_guid'], "", $options['limit'], $options['offset']);

	$options['count'] = $count;
	$options['items'] = $items;
	return elgg_view('page/components/list', $options);
}

function elgg_list_river_including(array $options = array()) {
	global $autofeed;
	$autofeed = true;

	$defaults = array(
		'offset'     => (int) max(get_input('offset', 0), 0),
		'limit'      => (int) max(get_input('limit', 20), 0),
		'pagination' => TRUE,
		'list_class' => 'elgg-river',
	);

	$options = array_merge($defaults, $options);

	$options['count'] = TRUE;
	$count = elgg_get_river_including($options);

	$options['count'] = FALSE;
	$items = elgg_get_river_including($options);

	$options['count'] = $count;
	$options['items'] = $items;
	return elgg_view('page/components/list', $options);
}


function elgg_get_river_including(array $options = array()) {
	global $CONFIG;

	$defaults = array(
		'ids'                  => ELGG_ENTITIES_ANY_VALUE,

		'subject_guids'	       => ELGG_ENTITIES_ANY_VALUE,
		'object_guids'         => ELGG_ENTITIES_ANY_VALUE,
		'annotation_ids'       => ELGG_ENTITIES_ANY_VALUE,
		'action_types'         => ELGG_ENTITIES_ANY_VALUE,

		'relationship'         => NULL,
		'relationship_guid'    => NULL,
		'inverse_relationship' => FALSE,

		'types'	               => ELGG_ENTITIES_ANY_VALUE,
		'subtypes'             => ELGG_ENTITIES_ANY_VALUE,
		'type_subtype_pairs'   => ELGG_ENTITIES_ANY_VALUE,

		'posted_time_lower'	   => ELGG_ENTITIES_ANY_VALUE,
		'posted_time_upper'	   => ELGG_ENTITIES_ANY_VALUE,

		'limit'                => 20,
		'offset'               => 0,
		'count'                => FALSE,

		'order_by'             => 'rv.posted desc',
		'group_by'             => ELGG_ENTITIES_ANY_VALUE,

		'wheres'               => array(),
		'joins'                => array(),
	);

	$options = array_merge($defaults, $options);

	$singulars = array('id', 'subject_guid', 'object_guid', 'annotation_id', 'action_type', 'type', 'subtype');
	$options = elgg_normalise_plural_options_array($options, $singulars);

	$wheres = $options['wheres'];

	$wheres[] = elgg_get_guid_based_where_sql('rv.id', $options['ids']);
	$wheres[] = elgg_get_guid_based_where_sql('rv.subject_guid', $options['subject_guids']);
	$wheres[] = elgg_get_guid_based_where_sql('rv.object_guid', $options['object_guids']);
	$wheres[] = elgg_get_guid_based_where_sql('rv.annotation_id', $options['annotation_ids']);
	$wheres[] = elgg_river_get_action_where_sql($options['action_types']);
	$wheres[] = elgg_get_river_type_subtype_where_sql('rv', $options['types'],
		$options['subtypes'], $options['type_subtype_pairs']);

	if ($options['posted_time_lower'] && is_int($options['posted_time_lower'])) {
		$wheres[] = "rv.posted >= {$options['posted_time_lower']}";
	}

	if ($options['posted_time_upper'] && is_int($options['posted_time_upper'])) {
		$wheres[] = "rv.posted <= {$options['posted_time_upper']}";
	}

	$joins = $options['joins'];

	if ($options['relationship_guid']) {
		$clauses = elgg_get_entity_relationship_where_sql(
				'rv.subject_guid',
				$options['relationship'],
				$options['relationship_guid'],
				$options['inverse_relationship']);
		if ($clauses) {
			$wheres = array_merge($wheres, $clauses['wheres']);
			$joins = array_merge($joins, $clauses['joins']);
		}
	}

	// remove identical where clauses
	$wheres = array_unique($wheres);

	// see if any functions failed
	// remove empty strings on successful functions
	foreach ($wheres as $i => $where) {
		if ($where === FALSE) {
			return FALSE;
		} elseif (empty($where)) {
			unset($wheres[$i]);
		}
	}

	if (!$options['including']) {
		if (!$options['count']) {
			$query = "SELECT DISTINCT rv.* FROM {$CONFIG->dbprefix}river rv ";
		} else {
			$query = "SELECT count(DISTINCT rv.id) as total FROM {$CONFIG->dbprefix}river rv ";
		}
	} else {
		if (!$options['count']) {
			$query = "SELECT DISTINCT rv.* FROM {$CONFIG->dbprefix}river rv, entities e, objects_entity o ";
		} else {
			$query = "SELECT count(DISTINCT rv.id) as total FROM {$CONFIG->dbprefix}river rv, entities e, objects_entity o ";
		}
	}

	// add joins
	foreach ($joins as $j) {
		$query .= " $j ";
	}

	// add wheres
	$query .= ' WHERE ';

	if (!$options['including']) {
		foreach ($wheres as $w) {
			$query .= " $w AND ";
		}
		$query .= elgg_river_get_access_sql();
	} else {
		if (count($options['including']) == 1) {
			foreach ($where as $w)
				$query .= " $w and ";
			$query .= elgg_river_get_access_sql();
			$query .= " AND e.guid=o.guid AND rv.object_guid=o.guid AND o.description LIKE '%{$options['including'][0]}%' ";
		} else {
			$i = 0;
			foreach ( $options['including'] as $include) {
				$i++;
				foreach ($where as $w)
					$query .= " $w and ";
				$query .= elgg_river_get_access_sql();
				if ($i < count($options['including'])) {
					$query .= " AND e.guid=o.guid AND rv.object_guid=o.guid AND o.description LIKE '%$include%' OR ";
				} else {
					$query .= " AND e.guid=o.guid AND rv.object_guid=o.guid AND o.description LIKE '%$include%' ";
				}
			}
		}
	}

	if (!$options['count']) {
		$options['group_by'] = sanitise_string($options['group_by']);
		if ($options['group_by']) {
			$query .= " GROUP BY {$options['group_by']}";
		}

		$options['order_by'] = sanitise_string($options['order_by']);
		$query .= " ORDER BY {$options['order_by']}";

		if ($options['limit']) {
			$limit = sanitise_int($options['limit']);
			$offset = sanitise_int($options['offset'], false);
			$query .= " LIMIT $offset, $limit";
		}

		$river_items = get_data($query, 'elgg_row_to_elgg_river_item');

		return $river_items;
	} else {
		$total = get_data_row($query);
		return (int)$total->total;
	}
}









function get_entities_including($including,$type = "", $subtype = "", $owner_guid = 0, $order_by = "", $limit = 10, $offset = 0, $count = false, $site_guid = 0, $container_guid = null, $timelower = 0, $timeupper = 0)
{
	global $CONFIG;
	
	if ($subtype === false || $subtype === null || $subtype === 0)
		return false;
	
	if ($order_by == "") $order_by = "e.time_created desc";
	$order_by = sanitise_string($order_by);
	//$including = sanitise_string_special($including, '#');
	$limit = (int)$limit;
	$offset = (int)$offset;
	$site_guid = (int) $site_guid;
	$timelower = (int) $timelower;
	$timeupper = (int) $timeupper;
	if ($site_guid == 0)
		$site_guid = $CONFIG->site_guid;

	$where = array();
	
	if (is_array($subtype)) {
		$tempwhere = "";
		if (sizeof($subtype))
		foreach($subtype as $typekey => $subtypearray) {
			foreach($subtypearray as $subtypeval) {
				$typekey = sanitise_string($typekey);
				if (!empty($subtypeval)) {
					$subtypeval = (int) get_subtype_id($typekey, $subtypeval);
				} else {
					$subtypeval = 0;
				}
				if (!empty($tempwhere)) $tempwhere .= " or ";
				$tempwhere .= "(e.type = '{$typekey}' and e.subtype = {$subtypeval})";
			}
		}
		if (!empty($tempwhere)) $where[] = "({$tempwhere})";
		
		
	} else {
	
		$type = sanitise_string($type);
		if ($subtype !== "")
			$subtype = get_subtype_id($type, $subtype);
		
		if ($type != "")
			$where[] = "e.type='$type'";
		if ($subtype!=="")
			$where[] = "e.subtype=$subtype";
			
	}
	
	if ($owner_guid != "") {
		if (!is_array($owner_guid)) {
			$owner_array = array($owner_guid);
			$owner_guid = (int) $owner_guid;
		//	$where[] = "owner_guid = '$owner_guid'";
		} else if (sizeof($owner_guid) > 0) {
			$owner_array = array_map('sanitise_int', $owner_guid);
			// Cast every element to the owner_guid array to int
		//	$owner_guid = array_map("sanitise_int", $owner_guid);
		//	$owner_guid = implode(",",$owner_guid);
		//	$where[] = "owner_guid in ({$owner_guid})";
		}
		if (is_null($container_guid)) {
			$container_guid = $owner_array;
		}
	}
	if ($site_guid > 0)
		$where[] = "e.site_guid = {$site_guid}";

	if (!is_null($container_guid)) {
		if (is_array($container_guid)) {
			foreach($container_guid as $key => $val) $container_guid[$key] = (int) $val;
			$where[] = "e.container_guid in (" . implode(",",$container_guid) . ")";
		} else {
			$container_guid = (int) $container_guid;
			$where[] = "e.container_guid = {$container_guid}";
		}
	}
	if ($timelower)
		$where[] = "e.time_created >= {$timelower}";
	if ($timeupper)
		$where[] = "e.time_created <= {$timeupper}";
		
	if (!$count) {
		$query = "SELECT e.*,o.* from {$CONFIG->dbprefix}entities e, {$CONFIG->dbprefix}objects_entity o where ";
	} else {
		$query = "SELECT count(e.guid) as total from {$CONFIG->dbprefix}entities e, {$CONFIG->dbprefix}objects_entity o where ";
	}

	if (count($including) == 1) {
		foreach ($where as $w)
			$query .= " $w and ";
		$query .= get_access_sql_suffix(); // Add access controls
		$query .= " AND e.guid=o.guid AND o.description LIKE '%$including[0]%' ";
	} else {
		$i = 0;
		foreach ( $including as $include) {
			$i++;
			foreach ($where as $w)
				$query .= " $w and ";
			$query .= get_access_sql_suffix(); // Add access controls
			if ($i < count($including)) {
				$query .= " AND e.guid=o.guid AND o.description LIKE '%$include%' OR ";
			} else {
				$query .= " AND e.guid=o.guid AND o.description LIKE '%$include%' ";
			}
		}
	}

	if (!$count) {
		$query .= " order by $order_by";
		if ($limit) $query .= " limit $offset, $limit"; // Add order and limit
		$dt = get_data($query, "entity_row_to_elggstar");
		return $dt;
	} else {
		$total = get_data_row($query);
		return $total->total;
	}
	
	/*

	SELECT e.*,o.* from elggentities e, elggobjects_entity o 
	where  e.type='object' and  e.subtype=8 and  e.site_guid = 1 and ( (1 = 1)  and e.enabled='yes') AND e.guid=o.guid AND o.description LIKE '%admin%'
	order by time_created desc limit 0, 10
		
	* */
}

