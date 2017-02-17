<?php

class Idev_OneStepCheckout_Model_Source_Geoip
{
    public function toOptionArray()
    {
        $options = array(
            array('label'=>'Please choose GeoIp detection method', 'value'=>''),
            array('label'=>'GeoIP2 database', 'value'=>'geoip2_db'),
            array('label'=>'GeoIP2 online', 'value'=>'geoip2_online'),
            array('label'=>'Pear Net/GeoIp (legacy)', 'value'=>'pear_geoip'),
            array('label'=>'Apache mod_geoip', 'value'=>'mod_geoip'),
            array('label'=>'Pecl geoip', 'value'=>'pecl_geoip')
        );

        return $options;
    }
}
