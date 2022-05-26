<?php
$dd = '{"data":[{"id_customer_address_city":"3","name":"ABAK"},{"id_customer_address_city":"79","name":"EKET"},{"id_customer_address_city":"88","name":"ETINAN"},{"id_customer_address_city":"134","name":"IKOT ABASSI"},{"id_customer_address_city":"135","name":"IKOT EKPENE"},{"id_customer_address_city":"136","name":"IKOT OSURA"},{"id_customer_address_city":"215","name":"MKPAT ENIN"},{"id_customer_address_city":"262","name":"ORON"},{"id_customer_address_city":"276","name":"QUA IBOH TERMINAL \/ QIT"},{"id_customer_address_city":"2880","name":"Uyo"}]}';

var_dump(json_decode($dd,TRUE));