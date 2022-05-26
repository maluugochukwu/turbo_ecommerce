<?php
$dd = '{"data":[{"id_customer_address_city":"100","name":"GOMBI"},{"id_customer_address_city":"107","name":"HONG"},{"id_customer_address_city":"208","name":"MAYO BELWA"},{"id_customer_address_city":"217","name":"MUBI"},{"id_customer_address_city":"222","name":"NGURORE"},{"id_customer_address_city":"227","name":"NUMAN"},{"id_customer_address_city":"294","name":"SONG"},{"id_customer_address_city":"2679","name":"Yola"},{"id_customer_address_city":"321","name":"YOLA-CENTRAL LOCATIONS"}]}';

var_dump(json_decode($dd,TRUE));