<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Electro2 extends Model_Items_Abstract_Item implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Komplexes Elektronisches Bauteil',
			'icon' => 'electro2',
			'description' => 'Dieser elektrische Schaltkreis ist mit den neuesten Bleeding Edge Komponenten der Computertechnik bestückt: Ein OctaCore Superprozessor mit GigaThreading Technologie, 1024 simultan verfügbare Interlaced PCI Lanes, Quattro-Channel-RAM-Anbindung und Support für die neuesten CPU-Befehlssätze FCK, CNT, BLLS, SHT und ASS.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
	);

	protected static $weight = 5;
}	