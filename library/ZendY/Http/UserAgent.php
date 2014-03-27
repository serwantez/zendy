<?php

namespace ZendY\Http;

class UserAgent extends \Zend_Http_UserAgent {

    public function getOperatingSystem() {
        $features = $this->getDevice()->getAllFeatures();
        $system = $features['comment']['detail'][0];
        return $system;
    }

    public function getOperatingSystemDetails() {
        $features = $this->getDevice()->getAllFeatures();
        $systemDetails = $features['comment']['detail'][1];
        if (isset($features['comment']['detail'][2]))
            $systemDetails .= ', ' . $features['comment']['detail'][2];
        if (isset($features['comment']['detail'][3]))
            $systemDetails .= ', ' . $features['comment']['detail'][3];
        if (isset($features['comment']['detail'][4]))
            $systemDetails .= ', ' . $features['comment']['detail'][4];
        return $systemDetails;
    }

}
